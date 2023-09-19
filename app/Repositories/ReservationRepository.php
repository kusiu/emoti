<?php

namespace App\Repositories;

use App\Models\Reservation;
use Carbon\Carbon;

class ReservationRepository extends Repository
{
    public function __construct()
    {
        $this->model = new Reservation();
    }

    public function list(Carbon $dateFrom, Carbon $dateTo)
    {
        return $this->model
            ->whereDate('start_date', '<=', $dateTo)
            ->whereDate('end_date', '>=', $dateFrom)
            ->get();
    }

    public function create(Carbon $startDate, Carbon $endDate, int $bookings)
    {
        $this->model
            ->create([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'bookings' => $bookings,
            ]);
    }
}
