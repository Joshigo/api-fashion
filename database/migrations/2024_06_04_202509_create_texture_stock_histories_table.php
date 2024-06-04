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
        Schema::create('texture_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('total_stock');
            $table->integer('amount');

            $table->unsignedBigInteger('texture_id');
            $table->foreign('texture_id')->references('id')->on('textures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('texture_stock_histories', function (Blueprint $table) {
            $table->dropForeign(['texture_id']);
            $table->dropColumn('texture_id');
        });
        Schema::dropIfExists('texture_stock_histories');
    }
};
