<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\UserProfile;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::create([
            'user_name' => '正内正',
            'email' => 'user1@example.com',
            'password' => bcrypt('masa0000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user1->id,
            'profile_image' => 'banana.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 101',
        ]);

        $user2 = User::create([
            'user_name' => '胡麻斑ごま',
            'email' => 'user2@example.com',
            'password' => bcrypt('goma0000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user2->id,
            'profile_image' => 'grapes.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 102',
        ]);

        $user3 = User::create([
            'user_name' => '正内小正',
            'email' => 'user3@example.com',
            'password' => bcrypt('komasa00'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user3->id,
            'profile_image' => 'kiwi.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 103',
        ]);
        $user4 = User::create([
            'user_name' => 'ウォーレンケアンズ',
            'email' => 'user4@example.com',
            'password' => bcrypt('warlen00'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user4->id,
            'profile_image' => 'melon.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 104',
        ]);
        $user5 = User::create([
            'user_name' => '氷見野ペン',
            'email' => 'user5@example.com',
            'password' => bcrypt('pen00000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user5->id,
            'profile_image' => 'muscat.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 105',
        ]);
        $user6 = User::create([
            'user_name' => '南川野今子',
            'email' => 'user6@example.com',
            'password' => bcrypt('ima00000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user6->id,
            'profile_image' => 'peach.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 201',
        ]);
        $user7 = User::create([
            'user_name' => '漢田虎男',
            'email' => 'user7@example.com',
            'password' => bcrypt('torao000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user7->id,
            'profile_image' => 'pineapple.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 202',
        ]);
        $user8 = User::create([
            'user_name' => '熊田くま夫',
            'email' => 'user8@example.com',
            'password' => bcrypt('kumao000'),
            'email_verified_at' => now(), 
        ]);

        UserProfile::create([
            'user_id' => $user8->id,
            'profile_image' => 'strawberry.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 別館',
        ]);
    }
}
