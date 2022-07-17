<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\AuthHelper;
use Illuminate\Http\Request;

class SellerController extends Controller
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

        $sellers = User::where('role', '=', $this->getSellerRole())
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
                'seller_approved',
                'blocked',
                'created_at',
                'updated_at',
            ]);

        if (empty($sellers)) {
            return response()->json([]);
        }

        return response()->json($sellers);
    }

    /**
     * Approve a seller account.
     * 
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function approveSeller(int $id)
    {
        $seller = User::find($id);

        if (empty($seller)) {
            return response()->json([
                'message' => 'O vendedor informado não existe!'
            ], 404);
        }

        if ($seller->role != $this->getSellerRole()) {
            return response()->json([
                'message' => 'O usuário informado não é um vendedor!'
            ], 400);
        }

        $seller->seller_approved = true;
        $seller->update();

        return response()->json([
            'message' => 'O vendedor foi aprovado com sucesso!'
        ], 200);
    }

    /**
     * Toggle blocked seller account.
     * 
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function toggleSeller(int $id)
    {
        $seller = User::find($id);

        if (empty($seller)) {
            return response()->json([
                'message' => 'O vendedor informado não existe!'
            ], 404);
        }

        if ($seller->role != $this->getSellerRole()) {
            return response()->json([
                'message' => 'O usuário informado não é um vendedor!'
            ], 400);
        }

        $seller->blocked = !$seller->blocked;
        $seller->update();

        return response()->json([
            'message' => $seller->blocked ? 'O vendedor foi bloqueado com sucesso!' : 'O vendedor foi desbloqueado com sucesso!'
        ], 200);
    }
}
