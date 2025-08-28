<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trade;
use App\Models\TradeRating;
use App\Models\User;

class TradeRatingSeeder extends Seeder
{
    public function run(): void
    {
        $trades = Trade::with(['buyer','seller'])->get();

        foreach ($trades as $trade) {
            // 購入者から出品者へ
            TradeRating::factory()->create([
                'trade_id' => $trade->id,
                'rater_id' => $trade->buyer_id,
                'ratee_id' => $trade->seller_id,
                'score'    => rand(3,5),
            ]);

            // 出品者から購入者へ
            TradeRating::factory()->create([
                'trade_id' => $trade->id,
                'rater_id' => $trade->seller_id,
                'ratee_id' => $trade->buyer_id,
                'score'    => rand(3,5),
            ]);
        }
    }
}
