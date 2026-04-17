<?php
namespace App\Http\Controllers;

use App\Http\Requests\Order\RateProviderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderRresource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'description' => ['required', 'string'],
            'phone_user' => ['required', 'string']
        ]);

        $provider = User::where('id', $id)->isProvider()->first();
        if (!$provider) {
            return response()->json(["status" => "error", "message" => "Provider not found"], 404);
        }

        Order::create([
            "user_id" => Auth::id(),
            "provider_id" => $provider->id,
            "status" => "active",
            'phone_user' => $request->phone,
            'description' => $request->description
        ]);

        return response()->json(["status" => "success", "message" => "Order created successfully"], 201);
    }

    public function getOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', '=', $user->id)
            ->with(['user', 'provider'])
            ->paginate(10)
            ->withQueryString();

        return response()->json(
            [
                "message" => "List of providers",
                "data" => [
                    "providers" => new OrderRresource($orders),
                ],
                "pagination" => $orders->linkCollection(),
            ],
            200
        );
    }
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

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
