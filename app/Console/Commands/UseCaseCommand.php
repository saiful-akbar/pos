<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UseCaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:use-case {fileName} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating use case files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Application/{$domain}/UseCases/{$fileName}",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create use case');

            return self::FAILURE;
        }

        $this->info('The use case was successfully created.');

        return self::SUCCESS;
    }
}
