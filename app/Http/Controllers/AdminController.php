<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Admin dashboard with platform-wide stats.
     */
    public function dashboard()
    {
        $stats = [
            'users'          => User::count(),
            'shop_owners'    => User::where('role', 'shop_owner')->count(),
            'manufacturers'  => User::where('role', 'manufacturer')->count(),
            'unverified'     => User::where('role', 'manufacturer')->where('is_verified', false)->count(),
            'suspended'      => User::where('is_active', false)->count(),
            'orders'         => Order::count(),
            'pending_orders' => Order::where('status', 'Pending')->count(),
            'gmv'            => (float) Order::sum('total_amount'),
            'collected'      => (float) Order::sum('paid_amount'),
            'outstanding'    => (float) Order::all()->sum(fn($o) => $o->total_amount - $o->paid_amount),
            'payments'       => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments'  => Payment::where('status', 'failed')->count(),
        ];

        $recentUsers = User::latest()->take(8)->get();

        $recentPayments = Payment::with(['order', 'payee'])->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments'));
    }

    /**
     * List all users with optional role/status filters.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role') && in_array($request->role, ['shop_owner', 'manufacturer', 'admin'], true)) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'suspended') {
                $query->where('is_active', false);
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'unverified') {
                $query->where('is_verified', false);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('business_name', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'          => User::count(),
            'shop_owner'   => User::where('role', 'shop_owner')->count(),
            'manufacturer' => User::where('role', 'manufacturer')->count(),
            'admin'        => User::where('role', 'admin')->count(),
            'suspended'    => User::where('is_active', false)->count(),
            'unverified'   => User::where('role', 'manufacturer')->where('is_verified', false)->count(),
        ];

        return view('admin.users', compact('users', 'counts'));
    }

    /**
     * Show a single user's details.
     */
    public function showUser(User $user)
    {
        $orders = $user->role === 'manufacturer'
            ? Order::where('manufacturer_id', $user->id)->with('product')->latest()->take(10)->get()
            : Order::where('shop_owner_id', $user->id)->with('product')->latest()->take(10)->get();

        $payments = Payment::where('payer_id', $user->id)
            ->orWhere('payee_id', $user->id)
            ->with('order')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.user-show', compact('user', 'orders', 'payments'));
    }

    /**
     * Toggle account suspension.
     */
    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be suspended.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active
            ? $user->name . ' has been activated.'
            : $user->name . ' has been suspended.';

        return back()->with('success', $message);
    }

    /**
     * Toggle verification status.
     */
    public function toggleVerified(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be unverified.');
        }

        $user->update(['is_verified' => !$user->is_verified]);

        $message = $user->is_verified
            ? $user->name . ' has been verified.'
            : $user->name . ' has been unverified.';

        return back()->with('success', $message);
    }

    /**
     * Platform-wide reports.
     */
    public function reports(Request $request)
    {
        $totalOrders = Order::count();
        $totalOrderValue = (float) Order::sum('total_amount');
        $totalPaid = (float) Order::sum('paid_amount');
        $pendingBalance = $totalOrderValue - $totalPaid;

        $paidAmount = (float) Payment::where('status', 'completed')->sum('amount');
        $failedAmount = (float) Payment::where('status', 'failed')->sum('amount');
        $pendingAmount = (float) Payment::where('status', 'pending')->sum('amount');

        // Payment method breakdown
        $methodBreakdown = Payment::selectRaw("
                CASE
                    WHEN stripe_payment_intent_id IS NOT NULL THEN 'stripe'
                    WHEN safepay_tracker_id IS NOT NULL THEN 'safepay'
                    ELSE 'unrecorded'
                END as method,
                COUNT(*) as count,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as completed_amount,
                COALESCE(SUM(amount), 0) as total_amount
            ")
            ->groupBy('method')
            ->get();

        // Status breakdown
        $statusBreakdown = Payment::selectRaw('status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('status')
            ->get();

        // Monthly volume trend (last 6 months)
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        $monthlyLabels = $months->map(fn($m) => $m->format('M Y'))->values();
        $monthlyOrders = $months->map(function ($m) {
            return Order::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->count();
        })->values();
        $monthlyValue = $months->map(function ($m) {
            return (float) Order::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('total_amount');
        })->values();

        // Top manufacturers by GMV
        $topManufacturers = Order::query()
            ->selectRaw('manufacturer_id, COUNT(*) as order_count, SUM(total_amount) as gmv')
            ->with('manufacturer')
            ->groupBy('manufacturer_id')
            ->orderByDesc('gmv')
            ->take(10)
            ->get()
            ->map(fn($row) => [
                'name'      => $row->manufacturer->business_name ?? $row->manufacturer->name ?? '—',
                'orders'    => $row->order_count,
                'gmv'       => (float) $row->gmv,
            ]);

        return view('admin.reports', compact(
            'totalOrders', 'totalOrderValue', 'totalPaid', 'pendingBalance',
            'paidAmount', 'failedAmount', 'pendingAmount',
            'methodBreakdown', 'statusBreakdown',
            'monthlyLabels', 'monthlyOrders', 'monthlyValue',
            'topManufacturers'
        ));
    }
}