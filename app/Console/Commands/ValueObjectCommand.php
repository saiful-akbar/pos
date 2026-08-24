<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValueObjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:value-object {fileName} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating value object files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Domain/{$domain}/ValueObjects/{$fileName}Value",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create value object');

            return self::FAILURE;
        }

        $this->info('The value object was successfully created.');

        return self::SUCCESS;
    }
}
