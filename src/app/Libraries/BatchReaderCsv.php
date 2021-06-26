<?php

namespace App\Libraries;

class BatchReaderCsv extends ReaderCsv
{
    /**
     * Reader file CSV.
     *
     * @param string $path
     * @param string $delimiter
     * @return array
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     */
    public static function fromPath(string $path, string $delimiter = ';'): array
    {
        $records = [];

        foreach (parent::fromPath($path, $delimiter) as $index => $record) {
            $records[$index] = self::normalize($record);
        }

        return $records;
    }

    public static function normalize(array $record): array
    {
        $records = [];

        foreach ($record as $column => $value) {
            $column = strtolower($column);

            if ($column === 'cod_fornecedor') $value = is_numeric($value) ? intval($value) : $value;
            elseif ($column === 'cod_prod') $value = is_numeric($value) ? intval($value) : $value;
            #elseif ($column === 'cliente') $value = $value;
            #elseif ($column === 'documento') $value = $value;
            #elseif ($column === 'nome_prod') $value = $value;
            #elseif ($column === 'nome_categoria') $value = $value;
            #elseif ($column === 'nome_fornecedor') $value = $value;
            elseif ($column === 'valor_original') $value = is_numeric($value) ? (float) number_format($value, 2) : $value;
            elseif ($column === 'data_compra') $value = $value === '0000/00/00' ? null : $value;
            elseif ($column === 'valor_desconto') $value = is_numeric($value) ? (float) number_format($value, 2) : $value;
            elseif ($column === 'valor_final') $value = is_numeric($value) ? (float) number_format($value, 2) : $value;
            elseif ($column === 'data_pgto') $value = $value === '0000/00/00' ? null : $value;
            elseif ($column === 'data_devolucao') $value = $value === '0000/00/00' ? null : $value;
            #elseif ($column === 'status_situacao') $value = $value;
            #elseif ($column === 'status_pgto') $value = $value;
            elseif ($column === 'taxa_aplicada') $value = is_numeric($value) ? (float) number_format($value, 2) : $value;
            elseif ($column === 'taxa_original') $value = is_numeric($value) ? (float) number_format($value, 2) : $value;

            $records[$column] = $value;
        }

        return $records;
    }

}