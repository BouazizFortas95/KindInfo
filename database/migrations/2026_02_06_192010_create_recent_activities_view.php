<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS recent_activities");

        DB::statement("
            CREATE VIEW recent_activities AS
            SELECT 
                'lesson_' || lesson_id || '_' || user_id as id,
                lesson_id as foreign_id,
                user_id,
                updated_at as activity_date,
                'lesson' as type
            FROM lesson_user
            WHERE progress >= 100
            UNION
            SELECT 
                'badge_' || badge_id || '_' || user_id as id,
                badge_id as foreign_id,
                user_id,
                earned_at as activity_date,
                'badge' as type
            FROM user_badges
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS recent_activities");
    }
};
