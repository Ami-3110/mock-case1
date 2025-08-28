<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_sold')) {
                $table->dropColumn('is_sold');
            }
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_sold')->default(false)->after('product_image');
        });
    }
};

