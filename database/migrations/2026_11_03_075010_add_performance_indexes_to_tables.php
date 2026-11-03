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
        // Bagian grades dan users dilewati karena index 'graded_at' dan 'role' sudah ada.

        // 3. Index untuk tabel submissions
        Schema::table('submissions', function (Blueprint $table) {
            // Menguntungkan query: filter status 'is_late' pada SubmissionController::dosenIndex
            $table->index('is_late');
            // Menguntungkan query: pengurutan berdasarkan waktu kumpul (latest/orderBy('submitted_at'))
            $table->index('submitted_at');
        });

        // 4. Index untuk tabel notifications
        Schema::table('notifications', function (Blueprint $table) {
            // Menguntungkan query: pencarian notifikasi yang belum dibaca (whereNull('read_at'))
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['is_late']);
            $table->dropIndex(['submitted_at']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
        });
    }
};