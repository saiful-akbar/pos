<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {fileName} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating enum files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Domain/{$domain}/Enums/{$fileName}Enum",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create enum');

            return self::FAILURE;
        }

        $this->info('The enum was successfully created.');

        return self::SUCCESS;
    }
}
