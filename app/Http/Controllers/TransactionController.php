<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function getAll(){
        $fields = ['*'];
        $transactions = $this->transactionService->getAll($fields);
        return response()->json(TransactionResource::collection($transactions));
    }

    public function store(TransactionRequest $request){
        $data = $request->validated();
        $transaction = $this->transactionService->createTransaction($data);
        return response()->json(['message' => 'Transaction created successfully', 'data' => $transaction], 201);
    }

    public function show($id){
        try{

        $fields = ['*'];
        $transaction = $this->transactionService->getTransactionById($id, $fields);
        return response()->json(new TransactionResource($transaction));
        }catch(ModelNotFoundException $e){
            return response()->json(['message' => 'Transaction not found'], 404);
        }
    }
    

    public function getTransactionByMerchant(){
        $user = Auth::user();

        if(!$user || !$user->merchant){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $merchantId = $user->merchant->id;
        $transactions = $this->transactionService->getTransactionByMerchant($merchantId);
        return response()->json(TransactionResource::collection($transactions));
    }
}
