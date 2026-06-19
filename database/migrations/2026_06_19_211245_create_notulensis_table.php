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
        Schema::create('notulensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('notulen_id')->constrained('notulens')->cascadeOnDelete();
            $table->foreignId('kepala_id')->constrained('kepalas')->cascadeOnDelete();
            $table->dateTime('notulensi_date');
            $table->date('meeting_date');
            $table->string('location');
            $table->longText('isi_notulensi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensis');
    }
};
