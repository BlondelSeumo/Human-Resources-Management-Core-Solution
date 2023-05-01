<?php


namespace App\Helpers\Traits;


use App\Helpers\Core\Traits\HasWhen;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait AssignRelationshipToPaginate
{
    public function paginated(LengthAwarePaginator $paginated)
    {
        return new class($paginated) implements PaginatorInterface {
            use HasWhen;

            protected LengthAwarePaginator $paginated;

            public function __construct(LengthAwarePaginator $paginated)
            {
                $this->paginated = $paginated;
            }

            public function setRelation(Closure $callback): PaginatorInterface
            {
                $this->paginated->each($callback);

                return $this;
            }

            public function get(): array
            {
                $response = $this->paginated->toArray();

                $response['data'] = $this->paginated->items();

                return $response;
            }
        };
    }
}