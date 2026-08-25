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

        // ── Manufacturers ───────────────────────────────────────
        $apex = User::create([
            'name'          => 'Bilal Ahmed',
            'email'         => 'apex@soms.test',
            'password'      => Hash::make('password'),
            'business_name' => 'Apex Textiles',
            'role'          => 'manufacturer',
            'is_active'     => true,
            'is_verified'   => true,
        ]);

        $prime = User::create([
            'name'          => 'Saad Malik',
            'email'         => 'prime@soms.test',
            'password'      => Hash::make('password'),
            'business_name' => 'Prime Leather Works',
            'role'          => 'manufacturer',
            'is_active'     => true,
            'is_verified'   => true,
        ]);

        // ── Shop Owners ─────────────────────────────────────────
        $quickMart = User::create([
            'name'          => 'Ayesha Khan',
            'email'         => 'quickmart@soms.test',
            'password'      => Hash::make('password'),
            'business_name' => 'Quick Mart',
            'role'          => 'shop_owner',
            'is_active'     => true,
            'is_verified'   => true,
        ]);

        $urban = User::create([
            'name'          => 'Omar Raza',
            'email'         => 'urban@soms.test',
            'password'      => Hash::make('password'),
            'business_name' => 'Urban Outlet',
            'role'          => 'shop_owner',
            'is_active'     => true,
            'is_verified'   => true,
        ]);

        // ── Products (Apex Textiles) ────────────────────────────
        $tshirt = Product::create(['user_id' => $apex->id, 'name' => 'Premium Cotton T-Shirt', 'description' => 'High-quality cotton t-shirt available in multiple colors and sizes.']);
        $hoodie = Product::create(['user_id' => $apex->id, 'name' => 'Urban Hoodie', 'description' => 'Comfortable fleece-lined hoodie with custom embroidery options.']);

        ProductVariant::create(['product_id' => $tshirt->id, 'variant_name' => 'Small - White',   'sku' => 'TS-SM-WHT', 'price' => 800,  'stock_quantity' => 500]);
        ProductVariant::create(['product_id' => $tshirt->id, 'variant_name' => 'Medium - Black',  'sku' => 'TS-MD-BLK', 'price' => 850,  'stock_quantity' => 350]);
        ProductVariant::create(['product_id' => $hoodie->id, 'variant_name' => 'Large - Grey',    'sku' => 'HD-LG-GRY', 'price' => 2200, 'stock_quantity' => 200]);
        ProductVariant::create(['product_id' => $hoodie->id, 'variant_name' => 'XL - Navy Blue',  'sku' => 'HD-XL-NVY', 'price' => 2400, 'stock_quantity' => 150]);

        ProductStage::create(['product_id' => $tshirt->id, 'name' => 'Fabric Cutting',  'description' => 'Cut fabric panels per size spec', 'sort_order' => 1]);
        ProductStage::create(['product_id' => $tshirt->id, 'name' => 'Stitching',        'description' => 'Sew panels into finished garment',  'sort_order' => 2]);
        ProductStage::create(['product_id' => $tshirt->id, 'name' => 'Quality Check',    'description' => 'Inspect stitching and fabric quality', 'sort_order' => 3]);

        ProductStage::create(['product_id' => $hoodie->id, 'name' => 'Cutting',          'description' => 'Cut fleece and outer panels',       'sort_order' => 1]);
        ProductStage::create(['product_id' => $hoodie->id, 'name' => 'Sewing & Embroidery', 'description' => 'Assemble hoodie and add logo',  'sort_order' => 2]);
        ProductStage::create(['product_id' => $hoodie->id, 'name' => 'Final Inspection',  'description' => 'Check zip, seams, and embroidery',  'sort_order' => 3]);

        // ── Products (Prime Leather) ────────────────────────────
        $wallet = Product::create(['user_id' => $prime->id, 'name' => 'Handcrafted Leather Wallet', 'description' => 'Genuine leather bifold wallet with RFID protection.']);

        ProductVariant::create(['product_id' => $wallet->id, 'variant_name' => 'Brown - Standard', 'sku' => 'LW-BRN-STD', 'price' => 1500, 'stock_quantity' => 300]);
        ProductVariant::create(['product_id' => $wallet->id, 'variant_name' => 'Black - Premium',  'sku' => 'LW-BLK-PRM', 'price' => 2200, 'stock_quantity' => 150]);

        ProductStage::create(['product_id' => $wallet->id, 'name' => 'Leather Cutting',   'description' => 'Cut leather panels and pockets',   'sort_order' => 1]);
        ProductStage::create(['product_id' => $wallet->id, 'name' => 'Stitching & Assembly','description' => 'Sew panels and install card slots', 'sort_order' => 2]);
        ProductStage::create(['product_id' => $wallet->id, 'name' => 'Polishing & Packaging','description' => 'Edge polish and box packaging',  'sort_order' => 3]);

        // ── Connections ──────────────────────────────────────────
        $conn1 = Connection::create([
            'shop_owner_id'   => $quickMart->id,
            'manufacturer_id' => $apex->id,
            'initiated_by'    => $quickMart->id,
            'status'          => 'accepted',
        ]);

        $conn2 = Connection::create([
            'shop_owner_id'   => $quickMart->id,
            'manufacturer_id' => $prime->id,
            'initiated_by'    => $quickMart->id,
            'status'          => 'accepted',
        ]);

        $conn3 = Connection::create([
            'shop_owner_id'   => $urban->id,
            'manufacturer_id' => $apex->id,
            'initiated_by'    => $urban->id,
            'status'          => 'accepted',
        ]);

        // ── Orders ──────────────────────────────────────────────
        // Order 1: Quick Mart → Apex Textiles (T-Shirts, Completed, fully paid)
        $order1 = Order::create([
            'order_number'          => 'ORD-1001',
            'shop_owner_id'         => $quickMart->id,
            'manufacturer_id'       => $apex->id,
            'product_id'            => $tshirt->id,
            'quantity'              => 200,
            'unit'                  => 'pcs',
            'total_amount'          => 160000,
            'paid_amount'           => 160000,
            'payment_terms'         => '50% advance',
            'due_date'              => now()->addWeeks(3),
            'status'                => 'Completed',
            'progress_percent'      => 100,
            'special_instructions'  => 'Pack in poly bags, size labels on each piece.',
        ]);

        OrderStage::create(['order_id' => $order1->id, 'name' => 'Cutting',        'description' => 'Cut 200 t-shirt panels',   'status' => 'Completed', 'sort_order' => 1, 'completed_at' => now()->subDays(18)]);
        OrderStage::create(['order_id' => $order1->id, 'name' => 'Stitching',      'description' => 'Sew all 200 units',         'status' => 'Completed', 'sort_order' => 2, 'completed_at' => now()->subDays(12)]);
        OrderStage::create(['order_id' => $order1->id, 'name' => 'Quality Check',  'description' => 'Final QC and packing',      'status' => 'Completed', 'sort_order' => 3, 'completed_at' => now()->subDays(5)]);

        Payment::create([
            'order_id'               => $order1->id,
            'payer_id'               => $quickMart->id,
            'payee_id'               => $apex->id,
            'amount'                 => 80000,
            'txn_ref_no'             => 'TXN-ADV001',
            'stripe_payment_intent_id' => 'pi_1Nadvance80k',
            'status'                 => 'completed',
            'paid_at'                => now()->subDays(20),
        ]);

        Payment::create([
            'order_id'               => $order1->id,
            'payer_id'               => $quickMart->id,
            'payee_id'               => $apex->id,
            'amount'                 => 80000,
            'txn_ref_no'             => 'TXN-BAL001',
            'stripe_payment_intent_id' => 'pi_2Nbalance80k',
            'status'                 => 'completed',
            'paid_at'                => now()->subDays(4),
        ]);

        // Order 2: Quick Mart → Prime Leather (Wallets, In Progress, partially paid)
        $order2 = Order::create([
            'order_number'          => 'ORD-1002',
            'shop_owner_id'         => $quickMart->id,
            'manufacturer_id'       => $prime->id,
            'product_id'            => $wallet->id,
            'quantity'              => 100,
            'unit'                  => 'pcs',
            'total_amount'          => 150000,
            'paid_amount'           => 75000,
            'payment_terms'         => '50% advance',
            'due_date'              => now()->addWeeks(4),
            'status'                => 'In Progress',
            'progress_percent'      => 60,
            'special_instructions'  => 'Gift box packaging for each wallet.',
        ]);

        OrderStage::create(['order_id' => $order2->id, 'name' => 'Leather Cutting',    'description' => 'Cut 100 wallet sets',       'status' => 'Completed', 'sort_order' => 1, 'completed_at' => now()->subDays(8)]);
        OrderStage::create(['order_id' => $order2->id, 'name' => 'Stitching & Assembly', 'description' => 'Sew and assemble wallets',  'status' => 'In Progress', 'sort_order' => 2]);
        OrderStage::create(['order_id' => $order2->id, 'name' => 'Polishing',           'description' => 'Edge polish and box pack',  'status' => 'Pending',   'sort_order' => 3]);

        Payment::create([
            'order_id'               => $order2->id,
            'payer_id'               => $quickMart->id,
            'payee_id'               => $prime->id,
            'amount'                 => 75000,
            'txn_ref_no'             => 'TXN-ADV002',
            'safepay_tracker_id'     => 'sp_75kadvance002',
            'status'                 => 'completed',
            'paid_at'                => now()->subDays(10),
        ]);

        // Order 3: Urban Outlet → Apex Textiles (Hoodies, Pending, unpaid)
        $order3 = Order::create([
            'order_number'          => 'ORD-1003',
            'shop_owner_id'         => $urban->id,
            'manufacturer_id'       => $apex->id,
            'product_id'            => $hoodie->id,
            'quantity'              => 50,
            'unit'                  => 'pcs',
            'total_amount'          => 110000,
            'paid_amount'           => 0,
            'payment_terms'         => 'Upon delivery',
            'due_date'              => now()->addWeeks(6),
            'status'                => 'Pending',
            'progress_percent'      => 0,
            'special_instructions'  => 'Embroider Urban Outlet logo on left chest.',
        ]);

        OrderStage::create(['order_id' => $order3->id, 'name' => 'Cutting',              'description' => 'Cut hoodie panels',       'status' => 'Pending', 'sort_order' => 1]);
        OrderStage::create(['order_id' => $order3->id, 'name' => 'Sewing & Embroidery',  'description' => 'Assemble and embroider',  'status' => 'Pending', 'sort_order' => 2]);
        OrderStage::create(['order_id' => $order3->id, 'name' => 'Final Inspection',     'description' => 'QC and packaging',        'status' => 'Pending', 'sort_order' => 3]);

        // ── Messages ────────────────────────────────────────────
        Message::create(['connection_id' => $conn1->id, 'sender_id' => $quickMart->id, 'body' => 'Hi Bilal, we need 200 t-shirts in white and black. Can you start this week?']);
        Message::create(['connection_id' => $conn1->id, 'sender_id' => $apex->id,      'body' => 'Yes Ayesha, we have fabric in stock. I will share the production timeline today.']);
        Message::create(['connection_id' => $conn2->id, 'sender_id' => $quickMart->id, 'body' => 'Saad, how is the wallet order coming along?']);
        Message::create(['connection_id' => $conn2->id, 'sender_id' => $prime->id,     'body' => 'We finished cutting 100 units, stitching is in progress. Should be done by Friday.']);
        Message::create(['connection_id' => $conn3->id, 'sender_id' => $urban->id,     'body' => 'Hello, we are interested in ordering hoodies for our winter collection.']);
    }
}
