<?php


namespace App\Manager\Employee;


use App\Exceptions\GeneralException;
use App\Manager\Employee\Manager\BaseManager;
use App\Manager\Employee\Manager\DepartmentManager;
use App\Manager\Employee\Manager\DesignationManager;
use App\Manager\Employee\Manager\EmployeeManagerContract;
use App\Manager\Employee\Manager\EmploymentStatusManager;
use App\Manager\Employee\Manager\WorkShiftManager;

class EmployeeManager
{
    public static array $managers = [
        'department' => DepartmentManager::class,
        'workShift' => WorkShiftManager::class,
        'designation' => DesignationManager::class,
        'employmentStatus' => EmploymentStatusManager::class
    ];

    public function walkInto(string $path, $modelId = null): EmployeeManagerContract
    {
        if (array_key_exists($path, self::$managers)) {
            return resolve(self::$managers[$path])
                ->when($modelId, fn(BaseManager $manager) => $manager->setModel($modelId));
        }

        throw new GeneralException('Manager not found');
    }




}