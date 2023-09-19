<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationCreateRequest;
use App\Http\Requests\ReservationListRequest;
use App\Http\Resources\ReservationResource;
use App\Repositories\ReservationRepository;
use App\Repositories\VacancyRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    private ReservationRepository $reservationRepository;
    private VacancyRepository $vacancyRepository;

    public function __construct(
        ReservationRepository $reservationRepository,
        VacancyRepository $vacancyRepository
    )
    {
        $this->reservationRepository = $reservationRepository;
        $this->vacancyRepository = $vacancyRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ReservationListRequest $request)
    {
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        return response()->json(
            ReservationResource::collection($this->reservationRepository->list($dateFrom, $dateTo)),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationCreateRequest $request)
    {
        $dateFrom = Carbon::parse($request->start_date);
        $dateTo = Carbon::parse($request->end_date);

        try {
            DB::beginTransaction();
            $areAllDatesAvailable = $this->vacancyRepository->areAllDatesAvailable($dateFrom, $dateTo, $request->bookings);
            if (!$areAllDatesAvailable) {
                return response()->json(['message' => 'Booking is for this period is unavailable.'], 400);
            }

            $this->reservationRepository->create($dateFrom, $dateTo, $request->bookings);
            $this->vacancyRepository->update($dateFrom, $dateTo, $request->bookings);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Creating reservation error.'], 500);
        }

        return response()->json();
    }
}
