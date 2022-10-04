<?php

namespace Muzidudu\LaravelRecord\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Database\Eloquent\Collection $recorders
 * @property \Illuminate\Database\Eloquent\Collection $records
 */
trait Recordable
{
    /**
     * @deprecated renamed to `hasBeenRecordedBy`, will be removed at 5.0
     */
    public function isRecordedBy(Model $user)
    {
        return $this->hasBeenRecordedBy($user);
    }

    public function hasRecorder(Model $user): bool
    {
        return $this->hasBeenRecordedBy($user);
    }

    public function hasBeenRecordedBy(Model $user): bool
    {
        if (\is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('recorders')) {
                return $this->recorders->contains($user);
            }

            return ($this->relationLoaded('records') ? $this->records : $this->records())
                    ->where(\config('record.user_foreign_key'), $user->getKey())->count() > 0;
        }

        return false;
    }

    public function records(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(config('record.record_model'), 'recordable');
    }

    public function recorders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('record.records_table'),
            'recordable_id',
            config('record.user_foreign_key')
        )
            ->where('recordable_type', $this->getMorphClass());
    }
}
