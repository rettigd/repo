<?php

function auth_collection(\Illuminate\Support\Collection $collection)
{
    return $collection->filter(function($model) {
        return \Gate::allows('view', $model);
    });
}

function auth_relation(\Illuminate\Support\Collection $collection)
{
    $collection->filter(function($relation) {
        if (is_a($relation, Illuminate\Database\Eloquent\Model::class)) {
            return \Gate::allows('view', $relation);
        } else {
            auth_relation($relation);
        }
        return true;
    });
    return $collection;
}