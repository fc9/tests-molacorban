<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "cod_fornecedor" => 'required|integer|min:1',
            "cod_prod" => 'required|integer|min:1',
            "cliente" => 'required|string|max:45',
            "documento" => 'required|string|max:16|min:6',
            "nome_prod" => 'required|string|max:45',
            "nome_categoria" => 'required|string|max:45',
            "nome_fornecedor" => 'required|string|max:45',
            "valor_original" => 'required|numeric|min:0',
            "data_compra" => 'required|date_format:Y/m/d',
            "valor_desconto" => 'required|numeric|min:0',
            "valor_final" => 'required|numeric|min:0',
            "data_pgto" => 'nullable|date_format:Y/m/d',
            "data_devolucao" => 'nullable|date_format:Y/m/d',
            "status_situacao" => 'required|string|max:45|in:PAGA,ESTORNO,CANCELADA',
            "status_pgto" => 'required|string|max:45|in:EFETIVADO,REMOVIDO',
            "taxa_aplicada" => 'required|numeric|min:0',
            "taxa_original" => 'required|numeric|min:0',
        ];
    }
}
