<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                ->orWhere('customer_email', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'items.product.images');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,waiting_for_customer_parcel,parcel_shipped_by_customer,parcel_received,resin_work_in_progress,completed',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated successfully.');
    }

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated successfully.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
