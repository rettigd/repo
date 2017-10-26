<?php
namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class Repository
{

    protected $user;
    protected $model;

    protected $with = [];

    public function __construct(Model $model, User $user = null)
    {

        $this->user = $user;


        if ($user == null) {
            $this->user = \Auth::check() ? \Auth::user() : new User();
        }

        $this->model = $model;
    }

    public function getbyId($id, $with = false)
    {
        $this->model = $this->model->with($with ?: $this->with)->findOrFail($id);
        return $this->view();
    }

    public function getByParams(array $params, $with = false)
    {

        $query = $this->model->with($with ?: $this->with);

        return $this->viewCollection($this->build($query, $params)->get());
    }

    public function create($attributes)
    {
        if (\Gate::allows('create', $this->model)) {
            return $this->save($attributes);
        }

        throw new \Exception();
    }

    public function update($id, $attributes)
    {
        $this->model = $this->model->findOrFail($id);

        if (\Gate::allows('update', $this->model)) {
            return $this->save($attributes);
        }
        throw new \Exception();
    }

    public function save($attributes)
    {
        $this->model->fillable($this->getFillable())->fill($attributes)->save();
        $this->model->makeVisible($this->getVisible());
        return $this->model;
    }

    public function delete($id)
    {
        $this->model = $this->model->findOrFail($id);

        if (\Gate::allows('delete', $this->model)) {
            return $this->model->delete();
        }
        throw new \Exception();
    }

    public function view()
    {
        if (\Gate::allows('view', $this->model)) {

            $this->model->makeVisible($this->getVisible());
            $relationships = $this->viewRelationships(collect($this->model->getRelations()));

            $this->model->setRelations($this->filterRelations($relationships));

            return $this->model;
        }

        throw new \Exception();
    }

    public function viewCollection(Collection $collection)
    {
        return $collection->filter(function($model) {
            return \Gate::allows('view', $model);
        })->each(function($model) {
            $model->makeVisible(app()->make($model->repositoryClass, [$this->user, $model])->getVisible());
            $relationships = $this->viewRelationships(collect($model->getRelations()));

            $model->setRelations($this->filterRelations($relationships));

        })->slice(0, 10)->values();
    }

    public function filterRelations(Collection $relationships) {
        $newRelationships = [];
        foreach ($relationships as $key => $relationship) {
            if (is_a($relationship, Model::class)) {
                if (collect($relationship->toArray())) {
                    $newRelationships[$key] = $relationship;
                }
            }
            else {
                $newRelationships[$key] = $relationship->filter(function ($relationship) {
                    return collect($relationship->toArray())->count();
                })->values();
            }
        }
        return $newRelationships;
    }

    public function viewRelationships(Collection $collection)
    {

        $collection->filter(function($relation) {
            if (is_a($relation, Model::class)) {

                $visible = app()->make($relation->repositoryClass, [$this->user, $relation])->getVisible();

                $relation->makeVisible($visible);
                $relations = collect($relation->getRelations());
                if ($relations->count()) {
                    $relationships = $this->viewRelationships($relations);
                    $relation->setRelations($this->filterRelations($relationships));
                }
                return \Gate::allows('view', $relation);
            } else {
                $this->viewRelationships($relation);
                return false;
            }
            return false;
        });

        return $collection;
    }

    public function build(Builder $query, array $params)
    {
        collect($params)->each(function($value, $column) use ($query) {
            $query->where($column, $value);
        });
        return $query;
    }

}