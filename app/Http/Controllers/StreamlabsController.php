<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Throwable;

class StreamlabsController extends Controller
{
    /**
     * Redirect the user to Streamlabs' OAuth authorization page.
     */
    public function redirectToStreamlabs()
    {
        $clientId    = config('services.streamlabs.client_id');
        $redirectUri = route('streamlabs.callback');
        $scope       = 'donations.read+donations.create';

        $authorizationUrl = "https://streamlabs.com/api/v2.0/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope={$scope}";

        return redirect($authorizationUrl);
    }

    /**
     * Handle the OAuth callback from Streamlabs.
     */
    public function handleStreamlabsCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('home')->with('error', 'Streamlabs authorization failed.');
        }

        $code = $request->input('code');

        if (!$code) {
            return redirect()->route('home')->with('error', 'Authorization code not found.');
        }

        $clientId     = config('services.streamlabs.client_id');
        $clientSecret = config('services.streamlabs.client_secret');
        $redirectUri  = route('streamlabs.callback');

        $http = new Client;

        $response = $http->post('https://streamlabs.com/api/v2.0/token', [
            'form_params' => [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'code'          => $code,
                'grant_type'    => 'authorization_code'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Save token details to the authenticated user.
        $user = auth()->user();
        $user->streamlabs_access_token  = $data['access_token'];
        $user->streamlabs_refresh_token = $data['refresh_token'];
        $user->streamlabs_expires_in    = $data['expires_in'];
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Streamlabs account connected successfully.');
    }

    /**
     * @param $streamerId
     * @return JsonResponse
     */
    public function getDonations($streamerId): JsonResponse
    {
        try {
            $user = User::findOrFail($streamerId);
            if (!$user || !$user->streamlabs_access_token) {
                return response()->json(['error' => 'Streamlabs not connected.'], 403);
            }
            $client = new Client();
            $response = $client->request('GET', 'https://streamlabs.com/api/v2.0/donations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->streamlabs_access_token,
                    'Accept'        => 'application/json',
                ],
            ]);
            $message = json_decode($response->getBody(), true);

            $donations = array_map(function ($donation) {
                unset($donation['email']);
                return $donation;
            }, $message['data']);

            return response()->json(["donations" => $donations]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Unable to get donations: ' . $e->getMessage()], 500);
        }
    }
}
