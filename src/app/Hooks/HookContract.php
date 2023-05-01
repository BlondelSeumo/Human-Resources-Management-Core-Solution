<?php


namespace App\Hooks;


abstract class HookContract
{
    public $model;

    public $relation, $properties, $type;

    abstract public function handle();

    /**
     * @param mixed $model
     * @return HookContract
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }


    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

}
