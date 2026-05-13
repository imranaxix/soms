<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;

class ShopOwnerController extends Controller
{
    public function dashboard()
    {
        // Simple array of orders
        $orders = Order::where('shop_owner_id', auth()->id())->latest()->get();
    
        // Initialize all stats to 0 first
        $totalOrders = 0;
        $pending = 0;
        $inProgress = 0;
        $completed = 0;
        $totalAmount = 0;
        $totalPaid = 0;

        // loop to calculate everything
        foreach ($orders as $order) {
            $totalOrders++;
            $totalAmount = $totalAmount + $order->total_amount;
            $totalPaid = $totalPaid + ($order->paid_amount ?? 0);

            if ($order->status == 'Pending') {
                $pending++;
            }
            if ($order->status == 'In Progress') {
                $inProgress++;
            }
            if ($order->status == 'Completed') {
                $completed++;
            }
        }

        // Final calculation
        $totalPendingAmount = $totalAmount - $totalPaid;

        // Putting it in an array for the view
        $stats = [
            'total' => $totalOrders,
            'pending' => $pending,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPendingAmount
        ];

        return view('shop-owner.dashboard', compact('orders', 'stats'));
    }

    public function orders()
    {
        $orders = Order::where('shop_owner_id', auth()->id())->with(['manufacturer', 'product', 'variant'])->latest()->get();
        return view('shop-owner.orders.index', compact('orders'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('shop-owner.profile.index', compact('user'));
    }

    public function createOrder()
    {
        $user = auth()->user();
        $manufacturers = $user->manufacturerConnections()
            ->where('status', 'accepted')
            ->with('manufacturer')
            ->get()
            ->pluck('manufacturer');

        return view('shop-owner.orders.create', compact('manufacturers'));
    }
    

    public function storeOrder(Request $request)
    {
        $request->validate([
            'manufacturer_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'due_date' => 'required|date|after:today',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'required|string',
            'special_instructions' => 'nullable|string',
        ]);

        $order = Order::create([
            'order_number' => '#' . strtoupper(Str::random(8)),
            'shop_owner_id' => auth()->id(),
            'manufacturer_id' => $request->manufacturer_id,
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'total_amount' => $request->total_amount,
            'payment_terms' => $request->payment_terms,
            'due_date' => $request->due_date,
            'special_instructions' => $request->special_instructions,
            'status' => 'Pending',
            'progress_percent' => 0,
        ]);

        return redirect()->route('shop.orders.index')->with('success', 'Order created successfully! Order Number: ' . $order->order_number);
    }

    public function getProducts($manufacturerId)
    {
        $manufacturer = \App\Models\User::findOrFail($manufacturerId);
        
        // Ensure they are connected
        $isConnected = auth()->user()->manufacturerConnections()
            ->where('manufacturer_id', $manufacturerId)
            ->where('status', 'accepted')
            ->exists();

        if (!$isConnected) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $products = $manufacturer->products()->get(['id', 'name']);
        return response()->json($products);
    }

    public function getVariants($productId)
    {
        $product = \App\Models\Product::findOrFail($productId);
        
        // Ensure the shop owner is connected to the manufacturer of this product
        $isConnected = auth()->user()->manufacturerConnections()
            ->where('manufacturer_id', $product->user_id)
            ->where('status', 'accepted')
            ->exists();

        if (!$isConnected) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $variants = $product->variants()->get(['id', 'variant_name', 'sku', 'price']);
        return response()->json($variants);
    }

    public function showOrder($id)
    {
        $order = Order::with(['manufacturer', 'product', 'variant'])->findOrFail($id);

        // Ensure the order belongs to the authenticated shop owner
        if ($order->shop_owner_id !== auth()->id()) {
            abort(403);
        }

        return view('shop-owner.orders.show', compact('order'));
    }

    public function connections()
    {
        $user = auth()->user();
        $pendingRequests = $user->connections()->where('status', 'pending')->where('initiated_by', '!=', $user->id)->get();
        $activeConnections = $user->connections()->where('status', 'accepted')->get();

        return view('shop-owner.connections.index', compact('pendingRequests', 'activeConnections'));
    }

    public function payments()
    {
        $orders = [
            ['id' => 'ORD-001', 'productName' => 'Men\'s Denim Jacket', 'manufacturerName' => 'Elite Garments', 'shopOwnerName' => 'City Fashion Store', 'totalAmount' => 150000, 'totalPaid' => 75000, 'remainingBalance' => 75000, 'status' => 'Pending', 'payments' => [['id' => 'P1', 'date' => '2024-04-04', 'method' => 'bank_transfer', 'amount' => 75000]]],
            ['id' => 'ORD-002', 'productName' => 'Cotton T-Shirts', 'manufacturerName' => 'Z-Fashion', 'shopOwnerName' => 'City Fashion Store', 'totalAmount' => 240000, 'totalPaid' => 0, 'remainingBalance' => 240000, 'status' => 'Pending', 'payments' => []],
            ['id' => 'ORD-003', 'productName' => 'Silk Scarves', 'manufacturerName' => 'Heritage Weaves', 'shopOwnerName' => 'City Fashion Store', 'totalAmount' => 80000, 'totalPaid' => 80000, 'remainingBalance' => 0, 'status' => 'Completed', 'payments' => [['id' => 'P2', 'date' => '2024-04-02', 'method' => 'upi', 'amount' => 80000]]],
        ];

        return view('shop-owner.payments.index', compact('orders'));
    }

    public function reports()
    {
        $stats = [
            'totalSpend' => 30000,
            'pendingLiabilities' => 170000,
            'ordersFulfilled' => 18,
            'topManufacturer' => 'Textile Masters'
        ];

        $chartData = [
            'spending' => [
                'labels' => ['Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026', 'Mar 2026'],
                'data' => [0, 0, 0, 0, 0, 200000], 
            ],
            'distribution' => [
                'labels' => ['Pending', 'In Progress', 'Completed', 'Rejected'],
                'data' => [15, 25, 50, 10], 
            ]
        ];

        $transactions = [
            ['id' => 'TRX-0921A', 'date' => 'Mar 18, 2026', 'manufacturer' => 'Textile Masters', 'method' => 'Bank Transfer', 'status' => 'Paid', 'amount' => 4500],
            ['id' => 'TRX-0915B', 'date' => 'Mar 15, 2026', 'manufacturer' => 'Global Garments', 'method' => 'JazzCash', 'status' => 'Pending', 'amount' => 2100],
            ['id' => 'TRX-0884C', 'date' => 'Mar 12, 2026', 'manufacturer' => 'Quick Stitch Co.', 'method' => 'PayPal', 'status' => 'Overdue', 'amount' => 8000],
            ['id' => 'TRX-0870D', 'date' => 'Mar 10, 2026', 'manufacturer' => 'Textile Masters', 'method' => 'Bank Transfer', 'status' => 'Paid', 'amount' => 3200],
        ];

        return view('shop-owner.reports.index', compact('stats', 'chartData', 'transactions'));
    }
}