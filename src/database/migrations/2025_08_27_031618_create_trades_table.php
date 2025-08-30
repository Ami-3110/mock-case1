<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $t->string('status')->default('trading');
            $t->timestamps();

            $t->index(['buyer_id', 'seller_id']);
            $t->index(['status', 'updated_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('trades'); }
};
