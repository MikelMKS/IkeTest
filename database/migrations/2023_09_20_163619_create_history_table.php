<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_card')->nullable();
            $table->unsignedBigInteger('id_consent1')->nullable();
            $table->unsignedBigInteger('id_consent2')->nullable();
            $table->unsignedBigInteger('id_consent3')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_card')->references('id')->on('cards');
            $table->foreign('id_consent1')->references('id')->on('consents');
            $table->foreign('id_consent2')->references('id')->on('consents');
            $table->foreign('id_consent3')->references('id')->on('consents');
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('history');
    }
};
