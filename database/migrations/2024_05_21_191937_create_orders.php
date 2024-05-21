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
            $table->decimal('neck', 8, 2); // Usar decimal para medidas numéricas
            $table->decimal('shoulder', 8, 2);
            $table->decimal('arm', 8, 2);
            $table->decimal('mid_front', 8, 2);
            $table->decimal('bicep', 8, 2);
            $table->decimal('bust', 8, 2);
            $table->decimal('size', 8, 2);
            $table->decimal('waist', 8, 2);
            $table->decimal('leg', 8, 2);
            $table->decimal('hip', 8, 2);
            $table->decimal('skirt_length', 8, 2);
            $table->enum('unit_length', ['cm', 'inch'])->default('inch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
