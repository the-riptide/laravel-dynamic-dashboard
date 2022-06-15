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
        Schema::create('dyn_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained();
            $table->string('origin_type');
            $table->string('link_type');
            $table->foreignId('link_id')->constrained();
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
        Schema::dropIfExists('dyn_relations');
    }
};
