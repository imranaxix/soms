<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
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

        // Notify the manufacturer about the new order
        $manufacturer = \App\Models\User::find($request->manufacturer_id);
        $manufacturer->notify(new \App\Notifications\NewOrderReceived($order, auth()->user()));

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
        $order = Order::with(['manufacturer', 'product', 'variant', 'stages'])->findOrFail($id);

        // Ensure the order belongs to the authenticated shop owner
        if ($order->shop_owner_id !== auth()->id()) {
            abort(403);
        }

        return view('shop-owner.orders.show', compact('order'));
    }

    public function cancelOrder($id)
    {
        $order = Order::where('shop_owner_id', auth()->id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $order->update(['status' => 'Cancelled']);

        // Notify the manufacturer
        $order->manufacturer->notify(new \App\Notifications\OrderStatusChanged($order, auth()->user(), 'Cancelled'));

        return back()->with('success', 'Order has been cancelled.');
    }

    public function confirmDelivery($id)
    {
        $order = Order::where('shop_owner_id', auth()->id())
            ->where('status', 'Delivered')
            ->findOrFail($id);

        $order->update(['status' => 'Completed']);

        // Notify the manufacturer
        $order->manufacturer->notify(new \App\Notifications\OrderStatusChanged($order, auth()->user(), 'Completed'));

        return back()->with('success', 'Order delivery confirmed. Order is now Completed.');
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
        $user = Auth::user();

        // Real orders with their completed payments
        $orders = \App\Models\Order::where('shop_owner_id', $user->id)
            ->with(['payments' => fn($q) => $q->where('status', 'completed')->latest(), 'manufacturer', 'product'])
            ->latest()
            ->get();

        // Aggregate stats
        $totalOrderValue  = $orders->sum('total_amount');
        $totalPaid        = $orders->sum('paid_amount');
        $pendingBalance   = $totalOrderValue - $totalPaid;

        // Flat list of completed transactions for the table
        $transactions = $orders->flatMap(function ($order) {
            return $order->payments->map(fn($p) => [
                'date'             => $p->paid_at ?? $p->created_at,
                'order_number'     => $order->order_number,
                'order_id'         => $order->id,
                'manufacturer'     => $order->manufacturer->business_name ?? $order->manufacturer->name,
                'txn_ref_no'       => $p->txn_ref_no,
                'pp_response_code' => $p->pp_response_code,
                'amount'           => $p->amount,
                'status'           => $p->status,
            ]);
        })->sortByDesc('date')->values();

        // Per-order balance rows
        $orderBalances = $orders->map(fn($order) => [
            'order_number' => $order->order_number,
            'order_id'     => $order->id,
            'product'      => $order->product->name ?? '—',
            'manufacturer' => $order->manufacturer->business_name ?? $order->manufacturer->name,
            'total'        => $order->total_amount,
            'paid'         => $order->paid_amount,
            'balance'      => $order->total_amount - $order->paid_amount,
            'status'       => $order->status,
        ])->values();

        return view('shop-owner.payments.index', compact(
            'orders', 'transactions', 'orderBalances',
            'totalOrderValue', 'totalPaid', 'pendingBalance'
        ));
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