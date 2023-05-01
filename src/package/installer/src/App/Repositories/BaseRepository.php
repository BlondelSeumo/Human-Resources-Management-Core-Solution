<?php

namespace Gainhq\Installer\App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    /**
     * The repository model.
     *
     * @var Model
     */
    protected $model;


    public function __call($name, $arguments)
    {
        return $this->model->{$name}(...$arguments);
    }
}
