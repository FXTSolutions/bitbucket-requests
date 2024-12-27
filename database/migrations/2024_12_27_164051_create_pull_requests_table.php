<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->id();
            $table->string('branch_origin');
            $table->string('title');
            $table->string('branch');
            $table->string('author');
            $table->date('created_on');
            $table->date('updated_on');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pull_requests');
    }
};
