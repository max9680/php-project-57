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
        Schema::create('label_tasks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('label_id');
            $table->unsignedBigInteger('task_id');

            $table->index('label_id', 'label_task_label_idx');
            $table->index('task_id', 'label_task_task_idx');

            $table->foreign('label_id', 'label_task_label_fk')->on('labels')->references('id');
            $table->foreign('task_id', 'label_task_task_fk')->on('tasks')->references('id');

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
        Schema::dropIfExists('label_tasks');
    }
};
