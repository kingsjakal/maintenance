<?php

namespace Stevebauman\Maintenance\Validators;

class MeterValidator extends BaseValidator
{
    protected $rules = [
        'metric' => 'required|integer',
        'name' => 'required|max:250',
        'reading' => 'positive',
        'comment' => 'max:250',
    ];
}
