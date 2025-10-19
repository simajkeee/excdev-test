<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    use AuthorizesRequests;

    public function summary(Account $account, Request $request)
    {
        $this->authorize('view', $account);

        $transactions = Transaction::where('account_id', $account->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        AccountResource::$wrap = null;
        TransactionResource::$wrap = null;

        return Inertia::render('account/index', [
            'userId' => $request->user(),
            'account' => new AccountResource($account),
            'transactions' => TransactionResource::collection($transactions)
        ]);
    }

    public function history(Account $account, Request $request)
    {
        $this->authorize('view', $account);

        $query = $account->transactions();
        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('limit', 15);
        $orderBy = $request->input('order_by', 'created_at');
        $direction = $request->input('direction', 'desc');
        $transactions = $query->orderBy($orderBy, $direction)
            ->paginate($perPage)
            ->withQueryString();

        AccountResource::$wrap = null;
        TransactionResource::$wrap = null;

        return Inertia::render('account/history', [
            'account' => new AccountResource($account),
            'transactions' => TransactionResource::collection($transactions),
            'filters' => [
                'search' => $search,
                'order_by' => $orderBy,
                'direction' => $direction,
                'limit' => $perPage,
            ],
        ]);
    }

    public function userAccounts(User $user, Request $request)
    {
        $this->authorize('viewAny',  [Account::class, $user]);

        return AccountResource::collection($user->accounts);
    }
}
