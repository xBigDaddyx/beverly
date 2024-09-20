<?php

namespace Xbigdaddyx\Beverly\Commands;

use Illuminate\Console\Command;

class BeverlyCommand extends Command
{
    public $signature = 'beverly';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
