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
        $name = $request->query('name');
        $whatsapp = $request->query('whatsapp');

        $users = User::with('role:id,name')
            ->where('whatsapp', 'like', "%{$whatsapp}%")
            ->where('name', 'like', "%{$name}%")
            ->paginate(5, [
                'id',
                'name',
                'whatsapp',
                'role',
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
