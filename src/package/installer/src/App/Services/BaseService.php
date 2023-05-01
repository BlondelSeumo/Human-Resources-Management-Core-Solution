<?php


namespace Gainhq\Installer\App\Services;

use Gainhq\Installer\App\Helpers\Traits\HasAttrs;
use Illuminate\Database\Eloquent\Model;

class BaseService
{
    use HasAttrs;
    protected $model;

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function save($options = [])
    {
        $attributes = count($options) ? $options : request()->all();

        $this->model
            ->fill($this->getFillAble($attributes))
            ->save();

        return $this->model;
    }

    public function find($id)
    {
        return $this->model =  $this->model::query()->find($id);
    }

    public function __call($method, $arguments)
    {
        return $this->model->{$method}(...$arguments);
    }

}
