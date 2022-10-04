<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('record.records_table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(config('record.user_foreign_key'))->index()->comment('user_id');
            $table->morphs('recordable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('record.records_table'));
    }
};
