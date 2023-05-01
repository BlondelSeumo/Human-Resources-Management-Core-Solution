<?php


namespace App\Manager\Employee\Manager;


use App\Helpers\Core\Traits\HasWhen;
use Illuminate\Database\Eloquent\Model;

class BaseManager
{
    use HasWhen;

    public $model;

    public function getModel()
    {
        return $this->model instanceof Model ? $this->model->id : $this->model;
    }

    public function setModel(int $model): BaseManager
    {
        $this->model = $model;

        return $this;
    }
}