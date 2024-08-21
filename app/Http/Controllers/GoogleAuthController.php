<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\GoogleAccount;
use App\Services\Google;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

;

class GoogleAuthController extends Controller
{

    public function index(Request $request, Google $google)
    {
        if (!$request->code) {
            return redirect($google->createAuthUrl()); /** @phpstan-ignore-line */
        }

        $google->authenticate($request->code); /** @phpstan-ignore-line */
        $account = $google->service('Oauth2')->userinfo->get();
        $googleAccount = auth()->user()->googleAccount;

        if (!$googleAccount) {
            $googleAccount = new GoogleAccount();
            $googleAccount->user_id = auth()->id();
            $googleAccount->company_id = auth()->user()->company_id;
            $googleAccount->google_id = $account->id;
            $googleAccount->name = $account->name;
            $googleAccount->token = $google->getAccessToken(); /** @phpstan-ignore-line */
            $googleAccount->save();
            Session::flash('message', __('menu.googleCalendar').' '. __('app.account').' '. __('app.connected').' '. __('app.successfully'));
        }
        else {
            $googleAccount->user_id = auth()->id();
            $googleAccount->company_id = auth()->user()->company_id;
            $googleAccount->google_id = $account->id;
            $googleAccount->name = $account->name;
            $googleAccount->token = $google->getAccessToken(); /** @phpstan-ignore-line */
            $googleAccount->update();
            Session::flash('message', __('menu.googleCalendar').' '. __('app.account').' '. __('app.update').' '. __('app.successfully'));
        }

        return redirect()->route('admin.settings.index', '#googleCalendar');
    }

    public function destroy($id, Google $google)
    {
        $googleAccount = GoogleAccount::find($id);
        $googleAccount->delete();
        $google->revokeToken($googleAccount->token);

        return Reply::success(__('messages.recordDeleted'));
    }

}
