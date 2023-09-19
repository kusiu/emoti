<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Vacancy;
use Database\Seeders\VacanciesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(VacanciesSeeder::class);
    }

    /**
     * @test
     */
    public function returns_list_of_reservations(): void
    {
        // this should be excluded
        Reservation::factory()->create([
            'start_date' => '2023-01-01',
            'end_date' => '2023-01-01',
            'bookings' => 1,
        ]);

        // this should be included
        Reservation::factory()->create([
            'start_date' => '2023-01-01',
            'end_date' => '2023-01-10',
            'bookings' => 1,
        ]);

        // this should be included
        Reservation::factory()->create([
            'start_date' => '2023-01-10',
            'end_date' => '2023-01-12',
            'bookings' => 1,
        ]);

        // this should be included
        Reservation::factory()->create([
            'start_date' => '2023-01-11',
            'end_date' => '2023-01-14',
            'bookings' => 1,
        ]);

        // this should be included
        Reservation::factory()->create([
            'start_date' => '2023-01-09',
            'end_date' => '2023-01-14',
            'bookings' => 2,
        ]);

        // this should be excluded
        Reservation::factory()->create([
            'start_date' => '2023-01-20',
            'end_date' => '2023-01-21',
            'bookings' => 1,
        ]);

        $response = $this->json('get', '/api/reservations', [
            'date_from' => '2023-01-10',
            'date_to' => '2023-01-13',
        ])->assertOk();
        $reservations = json_decode($response->getContent(), true);
        $this->assertCount(4, $reservations);
        $this->assertEquals($reservations, [
            [
                "start_date" => "2023-01-01",
                "end_date" => "2023-01-10",
                "bookings" => 1
            ],
            [
                "start_date" => "2023-01-10",
                "end_date" => "2023-01-12",
                "bookings" => 1
            ],
            [
                "start_date" => "2023-01-11",
                "end_date" => "2023-01-14",
                "bookings" => 1
            ],
            [
                "start_date" => "2023-01-09",
                "end_date" => "2023-01-14",
                "bookings" => 2
            ]
        ]);
    }

    /**
     * @test
     */
    public function create_reservations()
    {
        $this->assertEquals(10, Vacancy::whereDate('date', '2023-01-10')->first()->slots);

        $this->postJson('/api/reservations', [
            'start_date' => '2023-01-10',
            'end_date' => '2023-01-10',
            'bookings' => 6,
        ])->assertOk();

        $this->postJson('/api/reservations', [
            'start_date' => '2023-01-10',
            'end_date' => '2023-01-10',
            'bookings' => 4,
        ])->assertOk();

        $reservations = Reservation::whereDate('start_date', '2023-01-10')
            ->whereDate('end_date', '2023-01-10')
            ->get();
        $this->assertCount(2, $reservations);
        $this->assertEquals(0, Vacancy::whereDate('date', '2023-01-10')->first()->slots);

        // try to create reservation if there is not enough vacancy
        $this->postJson('/api/reservations', [
            'start_date' => '2023-01-09',
            'end_date' => '2023-01-10',
            'bookings' => 1,
        ])->assertStatus(400);

        $this->assertEquals(0, Vacancy::whereDate('date', '2023-01-10')->first()->slots);
    }
}
