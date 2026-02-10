<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_by_application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->string('candidate_name');
            $table->string('email')->index();
            $table->string('source')->default('career_site')->index();
            $table->string('stage')->default('applied')->index();
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->unsignedSmallInteger('fit_score')->nullable();
            $table->text('cover_letter')->nullable();
            $table->text('resume_text');
            $table->timestamp('applied_at')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
