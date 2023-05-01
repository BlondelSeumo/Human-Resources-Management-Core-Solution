<?php


namespace App\Helpers\Traits;


use Closure;

interface PaginatorInterface
{
    public function setRelation(Closure $callback): PaginatorInterface;

    public function get(): array;
}