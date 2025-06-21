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
            'email' => 'astb737@gmail.com',
            'password' => bcrypt('masa0000'),
        ]);

        UserProfile::create([
            'user_id' => $user1->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 101',
        ]);

        $user2 = User::create([
            'user_name' => '胡麻斑ごま',
            'email' => 'astb737+2@gmail.com',
            'password' => bcrypt('goma0000'),
        ]);

        UserProfile::create([
            'user_id' => $user2->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 102',
        ]);

        $user3 = User::create([
            'user_name' => '正内小正',
            'email' => 'astb737+3@gmail.com',
            'password' => bcrypt('komasa00'),
        ]);

        UserProfile::create([
            'user_id' => $user3->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 103',
        ]);
        $user4 = User::create([
            'user_name' => 'ウォーレン',
            'email' => 'astb737+4@gmail.com',
            'password' => bcrypt('warlen00'),
        ]);

        UserProfile::create([
            'user_id' => $user4->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 104',
        ]);
        $user5 = User::create([
            'user_name' => '氷川ペン',
            'email' => 'astb737+5@gmail.com',
            'password' => bcrypt('pen00000'),
        ]);

        UserProfile::create([
            'user_id' => $user5->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 105',
        ]);
        $user6 = User::create([
            'user_name' => '今川今子',
            'email' => 'astb737+6@gmail.com',
            'password' => bcrypt('ima00000'),
        ]);

        UserProfile::create([
            'user_id' => $user6->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 201',
        ]);
        $user7 = User::create([
            'user_name' => '漢田虎男',
            'email' => 'astb737+7@gmail.com',
            'password' => bcrypt('torao000'),
        ]);

        UserProfile::create([
            'user_id' => $user7->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 202',
        ]);
        $user8 = User::create([
            'user_name' => '熊田くま夫',
            'email' => 'astb737+8@gmail.com',
            'password' => bcrypt('kumao000'),
        ]);

        UserProfile::create([
            'user_id' => $user8->id,
            'profile_image' => '',
            'postal_code' => '1140024',
            'address' => '東京都北区西ヶ原4-12-14',
            'building' => 'ドールハウス 別館',
        ]);
    }
}
