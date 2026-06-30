<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'balance' => (float) ($user->wallet_balance ?? 0),
            'currency' => 'FCFA',
            // Aucune transaction tant que le rechargement n'est pas implémenté
            'transactions' => [],
        ]);
    }
}
