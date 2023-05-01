<?php


namespace App\Models\Core\Macro;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class BelongsToOne extends BelongsToMany
{
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->{$this->parentKey}])) {
                $model->setRelation(
                    $relation, Arr::first($dictionary[$key])
                );
            } else {
                $model->setRelation($relation, null);
            }
        }

        return $models;
    }
}