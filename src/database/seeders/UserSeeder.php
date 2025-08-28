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
            'profile_image' => 'user_images/banana.png',
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
            'profile_image' => 'user_images/grapes.png',
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
            'profile_image' => 'user_images/kiwi.png',
            'postal_code' => '111-1111',
            'address' => '東京都架空市架空町1-1-1',
            'building' => 'ドールハウス 103',
        ]);
        
    }
}
