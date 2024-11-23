<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('pk_task');
            $table->string('c_name');
            $table->text('c_description');
            $table->date('d_completion');
            $table->foreignId('fk_project')->constrained(table: 'projects', column: 'pk_project', indexName: 'task_project_id');
            $table->foreignId('fk_status')->constrained(table: 'status_tasks', column: 'pk_status', indexName: 'task_status_id');
            $table->foreignId('fk_user_responsible')->constrained(table: 'users', column: 'pk_user', indexName: 'task_user_responsible_id');
            $table->foreignId('fk_user_creator')->constrained(table: 'users', column: 'pk_user', indexName: 'task_user_creator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
