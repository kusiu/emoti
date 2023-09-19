<?php

namespace App\Repositories;

use App\Models\Vacancy;
use Carbon\Carbon;

class VacancyRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Vacancy();
    }

    public function update(Carbon $startDate, Carbon $endDate, int $bookings)
    {
        $this->model
            ->whereBetween('date', [$startDate, $endDate])
            ->decrement('slots', $bookings);
    }

    public function areAllDatesAvailable(Carbon $startDate, Carbon $endDate, int $bookings): bool
    {
        $vacancies = $this->model->whereBetween('date', [$startDate, $endDate])->get();

        foreach ($vacancies as $vacancy) {
            if ($vacancy->slots - $bookings < 0) {
                return false;
            }
        }

        return true;
    }
}
