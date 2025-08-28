<?php

// database/migrations/2025_08_27_000010_create_trade_messages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trade_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('trade_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('body');
            $t->string('image_path')->nullable();
            $t->timestamps();

            $t->index(['trade_id', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('trade_messages'); }
};
