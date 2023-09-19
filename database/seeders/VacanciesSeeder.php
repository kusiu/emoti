<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacanciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::create(2023, 1,1);
        $endDate = Carbon::create(2023, 1,31);

        $dateRange = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);

        foreach ($dateRange as $date){
            DB::table('vacancies')->insert([
                'date' => $date,
                'slots' => 10,
            ]);
        }
    }
}
