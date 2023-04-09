<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryRequestCollection;
use App\Http\Resources\DeliveryRequestResource;
use App\Models\City;
use App\Models\DeliveryRequest;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeliveryRequestController extends Controller
{
    /**
     * Получить все записи.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $deliveryRequests = DeliveryRequest::all();
            return response()->json(new DeliveryRequestCollection($deliveryRequests));
        }

        return response()->json(new DeliveryRequestCollection($user->deliveryRequests()->get()));
    }

    /**
     * Добавить новую запись.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $format = 'Y-m-d';
        $from_city_id = $request->input('from_city_id');
        $to_city_id = $request->input('to_city_id');
        $delivery_date = $request->input('delivery_date');
        $deliveryDateTest = DateTime::createFromFormat($format, $delivery_date);

        if ($from_city_id === $to_city_id)
            return response()->json([
                'success' => false,
                'error' => 'from city and to city equal'
            ], 400);

        if (!$deliveryDateTest || $deliveryDateTest->format($format) !== $delivery_date) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid date format'
            ], 400);
        }

        if (!City::where('id', $from_city_id)->exists() || !City::where('id', $to_city_id)->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'One or both cities do not exist'
            ], 400);
        }

        $deliveryRequests = DeliveryRequest::where('from_city_id', $from_city_id)
            ->where('to_city_id', $to_city_id)
            ->where('delivery_date', $delivery_date)
            ->get();

        $group = null;
        if ($deliveryRequests->count() > 0) {
            $maxGroup = DeliveryRequest::max('group') ?? 0;
            $oldGroup = $deliveryRequests->first()->group;
            $group = $oldGroup ?? $maxGroup + 1;

            if (!$oldGroup)
                DeliveryRequest::where('from_city_id', $from_city_id)
                    ->where('to_city_id', $to_city_id)
                    ->where('delivery_date', $delivery_date)
                    ->update(['group' => $group]);

        }
        $user = Auth::user();

        if (DeliveryRequest::where('from_city_id', $from_city_id)
            ->where('to_city_id', $to_city_id)
            ->where('user_id', $user->id)
            ->where('delivery_date', $delivery_date)
            ->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Delivery request with this parameters already exists'
            ], 400);
        }

        $deliveryRequest = new DeliveryRequest();

        $deliveryRequest->from_city_id = $from_city_id;
        $deliveryRequest->to_city_id = $to_city_id;
        $deliveryRequest->user_id = $user->id;
        $deliveryRequest->delivery_date = $delivery_date;
        $deliveryRequest->status = 'pending';
        $deliveryRequest->group = $group;
        $deliveryRequest->save();
        return response()->json([
            'success' => true,
            'deliveryRequest' => new DeliveryRequestResource($deliveryRequest)
        ]);
    }

    public function update(Request $request, $id)
    {
        $deliveryRequest = DeliveryRequest::find($id);

        if (!$deliveryRequest) {
            return response()->json([
                'success' => false,
                'error' => 'Delivery request not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:pending,rejected,approved'
        ]);

        $deliveryRequest->status = $validatedData['status'];

        $deliveryRequest->update();
        return response()->json(new DeliveryRequestResource($deliveryRequest));
    }

    /**
     * Удалить конкретную запись по ID.
     *
     * @param DeliveryRequest $deliveryRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $deliveryRequest = DeliveryRequest::find($id);
        if (!$deliveryRequest) {
            return response()->json(['error' => 'deliveryRequest not found'], 404);
        }

        $deliveryRequest->delete();
        return response()->json(['message' => 'deliveryRequest deleted successfully']);
    }
}
