<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RepositoryInterfaceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository-interface {fileName} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating repository interface files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Domain/{$domain}/Repositories/{$fileName}RepositoryInterface",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create repository interface');

            return self::FAILURE;
        }

        $this->info('The repository interface was successfully created.');

        return self::SUCCESS;
    }
}
