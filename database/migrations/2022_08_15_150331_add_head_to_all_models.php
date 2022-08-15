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
        foreach($this->tables() as $table ) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('dyn_type')->nullable();
                $table->unsignedBigInteger('dyn_head_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach($this->tables() as $table ) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('dyn_type');
                $table->dropColumn('dyn_head_id');
            });
        }
    }

    private function tables()
    {
        return collect([
            'dyn_texts',
            'dyn_strings',
            'dyn_booleans',
            'dyn_integers',
            'dyn_dates',
        ]);

    }
};
