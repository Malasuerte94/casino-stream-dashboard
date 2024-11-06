<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;

class ReferralController extends Controller
{
    /**
     * @param $id
     * @param $parentUser
     * @param $referredUser
     * @return string
     */
    public function registerReferral($id, $parentUser, $referredUser): string
    {
        $userStreamer = User::find($id);

        if (!$userStreamer || !$parentUser || !$referredUser) {
            return 'Nope!';
        }

        $referral = Referral::where('user_id', $id)->where('referred_user', $referredUser)->first();
        if ($referral) {
            return 'Ai fost adus deja de @' . $referral->parent_user;
        }

        $referral = Referral::where('user_id', $id)->where('parent_user', $parentUser)->where('referred_user', $referredUser)->first();
        if ($referral) {
            return 'Nu poti de mai multe ori';
        }

        $referral = new Referral();
        $referral->user_id = $id;
        $referral->parent_user = $parentUser;
        $referral->referred_user = $referredUser;
        $referral->save();

        return 'Perfecto!';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getReferrals($id): mixed
    {
        $userStreamer = User::find($id);
        if (!$userStreamer) {
            return [];
        }
        $list = Referral::where('user_id', $id)->get()->toArray();
        $user = $userStreamer->name;
        return [
            'user' => $user,
            'list' => $list
        ];
    }
}
