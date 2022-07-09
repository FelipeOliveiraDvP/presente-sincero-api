<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List the users.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::orWhere('name', 'like', "%{$search}%")
            ->orWhere('whatsapp', 'like', "%{$search}%")
            ->paginate(5, [
                'id',
                'name',
                'whatsapp',
                'email',
                'avatar',
                'created_at',
                'updated_at',
            ]);

        if (empty($users)) {
            return response()->json([]);
        }

        return response()->json($users);
    }
}
