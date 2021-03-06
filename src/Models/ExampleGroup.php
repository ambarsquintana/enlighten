<?php

namespace Styde\Enlighten\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Styde\Enlighten\Models\Concerns\GetStats;

class ExampleGroup extends Model implements Statusable
{
    use GetStats;

    protected $connection = 'enlighten';

    protected $table = 'enlighten_example_groups';

    protected $guarded = [];

    // Relationships
    public function examples()
    {
        return $this->hasMany(Example::class, 'group_id')
            ->orderBy('id');
    }

    public function stats()
    {
        return $this->hasMany(Example::class, 'group_id', 'id')
            ->selectRaw('DISTINCT(test_status), COUNT(id) as count, group_id')
            ->groupBy('test_status', 'group_id');
    }

    // Helpers
    public function matches(Module $module)
    {
        return Str::is($module->pattern, $this->class_name);
    }

    // Scopes
    public function scopeFilterByArea($query, Area $area) : Builder
    {
        return $query->where('area', $area->slug);
    }

    // Accessors

    public function getPassingTestsCountAttribute()
    {
        return $this->getPassingTestsCount();
    }

    public function getTestsCountAttribute()
    {
        return $this->getTestsCount();
    }

    public function getStatusAttribute(): string
    {
        return $this->getStatus();
    }

    public function getUrlAttribute()
    {
        return route('enlighten.group.show', [
            'run' => $this->run_id,
            'group' => $this->slug,
        ]);
    }
}
