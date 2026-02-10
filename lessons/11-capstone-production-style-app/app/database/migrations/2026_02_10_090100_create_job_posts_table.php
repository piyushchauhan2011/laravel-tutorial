<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->string('location');
            $table->string('employment_type')->default('full_time');
            $table->string('status')->default('draft')->index();
            $table->boolean('is_remote')->default(false);
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->text('description');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
