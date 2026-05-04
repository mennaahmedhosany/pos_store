<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateItemRequest;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\CalculateItemResource;
use App\Models\Cupsize;
use App\Models\Extra;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\WaterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{


    public function index()
    {
        return view('pos.index', [
            'cupSizes'   => Cupsize::active()->get(),
            'waterTypes' => WaterType::active()->get(),
            'extras'     => Extra::active()->get(),
        ]);
    }


    public function calculateItem(CalculateItemRequest $request)
    {
        $data = $request->validated();

        $cup   = CupSize::findOrFail($data['cup_size_id']);
        $water = WaterType::findOrFail($data['water_type_id']);

        $extras = Extra::whereIn('id', $data['extra_ids'] ?? [])->get();

        $extrasPrice = $extras->sum('price');
        $unitPrice   = $cup->price + $water->price + $extrasPrice;
        $quantity    = $data['quantity'];
        $lineTotal   = $unitPrice * $quantity;


        return new CalculateItemResource($cup, $water, $extras, $extrasPrice, $unitPrice, $quantity, $lineTotal);
    }


    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {


            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'status'       => 'completed',
                'notes'        => $data['notes'] ?? null,
                'total'        => 0,
            ]);

            $orderTotal = 0;

            foreach ($data['items'] as $itemData) {

                $cup   = CupSize::findOrFail($itemData['cup_size_id']);
                $water = WaterType::findOrFail($itemData['water_type_id']);

                $extras = Extra::whereIn('id', $itemData['extra_ids'] ?? [])->get();

                $extrasPrice = $extras->sum('price');
                $unitPrice   = $cup->price + $water->price + $extrasPrice;
                $quantity    = $itemData['quantity'];

                $lineTotal = $unitPrice * $quantity;

                $item = OrderItem::create([
                    'order_id'       => $order->id,
                    'cup_size_id'    => $cup->id,
                    'water_type_id'  => $water->id,
                    'quantity'       => $quantity,
                    'unit_price'     => $unitPrice,
                    'extras_price'   => $extrasPrice,
                    'total_price'    => $lineTotal,
                ]);

                foreach ($extras as $extra) {
                    $item->extras()->attach($extra->id, [
                        'price_at_time' => $extra->price
                    ]);
                }

                $orderTotal += $lineTotal;
            }

            $order->update([
                'total' => $orderTotal
            ]);

            return response()->json([
                'success' => true,
                'order'   => $order->load('items.extras'),
                'message' => 'Order created successfully',
            ]);
        });
    }


    public function orders()
    {
        return view('pos.orders', [
            'orders' => Order::with('items.extras')->latest()->paginate(20),
        ]);
    }


    public function show(Order $order)
    {
        return view('pos.show', [
            'order' => $order->load('items.cupSize', 'items.waterType', 'items.extras'),
        ]);
    }
}
