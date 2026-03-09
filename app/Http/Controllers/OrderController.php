<?php 
namespace App\Http\Controllers;

use App\Http\Requests\Order\RateProviderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(string $id): JsonResponse
    {
        $provider = User::where('id', $id)->isProvider()->first();
        if (!$provider) {
            return response()->json(["status" => "error", "message" => "Provider not found"], 404);
        }

        Order::create([
            "user_id"     => Auth::id(),
            "provider_id" => $provider->id,
            "status"      => "active",
        ]);

        return response()->json(["status" => "success", "message" => "Order created successfully"], 201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update',  $order);

        $order->update(['status' => $request->status]);

        return response()->json(["status" => "success", "message" => "Order status updated successfully"]);
    }

    public function rate(RateProviderRequest $request, User $provider, Order $order): JsonResponse
    {
        $this->authorize('rate', $order);
        $request->validate([
            'rate' => ['required', 'integer', 'min:1', 'max:5']
        ]);
        if ($order->provider_id !== $provider->id) {
            return response()->json(["status" => "error", "message" => "Order does not belong to this provider"], 400);
        }

        DB::transaction(function () use ($request, $provider) {
            $ordersCount = Order::where('provider_id', $provider->id)->count();
            $newRate = (($provider->rate * ($ordersCount - 1)) + $request->rate) / $ordersCount;
            
            $provider->update(['rate' => $newRate]);
        });

        return response()->json(["status" => "success", "message" => "Rating updated successfully"]);
    }
}