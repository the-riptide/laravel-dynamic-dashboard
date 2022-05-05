<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dyn_heads', function (Blueprint $table) {
            $table->id();
            $table->string('next_model')->nullable();
            $table->unsignedBigInteger('next_model_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('type');
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('dynamic_heads');
    }
};
