<?php

return [
    /**
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',

    /*
     * Table name for records records.
     */
    'records_table' => 'records',

    /*
     * Model name for record record.
     */
    'record_model' => Muzidudu\LaravelRecord\Record::class,
];
