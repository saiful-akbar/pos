<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EntityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:entity {fileName} {domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating entity files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $fileName = $this->argument('fileName');

        $exitCode = $this->call('make:class', [
            'name' => "Domain/{$domain}/Entities/{$fileName}Entity",
        ]);

        if ($exitCode !== 0) {
            $this->error('Failed to create entity');

            return self::FAILURE;
        }

        $this->info('The entity was successfully created.');

        return self::SUCCESS;
    }
}
