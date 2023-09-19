<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationListRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'date_from' => [
                'date',
                'required',
            ],
            'date_to' => [
                'date',
                'required',
            ],
        ];
    }
}
