<?php

namespace App\Libraries;

use League\Csv\Reader;

class ReaderCsv extends Reader
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
        $csv = parent::createFromPath(storage_path('app/' . $path), 'r');
        $csv->setDelimiter($delimiter);
        $csv->setHeaderOffset(0);

        return $csv->jsonSerialize();
    }

}