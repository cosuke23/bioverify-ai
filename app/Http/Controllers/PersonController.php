<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function index(): View
    {
        $people = Person::latest()->paginate(10);

        return view('people.index', compact('people'));
    }

    public function create(): View
    {
        return view('people.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'person_id' => [
                'required',
                'string',
                'max:50',
                'unique:people,person_id',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ]);

        Person::create($validated);

        return redirect()
            ->route('people.index')
            ->with('success', 'Person successfully registered.');
    }

    public function show(Person $person): View
    {
        $person->load([
            'faces',
            'voices',
            'recognitionLogs' => function ($query) {
                $query->latest()->limit(20);
            },
        ]);

        return view('people.show', compact('person'));
    }

    public function edit(Person $person): View
    {
        return view('people.edit', compact('person'));
    }

    public function update(
        Request $request,
        Person $person
    ): RedirectResponse {
        $validated = $request->validate([
            'person_id' => [
                'required',
                'string',
                'max:50',
                'unique:people,person_id,' . $person->id,
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'mobile_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ]);

        $person->update($validated);

        return redirect()
            ->route('people.show', $person)
            ->with('success', 'Person successfully updated.');
    }

    public function destroy(Person $person): RedirectResponse
    {
        $person->update([
            'status' => 'Inactive',
        ]);

        return redirect()
            ->route('people.index')
            ->with('success', 'Person successfully deactivated.');
    }
}
