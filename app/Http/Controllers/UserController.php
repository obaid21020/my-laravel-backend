<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Authorization;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   

    public function getAllUsers(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            // Check if user has authorization level 1 (highest authority)
            if (!$currentUser || $currentUser->authorization_level !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only level 1 administrators can view users.'
                ], 403);
            }

            $users = User::select('id', 'name', 'email', 'authorization_level', 'created_at')
                        ->orderBy('authorization_level', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(Request $request, $userId)
    {
        try {
            $currentUser = Auth::user();
            
            // Check if current user has authorization level 1
            if (!$currentUser || $currentUser->authorization_level !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only level 1 administrators can delete users.'
                ], 403);
            }

            $userToDelete = User::find($userId);
            
            if (!$userToDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Prevent deletion of users with equal or higher authorization
            if ($userToDelete->authorization_level <= $currentUser->authorization_level && $userToDelete->id !== $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with equal or higher authorization level'
                ], 403);
            }

            // Prevent self-deletion
            if ($userToDelete->id === $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 403);
            }

            $userName = $userToDelete->name;
            $userToDelete->delete();

            return response()->json([
                'success' => true,
                'message' => "User '{$userName}' deleted successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    


    public function getUserAuthorization($userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $authorization = Authorization::find($user->authorization_level);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'authorization_level' => $user->authorization_level,
                ],
                'permissions' => $authorization
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user authorization',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
