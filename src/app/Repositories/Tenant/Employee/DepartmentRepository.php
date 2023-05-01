<?php


namespace App\Repositories\Tenant\Employee;


use App\Helpers\Core\Traits\HasWhen;
use App\Models\Tenant\Employee\Department;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Support\Collection;

class DepartmentRepository extends TenantRepository
{
    use HasWhen;

    protected array $departments = [];

    protected array $users = [];

    protected array $manager = [];

    protected array $actionHelper = ['children' => 'childDepartments', 'parents' => 'parentDepartments'];

    protected string $actions = '';

    public function __construct(Department $department)
    {
        $this->model = $department;
    }

    public function employees(array $departments = [], $field = ['id']) : Collection
    {
        if (!count($departments)) {
            return collect([]);
        }

        return $this->model
            ->with('users:'.implode($field))
            ->findMany($departments)
            ->map(fn (Department $department) => $department->users)
            ->flatten();
    }

    public function getDepartments($id, $need = 'children', $type = 'user', $withChild = true): array
    {
        $collections = $this->getCollections($id, $need, $type, $withChild);

        $this->when($need == 'children',
            fn(DepartmentRepository $repository) => $repository->setDepartments($collections),
            fn(DepartmentRepository $repository) => $repository->setParentDepartments($collections[0])

        );

        return $this->departments;
    }

    public function setParentDepartments($department, $column = 'id'): self
    {
        array_push($this->departments, $department[$column]);

        if (array_key_exists($this->actions, $department) && $department[$this->actions]){
            $this->setParentDepartments($department[$this->actions], $column);
        }

        return $this;
    }

    public function setDepartments($departments, $column = 'id'): self
    {
        foreach ($departments as $department){
            if (!in_array($department[$column], $this->departments)){
                array_push($this->departments, $department[$column]);
            }
            if (array_key_exists($this->actions, $department) && count($department[$this->actions])){
                $this->setDepartments($department[$this->actions], $column);
            }
        }

        return $this;
    }

    public function setUsers($departments): DepartmentRepository
    {
        foreach ($departments as $department){
            foreach ($department['users'] as $user){
                if(!in_array($user['id'], $this->users)){
                    array_push($this->users, $user['id']);
                }
            }
            if (array_key_exists($this->actions, $department) && count($department[$this->actions])){
                $this->setUsers($department[$this->actions]);
            }
        }

        return $this;
    }

    public function getDepartmentUsers($id, $type = 'user', $withChild = true): array
    {
        $collections = $this->getCollections($id, 'children', $type, $withChild);
        $this->setUsers($collections);

        return $this->users;
    }

    public function getDepartmentsAndUsers($id, $type = 'user', $withChild = true): array
    {
        $collections = $this->getCollections($id, 'children', $type, $withChild);
        $this->setDepartments($collections);
        $this->setUsers($collections);

        return [
            'departments' => $this->departments,
            'users' => $this->users
        ];
    }

    public function getDepartmentsManagers($id, $need = 'children', $type = 'user', $withChild = true): array
    {
        $collections = $this->getCollections($id, $need, $type, $withChild);

        $this->when($need == 'children',
            fn(DepartmentRepository $repository) => $repository->setDepartments($collections, 'manager_id'),
            fn(DepartmentRepository $repository) => $repository->setParentDepartments($collections[0], 'manager_id')
        );

        return $this->departments;
    }

    public function getCollections($id, $need = 'children', $type = 'user', $withChild = true): array
    {
        $withRelations = ['users:id'];

        $typeActions = ['user' => 'manager_id', 'department'=> 'id'][$type];

        $this->actions = ['children' => 'child_departments', 'parents' => 'parent_departments'][$need];

        if ($withChild){
            $withRelations = array_merge([
                $this->actionHelper[$need].':id,name,manager_id,department_id',
            ], $withRelations);
        }
        return Department::with(
            $withRelations
        )->where($typeActions, $id)
            ->get(['id', 'name', 'manager_id','department_id'])->toArray();
    }

}
