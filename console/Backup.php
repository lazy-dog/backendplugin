<?php 

namespace karlm\backendplugin\console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Backup extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'karlm:backup';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a site backup';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->output->writeln('Backing up files...');

        $time_start = microtime(true);

        exec("zip -r /var/www/html/backup_$(date +\"%Y_%m_%d\").zip /var/www/html");

        $time_end = microtime(true);

        $execution_time = ($time_end - $time_start);

        $this->output->writeln('File backup completed.');
        
        $this->output->writeln('Total Execution Time: '.round($execution_time).' Seconds');

    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

}