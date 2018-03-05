<?php namespace Karlm\Backendplugin;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    public function boot()
    {


    }
     public function register()
    {
        $this->registerConsoleCommand('karlm.backup', 'karlm\backendplugin\console\Backup');
    }
}
