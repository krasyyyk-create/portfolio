<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reportable');
            $table->text('reason');
            $table->string('status')->default('pending');
            $table->string('resolution')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
