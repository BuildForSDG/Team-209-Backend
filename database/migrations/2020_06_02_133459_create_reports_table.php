<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string('address')->default("UNKNOWN");
            $table->point('location')->nullable();
            $table->string('description');
            $table->unsignedBigInteger("incident_id");
            $table->unsignedBigInteger("user_id");
            $table->timestamps(6);

            $table->foreign('incident_id')
                ->references('id')
                ->on('incidents')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
