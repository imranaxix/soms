<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function dashboard()
    {
        // Mock data for Manufacturer Dashboard
        $stats = [
            'totalOrders' => 3,
            'pendingApproval' => 1,
            'inProduction' => 2,
            'completed' => 0,
            'totalRevenue' => 200000,
            'receivedPayment' => 30000,
            'pendingPayment' => 170000,
        ];

        $pendingOrders = [
            [
                'order_id' => '#075130',
                'shop_owner' => 'ABC Textiles',
                'product' => 'Sweater - Sleeveless',
                'quantity' => '50 pieces',
                'due_date' => 'Apr 11, 2026',
                'amount' => 50000,
            ]
        ];

        $activeOrders = [
            [
                'order_id' => '#731221',
                'shop_owner' => 'ABC Textiles',
                'product' => 'T-Shirt - V Neck',
                'quantity' => '100 pieces',
                'due_date' => 'Apr 24, 2026',
                'progress' => 50,
                'amount' => 100000,
            ],
            [
                'order_id' => '#880759',
                'shop_owner' => 'ABC Textiles',
                'product' => 'Sweater - Sleeveless',
                'quantity' => '50 pieces',
                'due_date' => 'Apr 24, 2026',
                'progress' => 30,
                'amount' => 50000,
            ]
        ];

        return view('manufacturer.dashboard', compact('stats', 'pendingOrders', 'activeOrders'));
    }

    public function orders()
    {
        $orders = [
            [
                'order_id' => '#075130',
                'product' => 'Sweater - Sleeveless',
                'qty' => 50,
                'shop_owner' => 'ABC Textiles',
                'date' => 'Mar 24, 2026',
                'amount' => 50000,
                'status' => 'Pending',
            ],
            [
                'order_id' => '#880759',
                'product' => 'Sweater - Sleeveless',
                'qty' => 50,
                'shop_owner' => 'ABC Textiles',
                'date' => 'Mar 24, 2026',
                'amount' => 50000,
                'status' => 'In Progress',
            ],
            [
                'order_id' => '#731221',
                'product' => 'T-Shirt - V Neck',
                'qty' => 100,
                'shop_owner' => 'ABC Textiles',
                'date' => 'Mar 24, 2026',
                'amount' => 100000,
                'status' => 'In Progress',
            ],
        ];

        return view('manufacturer.orders.index', compact('orders'));
    }

    public function catalog()
    {
        $products = [
            [
                'name' => 'T-Shirt',
                'description' => '',
                'variations' => ['V Neck', 'Round Neck', 'Polo'],
            ],
            [
                'name' => 'Sweater',
                'description' => '',
                'variations' => ['Sleeveless'],
            ],
        ];

        return view('manufacturer.catalog.index', compact('products'));
    }

    public function createProduct()
    {
        $products = [
            [
                'name' => 'T-Shirt',
                'description' => '',
                'variations' => ['V Neck', 'Round Neck', 'Polo'],
            ],
            [
                'name' => 'Sweater',
                'description' => '',
                'variations' => ['Sleeveless'],
            ],
        ];

        return view('manufacturer.catalog.create', compact('products'));
    }

    public function production()
    {
        $orders = [
            [
                'id' => '#731221',
                'product' => 'T-Shirt - V Neck',
                'quantity' => '100 pieces',
                'shop_owner' => 'ABC Textiles',
                'due_date' => 'Apr 24, 2026',
                'current_stage' => 'Tailoring/Assembly',
                'progress' => 50,
                'stages' => [
                    ['id' => 1, 'name' => 'Material Preparation', 'desc' => 'Sourcing and preparing raw materials', 'status' => 'completed'],
                    ['id' => 2, 'name' => 'Machine Work', 'desc' => 'Cutting, stitching, or manufacturing', 'status' => 'completed'],
                    ['id' => 3, 'name' => 'Tailoring/Assembly', 'desc' => 'Assembling components', 'status' => 'completed'],
                    ['id' => 4, 'name' => 'Finishing', 'desc' => 'Quality check and finishing touches', 'status' => 'pending'],
                    ['id' => 5, 'name' => 'Packing', 'desc' => 'Final packing and ready for dispatch', 'status' => 'pending'],
                ]
            ],
            [
                'id' => '#880759',
                'product' => 'Sweater - Sleeveless',
                'quantity' => '50 pieces',
                'shop_owner' => 'ABC Textiles',
                'due_date' => 'Apr 24, 2026',
                'current_stage' => 'Machine Work',
                'progress' => 30,
                'stages' => [
                    ['id' => 1, 'name' => 'Material Preparation', 'desc' => 'Sourcing and preparing raw materials', 'status' => 'completed'],
                    ['id' => 2, 'name' => 'Machine Work', 'desc' => 'Cutting, stitching, or manufacturing', 'status' => 'completed'],
                    ['id' => 3, 'name' => 'Tailoring/Assembly', 'desc' => 'Assembling components', 'status' => 'pending'],
                    ['id' => 4, 'name' => 'Finishing', 'desc' => 'Quality check and finishing touches', 'status' => 'pending'],
                    ['id' => 5, 'name' => 'Packing', 'desc' => 'Final packing and ready for dispatch', 'status' => 'pending'],
                ]
            ]
        ];

        return view('manufacturer.production.index', compact('orders'));
    }

    public function payments()
    {
        $stats = [
            'totalOrderValue' => 200000,
            'totalPaid' => 30000,
            'pendingBalance' => 170000
        ];

        $transactions = [
            [
                'date' => 'Mar 24, 2026',
                'order_id' => '#731221',
                'received_from' => 'ABC Textiles',
                'method' => 'jazzcash',
                'amount' => 30000
            ]
        ];

        $orderBalances = [
            ['order_id' => '#731221', 'product' => 'T-Shirt - V Neck', 'total' => 100000, 'paid' => 30000, 'balance' => 70000, 'status' => 'Pending'],
            ['order_id' => '#880759', 'product' => 'Sweater - Sleeveless', 'total' => 50000, 'paid' => 0, 'balance' => 50000, 'status' => 'Pending'],
            ['order_id' => '#075130', 'product' => 'Sweater - Sleeveless', 'total' => 50000, 'paid' => 0, 'balance' => 50000, 'status' => 'Pending'],
        ];

        return view('manufacturer.payments.index', compact('stats', 'transactions', 'orderBalances'));
    }

    public function connections()
    {
        return view('manufacturer.connections.index');
    }

    public function reports()
    {
        $stats = [
            'totalRevenue' => 200000,
            'pendingReceivables' => 170000,
            'ordersFulfilled' => 0,
            'topCustomer' => 'ABC Textiles'
        ];

        $chartData = [
            'revenue' => [
                'labels' => ['Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026'],
                'data' => [0, 0, 0, 0, 0, 180000],
            ],
            'distribution' => [
                'labels' => ['Pending', 'In Progress', 'Completed', 'Rejected'],
                'data' => [15, 25, 50, 10], 
            ]
        ];

        $transactions = [
            ['id' => 'PEND-1221', 'date' => '24 Apr 2026', 'partner' => 'ABC Textiles', 'method' => '--', 'status' => 'Pending', 'amount' => 70000],
            ['id' => 'PEND-0759', 'date' => '24 Apr 2026', 'partner' => 'ABC Textiles', 'method' => '--', 'status' => 'Pending', 'amount' => 50000],
            ['id' => 'PEND-5130', 'date' => '11 Apr 2026', 'partner' => 'ABC Textiles', 'method' => '--', 'status' => 'Pending', 'amount' => 50000],
            ['id' => 'TRX-69655', 'date' => '24 Mar 2026', 'partner' => 'ABC Textiles', 'method' => 'jazzcash', 'status' => 'Paid', 'amount' => 30000],
        ];

        return view('manufacturer.reports.index', compact('stats', 'chartData', 'transactions'));
    }
}
