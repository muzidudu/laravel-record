<?php

namespace Muzidudu\LaravelRecord\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;

/**
 * @property \Illuminate\Database\Eloquent\Collection $records
 */
trait Recorder
{
    public function record(Model $object): void
    {
        /* @var \Muzidudu\LaravelRecord\Traits\Recordable $object */
        if (!$this->hasRecorded($object)) {
            $record = app(config('record.record_model'));
            $record->{config('record.user_foreign_key')} = $this->getKey();

            $object->records()->save($record);
        }
    }

    public function unrecord(Model $object): void
    {
        /* @var \Muzidudu\LaravelRecord\Traits\Recordable $object */
        $relation = $object->records()
            ->where('recordable_id', $object->getKey())
            ->where('recordable_type', $object->getMorphClass())
            ->where(config('record.user_foreign_key'), $this->getKey())
            ->first();

        if ($relation) {
            $relation->delete();
        }
    }

    public function toggleRecord(Model $object): void
    {
        $this->hasRecorded($object) ? $this->unrecord($object) : $this->record($object);
    }

    public function hasRecorded(Model $object): bool
    {
        return ($this->relationLoaded('records') ? $this->records : $this->records())
            ->where('recordable_id', $object->getKey())
            ->where('recordable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(config('record.record_model'), config('record.user_foreign_key'), $this->getKeyName());
    }

    public function attachRecordStatus($recordables, callable $resolver = null)
    {
        $returnFirst = false;
        $toArray = false;

        switch (true) {
            case $recordables instanceof Model:
                $returnFirst = true;
                $recordables = \collect([$recordables]);
                break;
            case $recordables instanceof LengthAwarePaginator:
                $recordables = $recordables->getCollection();
                break;
            case $recordables instanceof Paginator:
            case $recordables instanceof CursorPaginator:
                $recordables = \collect($recordables->items());
                break;
            case $recordables instanceof LazyCollection:
                $recordables = \collect($recordables->all());
                break;
            case \is_array($recordables):
                $recordables = \collect($recordables);
                $toArray = true;
                break;
        }

        \abort_if(!($recordables instanceof Enumerable), 422, 'Invalid $recordables type.');

        $recorded = $this->records()->get()->keyBy(function ($item) {
            return \sprintf('%s:%s', $item->recordable_type, $item->recordable_id);
        });

        $recordables->map(function ($recordable) use ($recorded, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $recordable = $resolver($recordable);

            if ($recordable && \in_array(Recordable::class, \class_uses_recursive($recordable))) {
                $key = \sprintf('%s:%s', $recordable->getMorphClass(), $recordable->getKey());
                $recordable->setAttribute('has_recorded', $recorded->has($key));
            }
        });

        return $returnFirst ? $recordables->first() : ($toArray ? $recordables->all() : $recordables);
    }

    public function getRecordItems(string $model): Builder
    {
        return app($model)->whereHas(
            'recorders',
            function ($q) {
                return $q->where(config('record.user_foreign_key'), $this->getKey());
            }
        );
    }
}
