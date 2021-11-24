<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->unique();
            $table->string('client_secret');
            $table->string('redirect_uri');
            $table->string('access_token')->nullable();
            $table->string('vk_token_expires')->nullable();
            $table->string('browser_url');
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->integer('count')->default(0);
            $table->boolean('worked')->default(0);
            $table->boolean('need_token')->default(0);
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
        Schema::dropIfExists('applications');
    }
}
