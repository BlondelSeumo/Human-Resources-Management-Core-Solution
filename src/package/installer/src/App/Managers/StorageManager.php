<?php


namespace Gainhq\Installer\App\Managers;

use Illuminate\Support\Facades\Artisan;

class StorageManager
{
    public function link()
    {
        $exploded_path = explode('/', base_path());
        if (end($exploded_path) === 'src') {
            return $this->handle();
        } elseif (!file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            return ['status' => true, 'message' => trans('default.symlink_working') , 'optional' => null];
        }elseif (file_exists(public_path('storage'))){
            //if already exists
            return ['status' => true, 'message' => trans('default.symlink_working') , 'optional' => null];
        }
    }

    public function handle()
    {
        $_SESSION["symlink_enabled"] = false;
        foreach ($this->links() as $link => $target) {
            if (strpos($link, 'src/public/')) {
                $link = str_replace('src/public/', '', $link);

                if (!file_exists($link)) {
                    try {
                        app()->make('files')->link($target, $link);
                        return ['status' => true, 'message' => trans('default.symlink_working'), 'optional' => null];

                    } catch (\Exception $e) {

                        return [
                            'status' => false,
                            'message' => $e->getMessage(),
                            'optional' => [
                                'symlink' =>[
                                    'target_link' => $target,
                                    'storage_link' => $link
                                ]
                            ]
                        ];
                    }

                } else {
                    return [
                        'status' => true,
                        'message' => trans('default.symlink_working'),
                        'optional' => null
                    ];
                }
            }
        }
    }

    protected function links()
    {
        return $this->laravel['config']['filesystems.links'] ??
            [public_path('storage') => storage_path('app/public')];
    }
}