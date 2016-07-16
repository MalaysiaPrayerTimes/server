<?php

namespace App\Http\Requests;

class PrayerRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
        ];
    }
    
    public function getYear()
    {
        return $this->input('year');
    }
    
    public function getMonth()
    {
        return $this->input('month');
    }
}
