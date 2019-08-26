<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateCreateAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Config::get('audit.table'), function (Blueprint $table) {
            $table->string('event');
            $table->bigInteger('user_id')->nullable();
            $table->longText('payload')->nullable();
            $table->string('session')->nullable();
            $table->string('ip')->nullable();
            $table->string('client')->nullable();
            $table->dateTime('created_at');

            $table->index('event');
            $table->index('user_id');
            $table->index('ip');
            $table->index('client');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Config::get('audit.table'));
    }
}
