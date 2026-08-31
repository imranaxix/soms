<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'name'          => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                \Storage::disk('public')->delete($user->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function deleteProfileImage()
    {
        $user = auth()->user();

        if ($user->profile_image) {
            \Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return back()->with('success', 'Profile picture removed.');
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($user->profile_image) {
            \Storage::disk('public')->delete($user->profile_image);
        }

        $user->update([
            'profile_image' => $request->file('profile_image')->store('profile-images', 'public'),
        ]);

        return back()->with('success', 'Profile picture updated.');
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
            'unit' => 'required|string|in:pieces,meters,kilograms',
            'due_date' => 'required|date|after:today',
            'total_amount' => 'required|numeric|min:0',
            'payment_terms' => 'required|string',
            'special_instructions' => 'nullable|string',
        ]);

        // Ensure the shop owner has an accepted connection with the manufacturer
        $isConnected = auth()->user()->manufacturerConnections()
            ->where('manufacturer_id', $request->manufacturer_id)
            ->where('status', 'accepted')
            ->exists();

        if (!$isConnected) {
            return back()->withErrors(['manufacturer_id' => 'You are not connected with this manufacturer.'])->withInput();
        }

        // Ensure the product belongs to the manufacturer
        $product = Product::where('id', $request->product_id)
            ->where('user_id', $request->manufacturer_id)
            ->first();

        if (!$product) {
            return back()->withErrors(['product_id' => 'This product does not belong to the selected manufacturer.'])->withInput();
        }

        // Ensure the variant belongs to the product
        $variant = ProductVariant::where('id', $request->variant_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$variant) {
            return back()->withErrors(['variant_id' => 'This variant does not belong to the selected product.'])->withInput();
        }

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
        $order = Order::where('shop_owner_id', auth()->id())
            ->with(['manufacturer', 'product', 'variant', 'stages'])
            ->findOrFail($id);

        $latestPayment = $order->payments()->latest()->first();
        if ($latestPayment && $latestPayment->status === 'pending'
            && $latestPayment->created_at->diffInSeconds(now()) > 60) {
            $latestPayment->update([
                'status'                   => 'failed',
                'gateway_response_code'    => 'TIMEOUT',
                'gateway_response_message' => 'Payment timed out. No confirmation received within 1 minute.',
            ]);
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
                'gateway_response_code' => $p->gateway_response_code,
                'amount'           => $p->amount,
                'status'           => $p->status,
            ]);
        })->sortByDesc('date')->values();

        // Per-order balance rows
        $orderBalances = $orders->map(fn($order) => [
            'order_number'  => $order->order_number,
            'order_id'      => $order->id,
            'product'       => $order->product->name ?? '—',
            'manufacturer'  => $order->manufacturer->business_name ?? $order->manufacturer->name,
            'payment_terms' => str_replace('_', ' ', $order->payment_terms),
            'total'         => $order->total_amount,
            'paid'          => $order->paid_amount,
            'balance'       => $order->total_amount - $order->paid_amount,
            'status'        => $order->status,
        ])->values();

        return view('shop-owner.payments.index', compact(
            'orders', 'transactions', 'orderBalances',
            'totalOrderValue', 'totalPaid', 'pendingBalance'
        ));
    }

    public function reports()
    {
        $userId = auth()->id();
        $period = request('period', 'all');

        $allOrders = Order::where('shop_owner_id', $userId);

        if ($period === '30days') {
            $allOrders->where('created_at', '>=', now()->subDays(30));
        } elseif ($period === 'this_month') {
            $allOrders->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
        } elseif ($period === 'this_year') {
            $allOrders->whereYear('created_at', now()->year);
        }

        $allOrders = $allOrders->get();

        $totalSpend = $allOrders->sum('total_amount');
        $totalPaid = $allOrders->sum('paid_amount');
        $pendingLiabilities = $totalSpend - $totalPaid;
        $ordersFulfilled = $allOrders->where('status', 'Completed')->count();

        $topManufacturer = $allOrders->groupBy('manufacturer_id')
            ->map(fn($orders) => $orders->count())
            ->sortDesc()
            ->keys()
            ->first();

        $topManufacturerName = $topManufacturer
            ? (\App\Models\User::find($topManufacturer)->business_name ?? \App\Models\User::find($topManufacturer)->name ?? '—')
            : '—';

        $stats = [
            'totalSpend' => $totalSpend,
            'pendingLiabilities' => $pendingLiabilities,
            'ordersFulfilled' => $ordersFulfilled,
            'topManufacturer' => $topManufacturerName,
        ];

        // Spending trends — last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        $spendingLabels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        $spendingData = $months->map(function ($m) use ($userId) {
            return (int) Order::where('shop_owner_id', $userId)
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('total_amount');
        })->toArray();

        // Order status distribution
        $statusCounts = $allOrders->groupBy('status')->map->count();
        $distributionLabels = ['Pending', 'In Progress', 'Completed', 'Rejected'];
        $distributionData = [
            $statusCounts->get('Pending', 0),
            $statusCounts->get('In Progress', 0),
            $statusCounts->get('Completed', 0),
            $statusCounts->get('Rejected', 0),
        ];

        $chartData = [
            'spending' => [
                'labels' => $spendingLabels,
                'data' => $spendingData,
            ],
            'distribution' => [
                'labels' => $distributionLabels,
                'data' => $distributionData,
            ],
        ];

        // Recent transactions from completed payments
        $transactionsQuery = Payment::where('payer_id', $userId)
            ->where('status', 'completed')
            ->with('order.manufacturer');

        if ($period === '30days') {
            $transactionsQuery->where('paid_at', '>=', now()->subDays(30));
        } elseif ($period === 'this_month') {
            $transactionsQuery->whereMonth('paid_at', now()->month)
                              ->whereYear('paid_at', now()->year);
        } elseif ($period === 'this_year') {
            $transactionsQuery->whereYear('paid_at', now()->year);
        }

        $transactions = $transactionsQuery->latest('paid_at')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->txn_ref_no,
                'date' => $p->paid_at?->format('M d, Y') ?? $p->created_at->format('M d, Y'),
                'manufacturer' => $p->order->manufacturer->business_name ?? $p->order->manufacturer->name ?? '—',
                'method' => $p->stripe_payment_intent_id ? 'Stripe' : ($p->safepay_tracker_id ? 'Safepay' : '—'),
                'status' => 'Paid',
                'amount' => $p->amount,
            ])->toArray();

        return view('shop-owner.reports.index', compact('stats', 'chartData', 'transactions', 'period'));
    }
}