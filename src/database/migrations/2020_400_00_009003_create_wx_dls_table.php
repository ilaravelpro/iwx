<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/2/20, 7:31 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxDlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_dls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('src')->nullable('gfs');
            $table->text('url');
            $table->text('storage');
            $table->string('degree')->nullable('0.25');
            $table->timestamp('dl_at')->nullable();
            $table->timestamp('ref_at')->nullable();
            $table->timestamp('valid_at')->nullable();
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
        Schema::dropIfExists('wx_dls');
    }
}
