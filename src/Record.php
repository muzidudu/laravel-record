<?php

namespace Muzidudu\LaravelRecord;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Muzidudu\LaravelRecord\Events\Recorded;
use Muzidudu\LaravelRecord\Events\Unrecorded;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $recorder
 * @property \Illuminate\Database\Eloquent\Model $recordable
 */
class Record extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => Recorded::class,
        'deleted' => Unrecorded::class,
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = \config('record.records_table');

        parent::__construct($attributes);
    }

    protected static function boot()
    {
        parent::boot();

        self::saving(function ($record) {
            $userForeignKey = \config('record.user_foreign_key');
            $record->{$userForeignKey} = $record->{$userForeignKey} ?: auth()->id();

            if (\config('record.uuids')) {
                $record->{$record->getKeyName()} = $record->{$record->getKeyName()} ?: (string) Str::orderedUuid();
            }
        });
    }

    public function recordable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\config('auth.providers.users.model'), \config('record.user_foreign_key'));
    }

    public function recorder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->user();
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('recordable_type', app($type)->getMorphClass());
    }
}
