<?php
// database/migrations/2025_08_28_120000_create_trade_ratings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('trade_ratings', function (Blueprint $t) {
      $t->id();
      $t->foreignId('trade_id')->constrained()->cascadeOnDelete();
      $t->foreignId('rater_id')->constrained('users')->cascadeOnDelete();  // 評価する側
      $t->foreignId('ratee_id')->constrained('users')->cascadeOnDelete();  // 評価される側
      $t->unsignedTinyInteger('score');   // 1..5
      $t->timestamps();

      $t->unique(['trade_id','rater_id']); // 同取引で二重評価禁止
      $t->index(['ratee_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('trade_ratings'); }
};
