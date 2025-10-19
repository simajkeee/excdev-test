<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    public function index(User $user, Request $request)
    {
//        $this->authorize('viewAny',  [Transaction::class, $user]);

        $accountIds = $user->accounts()->pluck('id');
        $query = Transaction::whereIn('account_id', $accountIds);

        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $perPage = 15;
        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return TransactionResource::collection($transactions);
    }
}
