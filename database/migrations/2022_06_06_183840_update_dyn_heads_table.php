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
        if (Schema::hasColumn('dyn_heads', 'type')) {

            Schema::table('dyn_heads', function (Blueprint $table) {

                $table->renameColumn('type', 'dyn_type');
            });

        }

        Schema::table('dyn_heads', function (Blueprint $table) {

            $table->unsignedInteger('dyn_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('dyn_heads', 'dyn_type')) {

            Schema::table('dyn_heads', function (Blueprint $table) {

                $table->renameColumn('dyn_type', 'type');
            });
        }

        Schema::table('dyn_heads', function (Blueprint $table) {

            $table->dropColumn('dyn_order');
        });
    }
};
