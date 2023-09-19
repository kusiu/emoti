<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationCreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => [
                'date',
                'required',
            ],
            'end_date' => [
                'date',
                'required',
            ],
            'bookings' => [
                'integer',
                'required',
            ]
        ];
    }
}
