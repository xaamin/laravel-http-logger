<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHttpLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('http_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique();
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->text('url')->nullable();
            $table->text('headers')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->longText('input')->nullable();
            $table->longText('files')->nullable();
            $table->smallInteger('status_code')->signed()->nullable();
            $table->decimal('response_time', 15, 4)->nullable();
            $table->string('response_type')->nullable();
            $table->longText('response_body')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('http_logs');
    }
}
