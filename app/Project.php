<?php

namespace App;

use App\Libraries\Slugger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class Project
 * @package App
 */
class Project extends Model
{

    use UserScopingTrait, UuidTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [ 'user_id', 'name', 'slug', 'description', 'due_date', 'active' ];

    /**
     *
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically saved slugged version of the name attribute.
        static::saving(function ($model) {
            $model->slug = Slugger::slug($model->name);
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Returns all tasks associated with current project
     *
     * @param bool $withCompleted
     *
     * @return mixed
     */
    public function taskList($withCompleted = false)
    {
        $query = $this->tasks()
            ->with('project')
            ->with([
                'tags' => function ($query) {
                    $query->orderBy('is_context', 'desc');
                    $query->orderBy('name', 'asc');
                }
            ])
            ->select(['*', DB::raw('due_date IS NULL AS due_date_null')])
            ->orderBy('next', 'desc')
            ->orderBy('due_date_null', 'asc')
            ->orderBy('due_date')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');

        if (! $withCompleted) {
            return $query->open()->get();
        }

        return $query->get();
    }


    /**
     * Project can have many related tasks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
