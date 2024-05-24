<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('description');
            $table->decimal('price_unit', 10, 2);
            $table->string('piece_id');
            $table->string('piece_type');
            $table->string('piece_name');
            $table->decimal('piece_price', 10, 2);
            $table->string('category_id');
            $table->string('category_name');
            $table->string('texture_id');
            $table->string('texture_name');
            // $table->string('texture_provider');
            $table->string('color_id');
            $table->string('color_name');
            $table->string('color_code');

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
        Schema::dropIfExists('order_details');
    }
};
