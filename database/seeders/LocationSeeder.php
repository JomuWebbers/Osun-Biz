<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $towns = [
            ['Osogbo', 'osogbo'],
            ['Ile-Ife', 'ile-ife'],
            ['Ilesa', 'ilesa'],
            ['Ede', 'ede'],
            ['Iwo', 'iwo'],
            ['Ila-Orangun', 'ila-orangun'],
            ['Ikirun', 'ikirun'],
            ['Ipetumodu', 'ipetumodu'],
            ['Ejigbo', 'ejigbo'],
            ['Gbongan', 'gbongan'],
        ];

        DB::table('locations')->insertOrIgnore(array_map(
            fn (array $town): array => [
                'name' => $town[0],
                'slug' => $town[1],
                'state' => 'Osun',
                'created_at' => now(),
            ],
            $towns,
        ));
    }
}
