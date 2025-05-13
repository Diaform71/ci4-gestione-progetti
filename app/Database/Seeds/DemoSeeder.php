<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $this->call('AnagraficaDemoSeeder');
        $this->call('ProgettiDemoSeeder');
        $this->call('AttivitaDemoSeeder');
    }
}
