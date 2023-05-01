<?php


namespace App\Repositories\Core\Status;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\Memoization;
use App\Models\Core\Status;
use App\Repositories\Core\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StatusRepository extends BaseRepository
{
    use Memoization;

    public function __construct(Status $status)
    {
        $this->model = $status;
    }

    public function __call($name, $arguments)
    {
        $keys = preg_split(
            '/(^[^A-Z]+|[A-Z][^A-Z]+)/',
            $name,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        [$type] = $keys;

        if (count($keys) > 1) {
            throw_if(
                $count = count($arguments),
                new \ArgumentCountError("0 Argument expected, $count given.")
            );
            return $this->getStatusIds($keys);
        }

        if ($arguments) {
            return $this->generateValidStatuses([
                $type, ...$arguments
            ])->pluck('name', 'id')
                ->toArray();
        }
        return $this->statuses($type);
    }

    private function getStatusIds($keys)
    {
        $statuses = $this->generateValidStatuses($keys);

        if ($statuses->count() > 1) {
            return $statuses->pluck('id')->toArray();
        }

        return $statuses->first()->id;
    }

    private function generateValidStatuses($keys)
    {
        $statuses = $this->statuses($keys[0]);
        $existed_names = $statuses->pluck('name');

        $names = collect($keys)
            ->skip(1)
            ->map(function ($name) use ($keys, $existed_names) {
                $n = strtolower($name);
                $n = str_starts_with($n, 'status_') ? $n : "status_$n";
                throw_if(
                    !$existed_names->contains($n),
                    new GeneralException("Status named $n not Found.", 404)
                );
                return $n;
            })->values();

        return $statuses->whereIn('name', $names);
    }

    public function statuses($type = '')
    {
        return $this->get()->when($type, function (Collection $collection) use ($type) {
            return $collection->filter(function ($status) use ($type) {
                return $status->type == $type;
            });
        })->values();
    }

    public function get(): Collection
    {
        return $this->memoize(
            'statuses',
            fn() => cache()->rememberForever('statuses', function () {
                return DB::table('statuses')
                    ->get(['id', 'name', 'type', 'class'])
                    ->map(function ($status) {
                        $status->translated_name = trans('default.' . $status->name);
                        return $status;
                    });
            }));
    }

    public function getStatusId($type, $name)
    {
        $row = $this->statuses($type)
            ->firstWhere('name', $name);

        return $row ? $row->id : new GeneralException('Status Not Found');
    }
}

