<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Card;
use Illuminate\Support\Facades\DB;

use App\Exports\SalesByCategoryExport;
use App\Exports\UserSpendingExport;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{
    // Página básica (chamado por statistics.basic)
    public function basic()
    {
        $user = auth()->user();

        if ($user->type === 'member') {
            $data = [
                'total_orders' => Order::where('member_id', $user->id)->where('status', 'completed')->count(),
                'last_order' => Order::where('member_id', $user->id)->where('status', 'completed')->latest('date')->first(),
                'total_spent' => Order::where('member_id', $user->id)->where('status', 'completed')->sum('total')
            ];
            return view('statistics.member_basic', compact('data'));
        } elseif ($user->type === 'board' || $user->type === 'employee')  {
            $data = [
                'total_users' => User::count(),
                'users_by_type' => User::select('type')->selectRaw('count(*) as total')->groupBy('type')->get(),
                'pending_members' => User::where('type', 'pending_member')->count(),
                'total_cards' => Card::count(),
                'products_available' => Product::whereNull('deleted_at')->count(),
                'products_low_stock' => Product::whereColumn('stock', '<=', 'stock_lower_limit')->count(),
                'orders_by_status' => Order::select('status')->selectRaw('count(*) as total')->groupBy('status')->get(),
                'total_sales_value' => Order::where('status', 'completed')->sum('total')
            ];
            return view('statistics.board_basic', compact('data'));
        }
        return abort(403, 'This action is unauthorized.');
    }

    public function advanced()
    {
        $user = auth()->user();

        if ($user->type === 'member') {
            $ordersData = Order::selectRaw('YEAR(orders.created_at) as year, MONTH(orders.created_at) as month, categories.name as category, SUM(items_orders.subtotal) as totalS')
                ->join('items_orders', 'orders.id', '=', 'items_orders.order_id')
                ->join('products', 'items_orders.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.member_id', $user->id)
                ->where('orders.status', 'completed')
                ->groupBy('year', 'month', 'categories.name')
                ->get();
            
            $shipping = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(shipping_cost) as totalS')
                ->where('member_id', $user->id)
                ->where('status', 'completed')
                ->groupBy('year', 'month')
                ->get();

            $frequentProducts = Product::select('products.name')
                ->selectRaw("SUM(items_orders.quantity) as total_quantity")
                ->join('items_orders', 'products.id', '=', 'items_orders.product_id')
                ->join('orders', 'items_orders.order_id', '=', 'orders.id')
                ->where('orders.member_id', $user->id)
                ->where('orders.status', 'completed')
                ->groupBy('products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            $data = [
                'orders_by_category' => $ordersData,
                'shipping' => $shipping,
                'frequent_products' => $frequentProducts
            ];

            return view('statistics.member_advanced', compact('data'));
        } elseif ($user->type === 'board' || $user->type === 'employee') {
            $salesData = Order::selectRaw('YEAR(orders.created_at) as year, MONTH(orders.created_at) as month, categories.name as category, SUM(items_orders.subtotal) as totalS')
                ->join('items_orders', 'orders.id', '=', 'items_orders.order_id')
                ->join('products', 'items_orders.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.status', 'completed')
                ->groupBy('year', 'month', 'categories.name')
                ->get();
            
            $shipping = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(shipping_cost) as totalS')
                ->where('status', 'completed')
                ->groupBy('year', 'month')
                ->get();

            $topProducts = Product::select('products.name')
                ->selectRaw("SUM(items_orders.quantity) as total_quantity")
                ->join('items_orders', 'products.id', '=', 'items_orders.product_id')
                ->join('orders', 'items_orders.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->groupBy('products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            $topSpenders = User::select('users.name')
                ->selectRaw("SUM(orders.total) as total_spent")
                ->join('orders', 'users.id', '=', 'orders.member_id')
                ->where('orders.status', 'completed')
                ->groupBy('users.name')
                ->orderByDesc('total_spent')
                ->limit(5)
                ->get();

            $data = [
                'sales_by_category' => $salesData,
                'shipping' => $shipping,
                'top_products'   => $topProducts,
                'top_spenders'   => $topSpenders
            ];
            
            return view('statistics.board_advanced', compact('data'));
        }
        return abort(403, 'This action is unauthorized.');
    }

	public function exportUserSpending()
	{
		$user = auth()->user();
		if ($user != null && $user->type === 'member') {
			$ordersByMonth = Order::selectRaw("DATE_FORMAT(date, '%Y-%m') as data, SUM(total) as total")
				->where('member_id', $user->id)
				->where('status', 'completed')
				->groupBy('data')
				->orderBy('data')
				->get();

			return Excel::download(new UserSpendingExport($ordersByMonth), 'user_spending.xlsx');
		}
        return abort(403, 'This action is unauthorized.');
	}

    public function exportSalesByCategory()
	{
		$user = auth()->user();
		if ($user != null && $user->type === 'board') {
			$salesData = Order::selectRaw("DATE_FORMAT(date, '%Y-%m') as data, categories.name as category, SUM(items_orders.subtotal) as totalS")
				->join('items_orders', 'orders.id', '=', 'items_orders.order_id')
				->join('products', 'items_orders.product_id', '=', 'products.id')
				->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('status', 'completed')
				->groupBy('data', 'category')
				->orderBy('data')
				->get();

			return Excel::download(new SalesByCategoryExport($salesData), 'sales_by_category.xlsx');
		}
        return abort(403, 'This action is unauthorized.');
	}
}