<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManufacturerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $baseQuery = \App\Models\Order::where('manufacturer_id', $user->id);
        
        $stats = [
            'totalOrders'    => $baseQuery->clone()->count(),
            'pendingApproval'=> $baseQuery->clone()->where('status', 'Pending')->count(),
            'inProduction'   => $baseQuery->clone()->where('status', 'In Progress')->count(),
            'delivered'      => $baseQuery->clone()->where('status', 'Delivered')->count(),
            'completed'      => $baseQuery->clone()->where('status', 'Completed')->count(),
            'cancelled'      => $baseQuery->clone()->where('status', 'Cancelled')->count(),
            'totalRevenue'   => (int) $baseQuery->clone()->whereIn('status', ['In Progress', 'Delivered', 'Completed'])->sum('total_amount'),
            'receivedPayment'=> (int) $baseQuery->clone()->sum('paid_amount'),
            'pendingPayment' => (int) $baseQuery->clone()->whereIn('status', ['In Progress', 'Delivered', 'Completed'])->sum('total_amount') - (int) $baseQuery->clone()->sum('paid_amount'),
        ];

        $pendingOrders = \App\Models\Order::where('manufacturer_id', $user->id)
            ->where('status', 'Pending')
            ->with(['shopOwner', 'product', 'variant'])
            ->latest()
            ->get();

        $activeOrders = \App\Models\Order::where('manufacturer_id', $user->id)
            ->where('status', 'In Progress')
            ->with(['shopOwner', 'product', 'variant'])
            ->latest()
            ->get();

        // Revenue trends - last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }
        $revenueLabels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        $revenueData = $months->map(function ($m) use ($user) {
            return (int) Order::where('manufacturer_id', $user->id)
                ->whereIn('status', ['In Progress', 'Delivered', 'Completed'])
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('total_amount');
        })->toArray();

        // Order status distribution
        $allOrders = Order::where('manufacturer_id', $user->id)->get();
        $statusCounts = $allOrders->groupBy('status')->map->count();
        $distributionLabels = ['Pending', 'In Progress', 'Completed', 'Rejected'];
        $distributionData = [
            $statusCounts->get('Pending', 0),
            $statusCounts->get('In Progress', 0),
            $statusCounts->get('Completed', 0),
            $statusCounts->get('Rejected', 0),
        ];
        $distributionTotal = array_sum($distributionData);

        return view('manufacturer.dashboard', compact('stats', 'pendingOrders', 'activeOrders', 'distributionTotal'))
            ->with('revenueLabels', $revenueLabels)
            ->with('revenueData', $revenueData)
            ->with('distributionLabels', $distributionLabels)
            ->with('distributionData', $distributionData)
            ->with('distributionTotal', $distributionTotal);
    }

    public function orders()
    {
        $orders = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->with(['shopOwner', 'product', 'variant'])
            ->latest()
            ->get();

        return view('manufacturer.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = \App\Models\Order::with(['shopOwner', 'product.stages', 'variant', 'stages'])
            ->where('manufacturer_id', Auth::id())
            ->findOrFail($id);

        if ($order->status === 'In Progress' && $order->stages->isEmpty() && $order->product && $order->product->stages->isNotEmpty()) {
            foreach ($order->product->stages as $productStage) {
                $order->stages()->create([
                    'name'        => $productStage->name,
                    'description' => $productStage->description,
                    'sort_order'  => $productStage->sort_order,
                    'status'      => 'pending',
                ]);
            }
            $order->load('stages');
        }

        return view('manufacturer.orders.show', compact('order'));
    }

    public function acceptOrder($id)
    {
        $order = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->where('status', 'Pending')
            ->with('product.stages')
            ->findOrFail($id);

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'In Progress',
                'progress_percent' => 0,
            ]);

            // Copy product stages to order stages
            if ($order->product && $order->product->stages->isNotEmpty()) {
                foreach ($order->product->stages as $productStage) {
                    $order->stages()->create([
                        'name'        => $productStage->name,
                        'description' => $productStage->description,
                        'sort_order'  => $productStage->sort_order,
                        'status'      => 'pending',
                    ]);
                }
            }
        });

        // Notify the shop owner that their order was accepted
        $order->shopOwner->notify(new \App\Notifications\OrderStatusChanged($order, Auth::user(), 'In Progress'));

        return back()->with('success', 'Order ' . $order->order_number . ' has been accepted and is now In Progress.');
    }

    public function toggleStage($id, $stageId)
    {
        $order = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->where('status', 'In Progress')
            ->with('stages')
            ->findOrFail($id);

        $stage = $order->stages()->findOrFail($stageId);

        // Toggle the stage with cascading updates
        if ($stage->status === 'pending') {
            // Mark this and all preceding stages as completed
            $order->stages()
                ->where('sort_order', '<=', $stage->sort_order)
                ->where('status', 'pending')
                ->update([
                    'status'       => 'completed',
                    'completed_at' => now(),
                ]);
        } else {
            // Mark this and all subsequent stages as pending (undo cascade)
            $order->stages()
                ->where('sort_order', '>=', $stage->sort_order)
                ->where('status', 'completed')
                ->update([
                    'status'       => 'pending',
                    'completed_at' => null,
                ]);
        }

        // Recalculate progress
        $order->load('stages');
        $total     = $order->stages->count();
        $completed = $order->stages->where('status', 'completed')->count();
        $progress  = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        // Re-fetch order to get current status (may have changed since initial load)
        $order->refresh();

        // Auto-deliver the order when all stages are done
        if ($progress === 100) {
            $order->update(['status' => 'Delivered', 'progress_percent' => 100]);
            $order->shopOwner->notify(new \App\Notifications\OrderStatusChanged($order, Auth::user(), 'Delivered'));
        } else {
            // If they undo a stage from a delivered order, revert it back to In Progress
            if ($order->status === 'Delivered' && $progress < 100) {
                $order->update(['status' => 'In Progress', 'progress_percent' => $progress]);
            } else {
                $order->update(['progress_percent' => $progress]);
            }
            // Notify shop owner of stage update
            $order->shopOwner->notify(new \App\Notifications\OrderStatusChanged($order, Auth::user(), 'Stage Updated'));
        }

        return back()->with('success', 'Stage "' . $stage->name . '" updated.');
    }

    public function rejectOrder($id)
    {
        $order = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->where('status', 'Pending')
            ->findOrFail($id);

        $order->update([
            'status' => 'Rejected',
            'progress_percent' => 0,
        ]);

        // Notify the shop owner that their order was rejected
        $order->shopOwner->notify(new \App\Notifications\OrderStatusChanged($order, Auth::user(), 'Rejected'));

        return back()->with('success', 'Order ' . $order->order_number . ' has been rejected.');
    }

    public function cancelAcceptedOrder($id)
    {
        $order = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->whereIn('status', ['In Progress', 'Delivered'])
            ->with('shopOwner')
            ->withCount(['payments' => fn($q) => $q->where('status', 'completed')])
            ->findOrFail($id);

        $order->update([
            'status'           => 'Cancelled',
            'progress_percent' => 0,
        ]);

        // Also reset all order stages back to pending
        $order->stages()->update(['status' => 'pending', 'completed_at' => null]);

        // Notify the shop owner
        $order->shopOwner->notify(new \App\Notifications\OrderStatusChanged($order, Auth::user(), 'Cancelled'));

        $message = 'Order ' . $order->order_number . ' has been cancelled.';
        if ($order->payments_count > 0) {
            $message .= ' Note: This order has existing payments that will need to be refunded separately.';
        }

        return back()->with('success', $message);
    }

    public function catalog()
    {
        $products = Auth::user()->products()->with('variants')->get();

        return view('manufacturer.catalog.index', compact('products'));
    }

    public function createProduct()
    {
        $products = Auth::user()->products()->with('variants')->get();

        return view('manufacturer.catalog.create', compact('products'));
    }

    public function storeProduct(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'variations' => 'required|array|min:1',
        'variations.*.name' => 'required|string|max:255',
        'variations.*.price' => 'nullable|numeric|min:0',
        'variations.*.stock_quantity' => 'nullable|integer|min:0',
        'variations.*.image' => 'nullable|image|max:2048',
        'stages' => 'required|array|min:1',
        'stages.*.name' => 'required|string|max:255',
        'stages.*.description' => 'nullable|string',
    ]);

    $product = Auth::user()->products()->create([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    foreach ($request->variations as $variationData) {
        $imagePath = null;
        if (isset($variationData['image'])) {
            $imagePath = $variationData['image']->store('variants', 'public');
        }

        // Auto SKU GENERATION
        $productPrefix = strtoupper(Str::slug(substr($product->name, 0, 3), ''));
        $variantPrefix = strtoupper(Str::slug(substr($variationData['name'], 0, 3), ''));
        
        
        do {
            $sku = $productPrefix . '-' . $variantPrefix . '-' . strtoupper(Str::random(6));
        } while (\App\Models\ProductVariant::where('sku', $sku)->exists());

        $product->variants()->create([
            'variant_name' => trim($variationData['name']),
            'sku' => $sku, 
            'price' => $variationData['price'] ?? 0,
            'stock_quantity' => $variationData['stock_quantity'] ?? 0,
            'image' => $imagePath,
        ]);
    }
    if ($request->has('stages')) {
        foreach ($request->stages as $index => $stageData) {
            $product->stages()->create([
                'name' => trim($stageData['name']),
                'description' => $stageData['description'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    return redirect()->route('manufacturer.catalog.index')->with('success', 'Product created successfully');
}

    public function editProduct($id)
    {
        $product = Auth::user()->products()->with(['variants', 'stages'])->findOrFail($id);
        $products = Auth::user()->products()->with('variants')->get();

        return view('manufacturer.catalog.edit', compact('product', 'products'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Auth::user()->products()->with(['variants', 'stages'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'variations' => 'required|array|min:1',
            'variations.*.name' => 'required|string|max:255',
            'variations.*.price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.image' => 'nullable|image|max:2048',
            'stages' => 'required|array|min:1',
            'stages.*.name' => 'required|string|max:255',
            'stages.*.description' => 'nullable|string',
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('variations')) {
            foreach ($request->variations as $variationData) {
                $variantDataToSave = [
                    'variant_name' => trim($variationData['name']),
                    'price' => $variationData['price'] ?? 0,
                    'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                ];

                if (isset($variationData['image']) && $variationData['image']) {
                    $variantDataToSave['image'] = $variationData['image']->store('variants', 'public');
                }

                if (isset($variationData['id']) && $variationData['id']) {
                    $product->variants()->where('id', $variationData['id'])->update($variantDataToSave);
                } else {
                    $productPrefix = strtoupper(Str::slug(substr($product->name, 0, 3), ''));
                    $variantPrefix = strtoupper(Str::slug(substr($variationData['name'], 0, 3), ''));
                    do {
                        $sku = $productPrefix . '-' . $variantPrefix . '-' . strtoupper(Str::random(6));
                    } while (\App\Models\ProductVariant::where('sku', $sku)->exists());

                    $variantDataToSave['sku'] = $sku;
                    $product->variants()->create($variantDataToSave);
                }
            }
        }

        if ($request->has('stages')) {
            $providedStageIds = collect($request->stages)->pluck('id')->filter()->toArray();
            $product->stages()->whereNotIn('id', $providedStageIds)->delete();

            foreach ($request->stages as $index => $stageData) {
                $stageDataToSave = [
                    'name' => trim($stageData['name']),
                    'description' => $stageData['description'] ?? null,
                    'sort_order' => $index,
                ];

                if (isset($stageData['id']) && $stageData['id']) {
                    $product->stages()->where('id', $stageData['id'])->update($stageDataToSave);
                } else {
                    $product->stages()->create($stageDataToSave);
                }
            }
        }

        return redirect()->route('manufacturer.catalog.index')->with('success', 'Product updated successfully');
    }

    public function showProduct($id)
    {
        $product = Auth::user()->products()->with('variants')->findOrFail($id);

        return view('manufacturer.catalog.show', compact('product'));
    }

    public function destroyProduct($id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully');
    }

    public function production()
    {
        $orders = \App\Models\Order::where('manufacturer_id', Auth::id())
            ->where('status', 'In Progress')
            ->with(['shopOwner', 'product.stages', 'variant', 'stages'])
            ->latest()
            ->get();

        // For any In Progress order that has no order_stages yet,
        // copy from the product's stages (handles orders accepted before this feature was added)
        foreach ($orders as $order) {
            if ($order->stages->isEmpty() && $order->product && $order->product->stages->isNotEmpty()) {
                foreach ($order->product->stages as $productStage) {
                    $order->stages()->create([
                        'name'        => $productStage->name,
                        'description' => $productStage->description,
                        'sort_order'  => $productStage->sort_order,
                        'status'      => 'pending',
                    ]);
                }
                // Reload stages after seeding
                $order->load('stages');
            }
        }

        return view('manufacturer.production.index', compact('orders'));
    }

    public function payments()
    {
        $user = Auth::user();

        // All orders for this manufacturer with their completed payments
        $allOrders = \App\Models\Order::where('manufacturer_id', $user->id)
            ->whereIn('status', ['In Progress', 'Delivered', 'Completed'])
            ->with(['payments' => fn($q) => $q->where('status', 'completed')->latest(), 'shopOwner', 'product'])
            ->latest()
            ->get();

        // Aggregate stats from real data
        $stats = [
            'totalOrderValue' => $allOrders->sum('total_amount'),
            'totalPaid'       => $allOrders->sum('paid_amount'),
            'pendingBalance'  => $allOrders->sum('total_amount') - $allOrders->sum('paid_amount'),
        ];

        // Flat list of completed transactions
        $transactions = $allOrders->flatMap(function ($order) {
            return $order->payments->map(fn($p) => [
                'date'          => $p->paid_at ?? $p->created_at,
                'order_number'  => $order->order_number,
                'order_id'      => $order->id,
                'received_from' => $order->shopOwner->business_name ?? $order->shopOwner->name,
                'txn_ref_no'    => $p->txn_ref_no,
                'method'        => $p->safepay_tracker_id ? 'Safepay' : ($p->stripe_payment_intent_id ? 'Stripe' : '—'),
                'amount'        => $p->amount,
            ]);
        })->sortByDesc('date')->values();

        // Per-order balance breakdown
        $orderBalances = $allOrders->map(fn($order) => [
            'order_number' => $order->order_number,
            'order_id'     => $order->id,
            'product'      => $order->product->name ?? '—',
            'shop_owner'   => $order->shopOwner->business_name ?? $order->shopOwner->name,
            'total'        => $order->total_amount,
            'paid'         => $order->paid_amount,
            'balance'      => $order->total_amount - $order->paid_amount,
            'status'       => $order->status,
        ])->values();

        return view('manufacturer.payments.index', compact('stats', 'transactions', 'orderBalances'));
    }

    public function connections()
    {
        $user = auth()->user();
        $pendingRequests = $user->connections()->where('status', 'pending')->where('initiated_by', '!=', $user->id)->get();
        $activeConnections = $user->connections()->where('status', 'accepted')->get();

        return view('manufacturer.connections.index', compact('pendingRequests', 'activeConnections'));
    }

    public function reports()
    {
        $userId = auth()->id();

        $allOrders = Order::where('manufacturer_id', $userId)->get();

        $totalRevenue = $allOrders->whereIn('status', ['In Progress', 'Delivered', 'Completed'])->sum('total_amount');
        $totalPaid = $allOrders->sum('paid_amount');
        $pendingReceivables = $totalRevenue - $totalPaid;
        $ordersFulfilled = $allOrders->where('status', 'Completed')->count();

        $topCustomer = $allOrders->groupBy('shop_owner_id')
            ->map(fn($orders) => $orders->count())
            ->sortDesc()
            ->keys()
            ->first();

        $topCustomerName = $topCustomer
            ? (\App\Models\User::find($topCustomer)->business_name ?? \App\Models\User::find($topCustomer)->name ?? '—')
            : '—';

        $stats = [
            'totalRevenue' => $totalRevenue,
            'pendingReceivables' => $pendingReceivables,
            'ordersFulfilled' => $ordersFulfilled,
            'topCustomer' => $topCustomerName,
        ];

        // Revenue trends — last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        $revenueLabels = $months->map(fn($m) => $m->format('M Y'))->toArray();
        $revenueData = $months->map(function ($m) use ($userId) {
            return (int) Order::where('manufacturer_id', $userId)
                ->whereIn('status', ['In Progress', 'Delivered', 'Completed'])
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
            'revenue' => [
                'labels' => $revenueLabels,
                'data' => $revenueData,
            ],
            'distribution' => [
                'labels' => $distributionLabels,
                'data' => $distributionData,
            ],
        ];

        // Recent transactions from completed payments received
        $transactions = Payment::where('payee_id', $userId)
            ->where('status', 'completed')
            ->with('order.shopOwner')
            ->latest('paid_at')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id' => $p->txn_ref_no,
                'date' => $p->paid_at?->format('M d, Y') ?? $p->created_at->format('M d, Y'),
                'partner' => $p->order->shopOwner->business_name ?? $p->order->shopOwner->name ?? '—',
                'method' => $p->stripe_payment_intent_id ? 'Stripe' : ($p->safepay_tracker_id ? 'Safepay' : '—'),
                'status' => 'Paid',
                'amount' => $p->amount,
            ])->toArray();

        return view('manufacturer.reports.index', compact('stats', 'chartData', 'transactions'));
    }
    public function profile()
    {
        $user = auth()->user();
        return view('manufacturer.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'business_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $user->update($request->only('business_name', 'name'));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function paymentMethods()
    {
        $user = auth()->user();
        return view('manufacturer.payment-methods.index', compact('user'));
    }
}
