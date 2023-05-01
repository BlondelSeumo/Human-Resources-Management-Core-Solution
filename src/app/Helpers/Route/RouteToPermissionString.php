<?php


namespace App\Helpers\Route;


use App\Exceptions\GeneralException;

trait RouteToPermissionString
{
    protected array $exploded_route_name;

    protected string $route_name;

    public function validateRouteName()
    {
        $exploded_route_name = explode('.', $this->route_name);

        if (count($exploded_route_name) > 2) {
            throw new GeneralException('Route name can\'t be more than 2 index. Eg.brand.list.change not allowed');
        }

        $this->exploded_route_name = $exploded_route_name;

        return $this;
    }

    public function getPermissionString(): string
    {
        $exploded_route_name = $this->exploded_route_name;

        if (count($exploded_route_name) == 2) {
            $replacer_array = ['store' => 'create', 'index' => 'view', 'destroy' => 'delete', 'show' => 'view', 'lists' => 'view', 'edit' => 'update'];

            /*
             * if the index is exist in replacer array the replace the value with replacer array value
             * otherwise take the default value
            */
            $action = !empty($replacer_array[$exploded_route_name[1]]) ?
                $replacer_array[$exploded_route_name[1]] :
                $exploded_route_name[1];

            return str_replace('-', '_', $action . '_' . $exploded_route_name[0]);
        }

        $route_name = str_replace('-', '_', $this->route_name);

        return $permission_string = "manage_{$route_name}";
    }


    public function setRouteName(string $route_name)
    {
        $this->route_name = $route_name;
        return $this;
    }
}