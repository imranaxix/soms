<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopOwnerController extends Controller
{
    public function dashboard()
    {
        // Simple array of orders
        $orders = [
            ['id' => 'ORD-001', 'productName' => 'Sweater - Sleeveless', 'manufacturerName' => 'XYZ Manufacturing', 'quantity' => 100, 'dueDate' => '2024-05-15', 'status' => 'In Progress', 'totalAmount' => 100000, 'totalPaid' => 50000, 'productionProgress' => 50],
            ['id' => 'ORD-002', 'productName' => 'T Shirt - V Neck', 'manufacturerName' => 'XYZ Manufacturing', 'quantity' => 50, 'dueDate' => '2024-05-20', 'status' => 'Pending', 'totalAmount' => 50000, 'totalPaid' => 0, 'productionProgress' => 0],
            ['id' => 'ORD-003', 'productName' => 'T Shirt - Round Neck', 'manufacturerName' => 'XYZ Manufacturing', 'quantity' => 50, 'dueDate' => '2024-05-10', 'status' => 'Completed', 'totalAmount' => 50000, 'totalPaid' => 50000, 'productionProgress' => 100],
        ];

        // Initialize all stats to 0 first
        $totalOrders = 0;
        $pending = 0;
        $inProgress = 0;
        $completed = 0;
        $totalAmount = 0;
        $totalPaid = 0;

        // Use a basic loop to calculate everything (fresher style)
        foreach ($orders as $order) {
            $totalOrders++;
            $totalAmount = $totalAmount + $order['totalAmount'];
            $totalPaid = $totalPaid + $order['totalPaid'];

            if ($order['status'] == 'Pending') {
                $pending++;
            }
            if ($order['status'] == 'In Progress') {
                $inProgress++;
            }
            if ($order['status'] == 'Completed') {
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
        return view('shop-owner.orders.index');
    }

    public function createOrder()
    {
        return view('shop-owner.orders.create');
    }

    public function manufacturers()
    {
        return view('shop-owner.manufacturers.index');
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
        return view('shop-owner.reports.index');
    }
}