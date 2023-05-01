<?php
	
	
	namespace Gainhq\Installer\App\Controllers;
	
	use Gainhq\Installer\App\Managers\StorageManager;
	
	class InstallerController extends Controller
	{
		
		public function index()
		{
			return view('installer::installer');
		}

        public function testStorageLink()
        {
            return resolve(StorageManager::class)->link();
        }

        public function phpInfo()
        {
            return $this->phpInfo();
        }
	}