<?php

namespace Tests\Unit\App\Http\Requests;

use App\Http\Requests\PurchaseRequest;
use App\Libraries\BatchReaderCsv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Http\Requests\PurchaseRequest */
    private $rules;

    /** @var \Illuminate\Validation\Validator */
    private $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rules = (new PurchaseRequest)->rules();
        $this->validator = app()->get('validator');
    }

    public function validationProvider()
    {
        /* Poderia usar faker para gerar os casos */
        /* $faker = Factory::create(Factory::DEFAULT_LOCALE); */

        return [
            'todo_os_campos_sao_requiridos' => [
                'passed' => true,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'campos_numericos_com_string' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '877B',
                    "cod_prod" => 'X-333',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => 'R$1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '$0.05',
                    "valor_final" => 'EU1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12$',
                    "taxa_original" => '0.15R$',
                ])
            ],
            'colunas_string_com_mais_de_45_digitos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "documento" => '55588822244',
                    "nome_prod" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "nome_categoria" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "nome_fornecedor" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "status_pgto" => 'String muito longa, excedendo o limite de 45 digitos.',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'status_situacao_invalido' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGADO',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'status_pgto_invalido' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'INDEFINIDO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'documento_com_mais_de_16_digitos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '555.888.222-44/0001',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'datas_invalidas' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/13/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/32',
                    "data_devolucao" => '0000/00/01',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'valores_negativos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '-1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '-0.05',
                    "valor_final" => '-1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '-0.12',
                    "taxa_original" => '-0.15',
                ])
            ],
            'valores_com_formatos_invalidos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535,00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '4.045.05',
                    "valor_final" => '1.535,00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => 'E0+12',
                    "taxa_original" => '0.15',
                ])
            ],
            'data_devolucao_informada' => [
                'passed' => true,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '2022/02/17',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'documento_muito_pequeno' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '2244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'codigos_negativos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '-543',
                    "cod_prod" => '-12312232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_nomes' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => '',
                    "documento" => '55588822244',
                    "nome_prod" => '',
                    "nome_categoria" => '',
                    "nome_fornecedor" => '',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'falta_requeridos' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_original" => '0.15',
                ])
            ],
            'formatos_incorretos_de_data' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '15/02/2022',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022-01-23',
                    "data_devolucao" => '0000:00:00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_cod_fornecedor' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    #"cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_cod_prod' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    #"cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_cliente' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    #"cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_documento' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    #"documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_nome_prod' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    #"nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_nome_categoria' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    #"nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_nome_fornecedor' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    #"nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_valor_original' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    #"valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_data_compra' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    #"data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_valor_desconto' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    #"valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_valor_final' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    #"valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_data_pgto' => [
                'passed' => true,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    #"data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_data_devolucao' => [
                'passed' => true,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    #"data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_status_situacao' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    #"status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_status_pgto' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    #"status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_taxa_aplicada' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    #"taxa_aplicada" => '0.12',
                    "taxa_original" => '0.15',
                ])
            ],
            'sem_taxa_original' => [
                'passed' => false,
                'data' => BatchReaderCsv::normalize([
                    "cod_fornecedor" => '776223',
                    "cod_prod" => '232',
                    "cliente" => 'CARLOS LIMA',
                    "documento" => '55588822244',
                    "nome_prod" => 'EMPRESTIMO PESSOAL',
                    "nome_categoria" => 'EMP',
                    "nome_fornecedor" => 'SANTANDER',
                    "valor_original" => '1535.00',
                    "data_compra" => '2022/01/21',
                    "valor_desconto" => '0.05',
                    "valor_final" => '1535.00',
                    "data_pgto" => '2022/01/23',
                    "data_devolucao" => '0000/00/00',
                    "status_situacao" => 'PAGA',
                    "status_pgto" => 'EFETIVADO',
                    "taxa_aplicada" => '0.12',
                    #"taxa_original" => '0.15',
                ])
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validationProvider
     * @param bool $shouldPass
     * @param array $mockedRequestData
     */
    public function validation_results_as_expected(bool $shouldPass, array $mockedRequestData)
    {
        $this->assertEquals(
            $shouldPass,
            $this->validate($mockedRequestData)
        );
    }

    protected function validate($mockedRequestData)
    {
        return $this->validator
            ->make($mockedRequestData, $this->rules)
            ->passes();
    }
}