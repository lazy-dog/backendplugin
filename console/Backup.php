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

        $cwd = getcwd();

        $www_directory = realpath("$cwd/../");

        $database_config_file_path  = getcwd().'/config/database.php';

        $database_config_file = file_get_contents( $database_config_file_path );

		$files = scandir($www_directory);

		$config = include $database_config_file_path;
		$mysql_password = trim($config['connections']['mysql']['password']);
		$mysql_username = $config['connections']['mysql']['username'];
		$mysql_host = $config['connections']['mysql']['host'];
		$mysql_database = $config['connections']['mysql']['database'];
		
		$this->output->writeln('Available directories:');

		foreach($files as $file){
			if($file == '..' || $file=='.'){continue;}
			$this->output->writeln($file);
		}

        $file_exists = false;

        while(!$file_exists)
		{
			$folder_name = $this->ask('Please type the name of the directory you wish to backup');

			if(file_exists("$www_directory/$folder_name") )
			{
				$file_exists = true;
			}else
			{
				$this->output->writeln("$www_directory/$folder_name is not a valid directory");
			}
        }

        $time_start = microtime(true);

        exec("zip -r $www_directory/backup_$(date +\"%Y_%m_%d\").zip $www_directory/$folder_name");

        /*nothing to see here - FML*/

        exec(" mysqldump -u $mysql_username -h $mysql_host '-p$mysql_password' $mysql_database > $www_directory/backup.sql");

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
        return [
            ['example', null, InputOption::VALUE_REQUIRED , 'An example option.', null],
        ];
    }

}