<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of all users (Freelancers and Clients).
     * Accessible only by Admin.
     */
    public function index(Request $request)
    {
        // Guard: Only admins can list users
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return UserResource::collection($users);
    }

    /**
     * Remove the specified user.
     * Accessible only by Admin.
     */
    public function destroy(Request $request, User $user)
    {
        // Guard: Only admins can delete users
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        // Prevention: Admin cannot delete themselves
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot delete your own admin account.'], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
