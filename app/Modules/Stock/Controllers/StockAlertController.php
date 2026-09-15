<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function index(Request $request)
    {
        $items = StockItem::with(['location', 'createdBy'])
            ->active()
            ->lowStock()
            ->orderByRaw('CASE WHEN quantity <= 0 THEN 0 WHEN quantity <= min_quantity THEN 1 ELSE 2 END')
            ->orderBy('name')
            ->paginate(50)
            ->withQueryString();

        $criticalCount = StockItem::active()->where('quantity', '<=', 0)->where('min_quantity', '>', 0)->count();
        $lowCount = StockItem::active()->where('quantity', '>', 0)->whereColumn('quantity', '<=', 'min_quantity')->where('min_quantity', '>', 0)->count();

        return view('stock.alerts.index', compact('items', 'criticalCount', 'lowCount'));
    }
}
