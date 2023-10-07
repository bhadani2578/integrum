<?php

namespace Database\Seeders;


use App\Models\BankingArrangement;
use App\Models\Settlement;
use App\Models\TypeArrangement;
use App\Models\TypeContract;
use App\Models\TypeSource;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $typesource = [
            ['id' => '1','name' => 'wind'],['id'=>'2','name'=>'Solar'],
            ['id'=>'3','name'=>'Hybrid'],['id'=>'4','name'=>'BTM'],
            ['id'=>'5','name'=>'Interstate'],['id'=>'6','name'=>'Thermal'],
            ['id'=>'7','name'=>'Gas Coal'],['id'=>'8','name'=>'Biomass'],

        ];
        $typecontract = [
            ['id' => '1','name' => 'captive'],['id'=>'2','name'=>'Group captive'],
            [ 'id'=>'3','name'=>'Third Party'],
        ];
        $typearrangement = [
            ['id' => '1','name' => 'BTM'],['id'=>'2','name'=>'Interstate'],
            ['id'=>'3','name'=>'Instrastate'],
        ];
        $bankarrangement = [
            ['id' => '1','name' => 'No banking'],['id'=>'2','name'=>'Daily Banking'],
            ['id'=>'3','name'=>'Monthly banking'],['id' => '4' , 'name' => 'Annual Banking'],
        ];
        $settlement = [
           ['id' => '1','name' => 'Daily TOD'],['id'=>'2','name'=>'Monthly TOD'],
           ['id'=>'3','name'=>'Specific time blocks'],['id'=>'4','name'=>'Bucket'],
        ];

        TypeSource::insert($typesource);
        Settlement::insert($settlement);
        TypeContract::insert($typecontract);
        BankingArrangement::insert($bankarrangement);
        TypeArrangement::insert($typearrangement);
    }
}
