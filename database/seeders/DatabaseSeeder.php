<?php

namespace Database\Seeders;

use App\Models\Connection;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderStage;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductStage;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin ───────────────────────────────────────────────
        $this->call(AdminSeeder::class);

        
    }
}
