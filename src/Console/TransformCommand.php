<?php

namespace Axn\RevertDbDefaultStringLength\Console;

use Axn\RevertDbDefaultStringLength\Transformer;
use Illuminate\Console\Command;

class TransformCommand extends Command
{
    protected $signature = 'revert-db-default-string-length:transform';

    protected $description = 'Revert database default string length to 255 in a Laravel project';

    public function handle(Transformer $transformer)
    {
        $transformer->setConsoleCommand($this);
        $transformer->transform();
    }
}
