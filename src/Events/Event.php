<?php

namespace Muzidudu\LaravelRecord\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    public Model $record;

    public function __construct(Model $record)
    {
        $this->record = $record;
    }
}
