<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\AuthHelper;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AuthHelper;

    /**
     * List all sellers.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = User::where('role', '=', $this->getCustomerRole())
            ->whereIn('id', function ($query) use ($search) {
                $query
                    ->select('id')
                    ->from('users')
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->get();
            })
            ->paginate(10, [
                'id',
                'name',
                'whatsapp',
                'email',
                'avatar',
                'blocked',
                'created_at',
                'updated_at',
            ]);

        if (empty($customers)) {
            return response()->json([]);
        }

        return response()->json($customers);
    }
}
