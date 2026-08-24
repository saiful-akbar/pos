<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {fileName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating repository files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Repositories/{$fileName}Repository",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create repository');

            return self::FAILURE;
        }

        $this->info('The repository was successfully created.');

        return self::SUCCESS;
    }
}
