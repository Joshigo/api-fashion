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
        Schema::create('textures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->string('color_name');
            $table->string('color_code');
            $table->decimal('total_stock', 10, 2);
            $table->decimal('cost_meter_texture', 10, 2);

            $table->timestamps();

            $table->unsignedBigInteger('piece_id');
            $table->foreign('piece_id')->references('id')->on('pieces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('textures', function (Blueprint $table) {
            $table->dropForeign(['piece_id']);
            $table->dropColumn('piece_id');
        });
        Schema::dropIfExists('textures');
    }
};
