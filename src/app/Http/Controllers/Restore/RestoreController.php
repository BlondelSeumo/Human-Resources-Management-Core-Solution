<?php

namespace App\Http\Controllers\Restore;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Restore\RestoreService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RestoreController extends Controller
{
    public function __construct(RestoreService $service)
    {
        $this->service = $service;
    }

    public function index(){
        $this->checkPermissions();
        $this->setCredentials();

        $default_work_shift = WorkingShift::query()
            ->where('is_default', 1)
            ->with('details')
            ->first();

        $adminPass = User::query()->find(1)->first('password')->password;

        /** @var DB $db */
        $db = DB::connection('mysql_2');

        DB::transaction(function () use($db, $default_work_shift, $adminPass){
            $this->service
                ->setDb($db)
                ->runDefaultSeeder()
                ->restoreDepartments()
                ->restoreDesignations()
                ->restoreWorkShift($default_work_shift)
                ->restoreEmployees($adminPass)
                ->restoreHolidays()
                ->restoreLeaveType()
                //->restoreAttendances()
                ->restoreLeaves();
        });

        auth()->loginUsingId(1);
        return ['attendance' => $db->table('sveipa_hrm_attendances')->count()];
    }

    public function checkPermissions(): self
    {
        throw_if(
            !auth()->user()->isAppAdmin(),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function setCredentials(): self
    {
        $this->checkPermissions();
        config()->set('database.connections.mysql.strict', false);
        config()->set('database.connections.mysql_2.strict', false);

        if(! defined('STDIN')) define('STDIN', fopen("php://stdin","r"));

        $secondDatabaseCredentials = Cache::get('second_database');

        throw_if(
            !$secondDatabaseCredentials,
            new GeneralException('At first need to set second database credentials')
        );

        config([
            'database.connections.mysql_2.host' => $secondDatabaseCredentials['DB_HOST_2'],
            'database.connections.mysql_2.port' => $secondDatabaseCredentials['DB_PORT_2'],
            'database.connections.mysql_2.database' => $secondDatabaseCredentials['DB_DATABASE_2'],
            'database.connections.mysql_2.username' => $secondDatabaseCredentials['DB_USERNAME_2'],
            'database.connections.mysql_2.password' => $secondDatabaseCredentials['DB_PASSWORD_2'],
        ]);

        return $this;
    }

    public function attendanceImport()
    {
        $this->checkPermissions();
        validator(request()->all(), [
            'skip' => 'required|numeric',
            'take' => 'required|numeric',
        ])->validate();

        $this->setCredentials();

        $db = DB::connection('mysql_2');

        $this->service
            ->setDb($db)
            ->restoreAttendances(request()->get('skip'), request()->get('take'));

        return 'success';
    }

    public function updateSettings()
    {
        $this->checkPermissions();

        validator(request()->all(), [
            'host' => 'required',
            'port' => 'required',
            'db_name' => 'required',
            'db_username' => 'required',
            'db_password' => 'required'
        ])->validate();

        Cache::forget('second_database');

        Cache::rememberForever('second_database', function () {
            return [
                'DB_HOST_2' => request()->get('host'),
                'DB_PORT_2' => request()->get('port'),
                'DB_DATABASE_2' => request()->get('db_name'),
                'DB_USERNAME_2' => request()->get('db_username'),
                'DB_PASSWORD_2' => request()->get('db_password'),
            ];
        });

        return created_responses('credentials');
    }

    public function settings(): array
    {
        $this->checkPermissions();

        $secondDatabaseCredentials = Cache::get('second_database');

        return [
            'host' => $secondDatabaseCredentials ? $secondDatabaseCredentials['DB_HOST_2'] : '',
            'port' => $secondDatabaseCredentials ? $secondDatabaseCredentials['DB_PORT_2'] : '',
            'db_name' => $secondDatabaseCredentials ? $secondDatabaseCredentials['DB_DATABASE_2'] : '',
            'db_username' => $secondDatabaseCredentials ? $secondDatabaseCredentials['DB_USERNAME_2'] : '',
            'db_password' => $secondDatabaseCredentials ? $secondDatabaseCredentials['DB_PASSWORD_2'] : '',
        ];
    }

}
