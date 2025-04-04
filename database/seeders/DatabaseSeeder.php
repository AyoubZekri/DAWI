<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('roles')->insert([
            ['id'=> 1,'role_name' => 'admin', 'created_at' => now()],
            ['id' => 2,'role_name' => 'User', 'created_at' => now()],
            ['id' => 3, 'role_name' => 'Clinic', 'created_at' => now()],
            ['id' => 4, 'role_name' => 'Doctor', 'created_at' => now()],

        ]);
    }
}
