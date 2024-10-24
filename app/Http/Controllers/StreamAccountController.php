<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StreamAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $streamAccounts = $user->streamAccounts()->get();

        return response()->json([
            'streamAccounts' => $streamAccounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['youtube', 'kick', 'facebook', 'twitch'])],
            'url' => 'required|url',
        ]);

        $user = $request->user();
        $streamAccount = $user->streamAccounts()->create($validated);

        return response()->json([
            'streamAccount' => $streamAccount,
            'message' => 'Stream account created successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = request()->user();
        $streamAccount = $user->streamAccounts()->findOrFail($id);
        $streamAccount->delete();

        return response()->json([
            'message' => 'Stream account deleted successfully'
        ]);
    }
}
