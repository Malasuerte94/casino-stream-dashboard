<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $latestStream = Stream::latest()->first();
        if(!$latestStream) {
            $latestStream = Stream::create();
        }
        return response()->json([
            'stream' => $latestStream,
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
        $streamData = [];

        $stream = $user->streams()->create($streamData);
        return response()->json([
            'stream' => $stream,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $stream = Stream::findOrFail($request->id);
        $stream->casino = $request->casino;
        $stream->deposit = $request->deposit;
        $stream->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stream $stream)
    {
        //
    }

}
