<?php

namespace Database\Seeders;

use App\Models\Kepala;
use App\Models\Notulen;
use Illuminate\Database\Seeder;

class NotulensiSeeder extends Seeder
{
    public function run(): void
    {
        Notulen::create([
            'name' => 'Notulen 1',
            'nip' => '198501012010011001',
            'is_active' => true,
        ]);

        Notulen::create([
            'name' => 'Notulen 2',
            'nip' => '198501012010011002',
            'is_active' => true,
        ]);

        Kepala::create([
            'name' => 'Kepala 1',
            'nip' => '197001012005011001',
            'is_active' => true,
        ]);

        Kepala::create([
            'name' => 'Kepala 2',
            'nip' => '197001012005011002',
            'is_active' => true,
        ]);
    }
}
