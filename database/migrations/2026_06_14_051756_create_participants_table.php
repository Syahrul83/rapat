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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('jenis_peserta', ['pegawai_dinas', 'eksternal']);
            $table->enum('tipe_peserta', ['narasumber', 'peserta']);
            $table->string('nip', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->text('signature_data');
            $table->boolean('declaration')->default(false);
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['meeting_id', 'nip']);
            $table->unique(['meeting_id', 'nik']);
            $table->index(['meeting_id', 'jenis_peserta']);
            $table->index('registered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
