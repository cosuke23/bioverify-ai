<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            [
                'person_id' => 'BV-00001',
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'suffix' => null,
                'email' => 'juan.delacruz@example.com',
                'mobile_number' => '09171234567',
                'status' => 'Active',
            ],

            [
                'person_id' => 'BV-00002',
                'first_name' => 'Maria',
                'middle_name' => 'Garcia',
                'last_name' => 'Santos',
                'suffix' => null,
                'email' => 'maria.santos@example.com',
                'mobile_number' => '09181234567',
                'status' => 'Active',
            ],

            [
                'person_id' => 'BV-00003',
                'first_name' => 'Pedro',
                'middle_name' => 'Reyes',
                'last_name' => 'Garcia',
                'suffix' => 'Jr.',
                'email' => 'pedro.garcia@example.com',
                'mobile_number' => '09191234567',
                'status' => 'Active',
            ],

            [
                'person_id' => 'BV-00004',
                'first_name' => 'Ana',
                'middle_name' => 'Lopez',
                'last_name' => 'Reyes',
                'suffix' => null,
                'email' => 'ana.reyes@example.com',
                'mobile_number' => '09201234567',
                'status' => 'Active',
            ],

            [
                'person_id' => 'BV-00005',
                'first_name' => 'Carlos',
                'middle_name' => 'Mendoza',
                'last_name' => 'Lopez',
                'suffix' => null,
                'email' => 'carlos.lopez@example.com',
                'mobile_number' => '09211234567',
                'status' => 'Inactive',
            ],
        ];

        foreach ($people as $person) {
            Person::updateOrCreate(
                [
                    'person_id' => $person['person_id'],
                ],
                $person
            );
        }
    }
}
