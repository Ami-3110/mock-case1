<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\User;

class TradeMessageSeeder extends Seeder
{
    public function run(): void
    {
        $trades = Trade::all();
        $users = User::pluck('id')->all();

        foreach ($trades as $trade) {
            // 各取引に100〜120件ぐらいランダムで入れる
            $count = rand(100, 120);

            for ($i = 0; $i < $count; $i++) {
                TradeMessage::create([
                    'trade_id'  => $trade->id,
                    'user_id'   => $users[array_rand($users)], // ランダムユーザー
                    'body'      => 'テストメッセージ ' . ($i + 1),
                    'image_path'=> null,
                ]);
            }
        }
    }
}
