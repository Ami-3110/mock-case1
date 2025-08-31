<?php

// database/migrations/2025_09_01_000000_add_uniques.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('trade_reads', function (Blueprint $t) {
            $t->unique(['trade_id','user_id'], 'trade_reads_trade_user_unique');
        });
        Schema::table('likes', function (Blueprint $t) {
            $t->unique(['user_id','product_id'], 'likes_user_product_unique');
        });
        Schema::table('trade_ratings', function (Blueprint $t) {
            $t->unique(['trade_id','rater_id'], 'ratings_trade_rater_unique');
        });
        Schema::table('user_profiles', function (Blueprint $t) {
            $t->unique('user_id', 'profiles_user_unique');
        });
    }

    public function down(): void {
        Schema::table('trade_reads', fn (Blueprint $t) => $t->dropUnique('trade_reads_trade_user_unique'));
        Schema::table('likes', fn (Blueprint $t) => $t->dropUnique('likes_user_product_unique'));
        Schema::table('trade_ratings', fn (Blueprint $t) => $t->dropUnique('ratings_trade_rater_unique'));
        Schema::table('user_profiles', fn (Blueprint $t) => $t->dropUnique('profiles_user_unique'));
    }
};
