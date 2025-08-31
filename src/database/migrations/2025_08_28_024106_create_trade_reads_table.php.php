<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trade_reads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('trade_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('last_read_message_id')->nullable(); // trade_messages.id
            $t->timestamps();

            $t->unique(['trade_id','user_id']);
            $t->index(['trade_id','last_read_message_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('trade_reads'); }
};
