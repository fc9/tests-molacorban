<?php

use App\Enums\{
    BatchStatusEnum
};

return [

    BatchStatusEnum::class => [
        BatchStatusEnum::ERROR => 'Erro',
        BatchStatusEnum::IN_FILE => 'In file',
        BatchStatusEnum::LOADING => 'Loading',
        BatchStatusEnum::DONE => 'Done',
    ],

];