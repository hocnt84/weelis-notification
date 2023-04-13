<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('scope')->nullable()->index();
            $table->morphs('notifiable');
            $table->string('type_slug')->nullable()->index();
            $table->string('type_id')->nullable()->index();
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('scope')->index();
            $table->string('channel')->index();
            $table->morphs('notifiable');
            $table->uuid('notification_id')->index();
            $table->longtext('send_log')->nullable();
            $table->timestamp('send_at')->nullable();
            $table->timestamp('receive_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_reports');
    }
}
