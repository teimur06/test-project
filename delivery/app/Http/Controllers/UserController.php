<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function updateIsAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.user_id' => 'required|integer',
            '*.is_admin' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $current_user = Auth::user();
        foreach ($request->all() as $item) {
            if ($current_user->id === $item['user_id']) break;
            $user = User::find($item['user_id']);
            if ($user) {
                $user->is_admin = $item['is_admin'];
                $user->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Users updated successfully'
        ]);
    }

}
