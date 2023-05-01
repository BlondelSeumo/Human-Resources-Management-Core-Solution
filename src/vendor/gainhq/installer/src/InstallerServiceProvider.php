<?php
	
	namespace Gainhq\Installer;
	
	use Illuminate\Support\ServiceProvider;
	
	class InstallerServiceProvider extends ServiceProvider
	{
		
		public function boot()
		{
			$this->loadRoutesFrom(__DIR__ . '/routes/web.php');
			$this->loadViewsFrom(__DIR__ . '/resources/views', 'installer');
            $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'installer');

			$this->publishes([
				__DIR__.'/assets/' => public_path('install/assets/'),
				__DIR__.'/install/' => public_path('install/'),
				__DIR__.'/config/installer.php' => config_path('installer.php'),
				__DIR__.'/resources/lang/en/setup.php' => base_path('resources/lang/en/setup.php'),


			], 'gainhq-installer');
		}
	}
