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
            $table->bigInteger('piece_id');
            $table->string('piece_type');
            $table->string('piece_name');
            $table->decimal('piece_price_base', 10, 2);
            $table->decimal('piece_usage_meter_texture', 10, 2);
            $table->decimal('piece_price_total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->bigInteger('category_id');
            $table->string('category_name');
            $table->bigInteger('texture_id');
            $table->string('texture_name');
            $table->enum('status', ['Accepted', 'Pending', 'Completed'])->default('Pending');
            $table->string('texture_color_name');
            $table->string('texture_color_code');
            $table->decimal('texture_cost_meter', 10, 2);
            $table->decimal('texture_total_stock', 10, 2);
            // $table->string('texture_provider');

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
