<?php
namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();
        return response()->json(['cities' => $cities]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:cities,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $city = new City();
        $city->name = $request->input('name');
        $city->save();

        return response()->json([
            'success' => true,
            'city' => $city,
            'message' => 'City added successfully'
        ]);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        if (!$city) {
            return response()->json([
                'success' => false,
                'error' => 'City not found'
            ], 404);
        }

        $city->delete();
        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully'
        ]);
    }
}
