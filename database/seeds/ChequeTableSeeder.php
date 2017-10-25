<?php

use Illuminate\Database\Seeder;

class ChequeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Cheque::class, 15)->create();
    }
}
