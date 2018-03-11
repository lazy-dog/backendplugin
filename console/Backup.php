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

		$files = scandir($www_directory);

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

        $database_config_file = file_get_contents( getcwd().'/config/database.php' );
        preg_match("/'password'[ ]*=>[ ]*'.*/", $database_config_file, $match);
        preg_match("/[ ]*=>[ ]*'.*/", str_replace('password', '', $match[0]), $match2);
        $match2 = trim(str_replace('=>', '', $match2[0]), ',');
        $match2 = ltrim($match2, ' ');
        $match2 = rtrim($match2, '\'');
        $match2 = rtrim($match2, ' ');
        $password = ltrim($match2, '\'');

        $remove_me = "'database' => 'storage/database.sqlite'";
        preg_match_all("/'database'[ ]*=>[ ]*'.*/", $database_config_file, $match);
        preg_match_all("/[ ]*=>[ ]*'.*/", str_replace('database', '', $match[0][1]), $match2);
        $match2 = trim(str_replace('=>', '', $match2[0][0]), ',');
        $match2 = ltrim($match2, ' ');
        $match2 = rtrim($match2, '\'');
        $match2 = rtrim($match2, ' ');
        $username = ltrim($match2, '\'');

        preg_match("/'host'[ ]*=>[ ]*'.*/", $database_config_file, $match);
        preg_match("/[ ]*=>[ ]*'.*/", str_replace('host', '', $match[0]), $match2);
        $match2 = trim(str_replace('=>', '', $match2[0]), ',');
        $match2 = ltrim($match2, ' ');
        $match2 = rtrim($match2, '\'');
        $match2 = rtrim($match2, ' ');
        $host = ltrim($match2, '\'');
        if($host=='local'){$host = 'localhost';}
        $this->output->writeln($host);

        exec(" mysqldump -u dbman -h $host '-p$password' $username > $www_directory/backup.sql");

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