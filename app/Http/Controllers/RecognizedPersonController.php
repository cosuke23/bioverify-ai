<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\View\View;

class RecognizedPersonController extends Controller
{
    public function show(Person $person): View
    {
        $person->load([
            'faces',
            'voices',
            'recognitionLogs' => function ($query) {
                $query->latest();
            },
        ]);

        return view(
            'recognized-person.show',
            compact('person')
        );
    }
}