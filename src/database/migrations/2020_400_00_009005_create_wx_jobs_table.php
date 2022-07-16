<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/2/20, 7:31 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('src')->nullable('gfs');
            $table->text('storage');
            $table->string('date_folder');
            $table->string('file_name');
            $table->string('degree')->nullable('0.25');
            $table->string('hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wx_jobs');
    }
}
