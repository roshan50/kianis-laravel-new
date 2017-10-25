<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);
         $this->call(MemberTableSeeder::class);
         $this->call(PurchaseTableSeeder::class);
         $this->call(ChequeTableSeeder::class);
    }
}
