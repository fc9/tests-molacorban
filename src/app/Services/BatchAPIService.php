<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PurchaseStatusEnum;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Phpro\ApiProblem\Exception\ApiProblemException;
use Phpro\ApiProblem\Http\BadRequestProblem;

class BatchAPIService
{
    /**
     * Campos validos
     *
     * @var array
     */
    protected static array $fields = [
        "cod_fornecedor",
        "cod_prod",
        "cliente",
        "documento",
        "nome_prod",
        "nome_categoria",
        "nome_fornecedor",
        "valor_original",
        "data_compra",
        "valor_desconto",
        "valor_final",
        "data_pgto",
        "data_devolucao",
        "status_situacao",
        "status_pgto",
        "taxa_aplicada",
        "taxa_original"
    ];

    /**
     * Get Records
     *
     * @param Builder $query
     * @param array $params
     * @return array
     * @throws ApiProblemException
     */
    public static function dataQuery(Builder $query, array $params): array
    {
        $query = self::fields($query, Arr::get($params, 'fields', '') ?? '');
        $query = self::sort($query, Arr::get($params, 'sort', '') ?? '');
        $query = self::filter($query, Arr::get($params, 'filter', '') ?? '');
        $query = $query->paginate(Arr::get($params, 'per_page', 20))->withQueryString();

        $arr = $query->toArray();
        $arr['data'] = array_map(function ($item) {
            $item->valor_original = brl2decimal($item->valor_original ?? 0, 2);
            $item->valor_desconto = brl2decimal($item->valor_desconto ?? 0, 2);
            $item->valor_final = brl2decimal($item->valor_final ?? 0, 2);
            $item->taxa_aplicada = brl2decimal($item->taxa_aplicada ?? 0, 2);
            $item->taxa_original = brl2decimal($item->taxa_original ?? 0, 2);
            $item->status_situacao = PurchaseStatusEnum::fromValue(intval($item->status_situacao))->description;
            $item->status_pgto = PaymentStatusEnum::fromValue(intval($item->status_pgto))->description;
            return $item;
        }, $arr['data']);

        return $arr;
    }

    /**
     * Limitar campos retornados
     * ex.: ?fields=cliente,document,valor_original,valor_final
     *
     * @param Builder $query
     * @param string $fields
     * @return Builder
     * @throws ApiProblemException
     */
    protected static function fields(Builder $query, string $fields): Builder
    {
        if ($fields !== '') {
            $fields = array_reduce(explode(',', (string)$fields), function ($carry, $item) {
                $item = strtolower($item);
                if (!in_array($item, self::$fields)) {
                    throw new ApiProblemException(
                        new BadRequestProblem("'{$item}' is not a valid field.")
                    );
                }
                $carry[] = $item;
                return $carry;
            }, []);
        } else {
            $fields = self::$fields;
        }

        return $query->select($fields);
    }

    /**
     * Classifica o resultado por uma ou varias colunas
     * Ex.: ?sort=cliente,valor_original_desc,valor_final_ASC,data_pgto
     *
     * @param Builder $query
     * @param string $sort
     * @return Builder
     * @throws ApiProblemException
     */
    protected static function sort(Builder $query, string $sort): Builder
    {
        if (!$sort) return $query;

        $sorts = array_reduce(explode(',', $sort), function ($carry, $item) {
            $column = strtolower(str_replace(['_desc', '_asc'], '', trim(strtolower($item))));

            if (!in_array($column, self::$fields)) {
                throw new ApiProblemException(
                    new BadRequestProblem("'{$column}' is not a valid sorter.")
                );
            }

            $carry[] = [
                'column' => trim($column),
                'direction' => str_ends_with(strtolower($item), 'desc') ? 'DESC' : 'ASC',
            ];

            return $carry;
        }, []);

        return $query->when($sort, function ($query) use ($sorts) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['column'], $sort['direction']);
            }
        });
    }

    /**
     * Multiplos filtros
     * Ex.: ?filter=cliente:roberto%20carlos,nome_categoria:titulocap
     *
     * @param Builder $query
     * @param string $filter
     * @return Builder
     * @throws ApiProblemException
     */
    protected static function filter(Builder $query, string $filter): Builder
    {
        if (!$filter) return $query;

        $filters = array_reduce(explode(',', (string)$filter), function ($carry, $item) {

            try {
                list($key, $value) = explode(':', $item, 2);
            } catch (\Throwable $e) {
                throw new ApiProblemException(
                    new BadRequestProblem('Filter with problem.')
                );
            }

            $key = trim(strtolower($key));
            if (!in_array($key, self::$fields)) {
                throw new ApiProblemException(
                    new BadRequestProblem("'{$key}' is not a valid filter.")
                );
            }

            if ($key === 'cod_fornecedor'
                || $key === 'cod_prod') {
                $carry[$key] = intval($value);
            } elseif ($key === 'data_compra'
                || $key === 'data_pgto'
                || $key === 'data_devolucao') {
                $carry[$key] = $value === '0000/00/00' ? null : $value;
            } elseif ($key === 'valor_original'
                || $key === 'valor_desconto'
                || $key === 'valor_final'
                || $key === 'taxa_aplicada'
                || $key === 'taxa_original') {
                $carry[$key] = brl2decimal($value, 2);
            } elseif ($key === 'cliente'
                || $key === 'documento'
                || $key === 'nome_prod'
                || $key === 'nome_categoria'
                || $key === 'nome_fornecedor'
                || $key === 'status_situacao'
                || $key === 'status_pgto') {
                $carry[$key] = $value;
            }
            return $carry;

        }, []);

        $validator = Validator::make($filters, [
            "cod_fornecedor" => 'integer|min:1',
            "cod_prod" => 'integer|min:1',
            "cliente" => 'string|max:45',
            "documento" => 'string|max:16',
            "nome_prod" => 'string|max:45',
            "nome_categoria" => 'string|max:45|exists:categories,name',
            "nome_fornecedor" => 'string|max:45',
            "valor_original" => 'regex:/^\d+(\.\d{1,2})?$/',
            "data_compra" => 'date_format:Y/m/d',
            "valor_desconto" => 'regex:/^\d+(\.\d{1,2})?$/',
            "valor_final" => 'regex:/^\d+(\.\d{1,2})?$/',
            "data_pgto" => 'date_format:Y/m/d',
            "data_devolucao" => 'date_format:Y/m/d',
            "status_situacao" => 'string|max:45|in:paga,estorno,cancelada',
            "status_pgto" => 'string|max:45|in:efetivado,removido',
            "taxa_aplicada" => 'regex:/^\d+(\.\d{1,2})?$/',
            "taxa_original" => 'regex:/^\d+(\.\d{1,2})?$/',
        ]);

        if ($validator->fails()) {
            throw new ApiProblemException(
                new BadRequestProblem($validator->errors())
            );
        }

        return $query->when($filter, function ($query) use ($filters) {
            foreach ($filters as $column => $value) {
                if (in_array($column, [
                    'cod_fornecedor',
                    'cod_prod',
                    'data_compra',
                    'data_pgto',
                    'data_devolucao',
                    'valor_original',
                    'valor_desconto',
                    'valor_final',
                    'taxa_aplicada',
                    'taxa_original'])) {
                    $query->where($column, $value);
                } elseif ($column === 'status_situacao') {
                    $query->where($column, PurchaseStatusEnum::fromKey(strtoupper($value))->value);
                } elseif ($column === 'status_pgto') {
                    $query->where($column, PaymentStatusEnum::fromKey(strtoupper($value))->value);
                } else {
                    # cliente
                    # documento
                    # nome_prod
                    # nome_categoria
                    # nome_fornecedor
                    $query->where($column, 'LIKE', "%{$value}%");
                }
            }
        });
    }
}