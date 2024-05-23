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

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('status')->default(true);
            $table->decimal('neck', 8, 2)->nullable();
            $table->decimal('shoulder', 8, 2)->nullable();
            $table->decimal('arm', 8, 2)->nullable();
            $table->decimal('mid_front', 8, 2)->nullable();
            $table->decimal('bicep', 8, 2)->nullable();
            $table->decimal('bust', 8, 2)->nullable();
            $table->decimal('size', 8, 2)->nullable();
            $table->decimal('waist', 8, 2)->nullable();
            $table->decimal('leg', 8, 2)->nullable();
            $table->decimal('hip', 8, 2)->nullable();
            $table->decimal('skirt_length', 8, 2)->nullable();
            $table->enum('unit_length', ['cm', 'inch'])->default('inch');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
        Schema::dropIfExists('orders');
    }
};
