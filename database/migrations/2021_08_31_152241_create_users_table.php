<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('bdate');
            $table->string('photo');
            $table->boolean('wrote')->default(0);
            $table->boolean('need_to_write')->default(0);
            $table->string('last_seen')->default('0');
            $table->string('age')->nullable()->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
