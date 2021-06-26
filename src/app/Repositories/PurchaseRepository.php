<?php

namespace App\Repositories;

use App\Enums\{PaymentStatusEnum, PurchaseStatusEnum};
use App\Models\{Category, Customer, Payment, Product, Purchase, Supplier};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PurchaseRepository extends BaseRepository
{
    /**
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * @param array $payload
     * @return \Illuminate\Database\Query\Builder
     */
    public function show(array $payload)
    {
        $products = DB::table('products as pro')
            ->select('pro.id as producto_id',
                'pro.code as cod_prod',
                'pro.name as nome_prod',
                'cat.name as nome_categoria'
            )
            ->join('categories as cat', 'pro.category_id', '=', 'cat.id');

        $querySub = DB::table('purchases as pur')
            ->select('pur.id',
                'sup.code as cod_fornecedor',
                'pro.cod_prod',
                'cus.name as cliente',
                'cus.document as documento',
                'pro.nome_prod',
                'pro.nome_categoria',
                'sup.name as nome_fornecedor',
                'pur.value as valor_original',
                'pur.date as data_compra',
                'pur.returned_at as data_devolucao',
                'pur.status as status_situacao',
                'pur.rate as taxa_original')
            ->join('suppliers as sup', 'pur.supplier_id', '=', 'sup.id')
            ->joinSub($products, 'pro', function ($join) {
                $join->on('pur.product_id', '=', 'pro.producto_id');
            })
            ->join('customers as cus', 'pur.customer_id', '=', 'cus.id')
            ->where('pur.batch_uuid', $payload['batch_uuid']);

        $purchases = DB::table('payments as pay')
            ->select('cod_fornecedor',
                'cod_prod',
                'cliente',
                'documento',
                'nome_prod',
                'nome_categoria',
                'nome_fornecedor',
                'valor_original',
                'data_compra',
                'pay.discount as valor_desconto',
                'pay.value as valor_final',
                'pay.date as data_pgto',
                'data_devolucao',
                'status_situacao',
                'pay.status as status_pgto',
                'pay.rate as taxa_aplicada',
                'taxa_original')
            ->joinSub($querySub, 'p', function ($join) {
                $join->on('p.id', '=', 'pay.purchase_id');
            });

        return DB::table($purchases, 'purchases');
    }

    /**
     * @param array $payload
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumKeyException
     */
    public function store(array $payload)
    {
        $supplier = Supplier::updateOrCreate(
            ["code" => Arr::get($payload, 'cod_fornecedor')],
            [
                'code' => Arr::get($payload, 'cod_fornecedor'),
                'name' => Arr::get($payload, 'nome_fornecedor')
            ]
        );

        $category = Category::updateOrCreate(
            ["name" => Arr::get($payload, 'nome_categoria')],
            ['name' => Arr::get($payload, 'nome_categoria')]
        );

        $product = Product::updateOrCreate(
            [
                'category_id' => $category->id,
                'code' => Arr::get($payload, 'cod_prod'),
            ],
            [
                'category_id' => $category->id,
                'code' => Arr::get($payload, 'cod_prod'),
                'name' => Arr::get($payload, 'nome_prod')
            ]
        );

        $customer = Customer::updateOrCreate(
            [
                'document' => Arr::get($payload, 'documento'),
            ],
            [
                'document' => Arr::get($payload, 'documento'),
                'name' => Arr::get($payload, 'cliente')
            ]
        );

        $purchase = Purchase::create([
            'batch_uuid' => Arr::get($payload, 'batch_uuid'),
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'value' => Arr::get($payload, 'valor_original'),
            'rate' => Arr::get($payload, 'taxa_original'),
            'status' => PurchaseStatusEnum::fromKey(Arr::get($payload, 'status_situacao')),
            'date' => Arr::get($payload, 'data_compra'),
            'returned_at' => Arr::get($payload, 'data_devolucao')
        ]);

        if (Arr::get($payload, 'data_pgto') !== null) {
            Payment::create([
                'purchase_id' => $purchase->id,
                'discount' => Arr::get($payload, 'valor_desconto'),
                'value' => Arr::get($payload, 'valor_final'),
                'rate' => Arr::get($payload, 'taxa_aplicada'),
                'status' => PaymentStatusEnum::fromKey(Arr::get($payload, 'status_pgto')),
                'date' => Arr::get($payload, 'data_pgto'),
            ]);
        }
    }
}