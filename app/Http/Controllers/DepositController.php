<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $deposits = $user->deposits()->get();

        return response()->json([
            'deposits' => $deposits,
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
        $deposits = $stream->deposits()->get();

        return response()->json([
            'deposits' => $deposits,
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
                'casino'=> $data['casino'],
                'amount'=> $data['amount'],
            ];

        $deposit = $user->deposits()->create($streamData);
        return response()->json([
            'deposit' => $deposit,
        ]);
    }


}
