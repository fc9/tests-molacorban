<?php

namespace App\Services;

use League\Csv\AbstractCsv;
use League\Csv\Reader;

class BatchCSVService
{
    /**
     * @var AbstractCsv|Reader
     */
    protected AbstractCsv $csv;

    /**
     * Constructor.
     */
    public function __construct(AbstractCsv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * Get Header
     *
     * @return string[]
     */
    public function getHeader()
    {
        return $this->csv->getHeader();
    }

    /**
     * Get Records
     *
     * @return array
     */
    public function getRecords()
    {
        $records = [];

        foreach ($this->csv->getRecords() as $index => $record) {
            foreach ($record as $column => $value) {
                $column = strtolower($column);
                if ($column === 'cod_fornecedor') $value = intval($value);
                elseif ($column === 'cod_prod') $value = intval($value);
                #elseif ($column === 'cliente') $value = $value;
                #elseif ($column === 'documento') $value = $value;
                #elseif ($column === 'nome_prod') $value = $value;
                #elseif ($column === 'nome_categoria') $value = $value;
                #elseif ($column === 'nome_fornecedor') $value = $value;
                elseif ($column === 'valor_original') $value = brl2decimal($value, 2);
                elseif ($column === 'data_compra') $value = $value === '0000/00/00' ? null : $value;
                elseif ($column === 'valor_desconto') $value = brl2decimal($value, 2);
                elseif ($column === 'valor_final') $value = brl2decimal($value, 2);
                elseif ($column === 'data_pgto') $value = $value === '0000/00/00' ? null : $value;
                elseif ($column === 'data_devolucao') $value = $value === '0000/00/00' ? null : $value;
                #elseif ($column === 'status_situacao') $value = $value;
                #elseif ($column === 'status_pgto') $value = $value;
                elseif ($column === 'taxa_aplicada') $value = brl2decimal($value, 2);
                elseif ($column === 'taxa_original') $value = brl2decimal($value, 2);
                $records[$index][$column] = $value;
            }
        }

        return $records;
    }

    /**
     * Reader file CSV.
     *
     * @param string $path
     * @param string $delimiter
     * @param int $offset
     * @return BatchCSVService
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     */
    public static function readerByPath(string $path, string $delimiter = ';', int $offset = 0): BatchCSVService
    {
        $csv = Reader::createFromPath(storage_path('app/' . $path), 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset($offset);

        return new self($csv);
    }

}