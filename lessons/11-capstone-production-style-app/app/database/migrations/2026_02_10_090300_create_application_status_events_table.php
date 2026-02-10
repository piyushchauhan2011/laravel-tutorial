<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_status_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('from_stage')->nullable();
            $table->string('to_stage');
            $table->string('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_status_events');
    }
};
