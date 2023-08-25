<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $withdrawals = $user->withdrawals()->get();

        return response()->json([
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
    * Show from link
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function show(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $stream = Stream::findOrFail($id);
        $withdrawals = $stream->withdrawals()->get();

        return response()->json([
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->payload;

        $streamData = [
                'stream_id' => $data['stream_id'],
                'amount'=> $data['amount'],
            ];

        $withdrawal = $user->withdrawals()->create($streamData);
        return response()->json([
            'withdrawal' => $withdrawal,
        ]);
    }

}
