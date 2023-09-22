<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->binary('user');
            $table->binary('name');
            $table->binary('phone');
            $table->binary('password');
            $table->unsignedBigInteger('id_consent2');
            $table->unsignedBigInteger('id_consent3');
            $table->foreign('id_consent2')->references('id')->on('consents');
            $table->foreign('id_consent3')->references('id')->on('consents');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
