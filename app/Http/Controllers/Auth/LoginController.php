<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\FrontBaseController;
use App\Models\Role;
use App\Models\Social;
use App\Notifications\NewUser;
use App\Traits\SocialAuthSettings;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends FrontBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, SocialAuthSettings;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('email.loginAccount'));
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        /*if (!$this->isLegal()) {
            return redirect('verify-purchase');
        }*/

        if (!session()->has('errors')) {
            session()->put('url.encoded', url()->previous());
        }

        $socialAuthSettings = $this->socialAuthSettings;

        return view('auth.login', compact('socialAuthSettings'));
    }

    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_admin) {
            if ($user->company->verified == 'yes' && $user->company->status !== 'inactive') {
                return $this->guard()->attempt(
                    $this->credentials($request), $request->filled('remember')
                );
            }
        }
        else {
            return $this->guard()->attempt(
                $this->credentials($request), $request->filled('remember')
            );
        }
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $google_captcha = $this->googleCaptchaSettings;

        if($google_captcha->status == 'active' && $google_captcha->login_page == 'active')
        {
            $rules['recaptcha'] = 'required';
        }

        // User type from email/username
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check google reCaptcha if setting is enabled
        if ($google_captcha->status == 'active' && $google_captcha->v2_status == 'active' && (is_null($user) || ($user && !$user->hasRole('admin')))) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $this->validate($request, $rules);
    }

    public function validateGoogleReCaptcha($googleReCaptchaResponse)
    {
        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => $this->googleCaptchaSettings->v2_secret_key,
                    'response' => $googleReCaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

    public function googleRecaptchaMessage()
    {
        throw ValidationException::withMessages([
            'g-recaptcha-response' => [trans('app.recaptchaFailed')],
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // User type from email/username
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check google recaptcha if setting is enabled
        if ($this->googleCaptchaSettings->status == 'active' && $this->googleCaptchaSettings->v2_status == 'active' && (is_null($user) || ($user && !$user->hasRole('admin'))))
        {
            // Checking is google recaptcha is valid
            $gReCaptchaResponseInput = 'g-recaptcha-response';
            $gReCaptchaResponse = $request->{$gReCaptchaResponseInput};
            $validateRecaptcha = $this->validateGoogleReCaptcha($gReCaptchaResponse);

            if (!$validateRecaptcha)
            {
                return $this->googleRecaptchaMessage();
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if (method_exists($this, 'hasTooManyLoginAttempts') && $this->hasTooManyLoginAttempts($request))
        {
            $this->fireLockoutEvent($request);
            /** @phpstan-ignore-next-line */
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_superadmin || $user->is_agent) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->is_admin && $user->company->verified == 'yes') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->is_employee) {
            return redirect()->route('admin.dashboard');
        }

        return redirect(session()->get('url.encoded'));
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        session()->forget('url.encoded');

        return redirect(url()->previous());
    }

    public function redirect($provider)
    {
        $this->setSocailAuthConfigs();
        return Socialite::driver($provider)->redirect(); /** @phpstan-ignore-line */
    }

    public function callback(Request $request, $provider)
    {
        $this->setSocailAuthConfigs();

        try {
            if($provider != 'twitter') {
                $data = Socialite::driver($provider)->stateless()->user(); /** @phpstan-ignore-line */
            }
            else {
                $data = Socialite::driver($provider)->user(); /** @phpstan-ignore-line */
            }
        }
        catch (\Exception $e) {
            if ($request->has('error_description') || $request->has('denied')) {
                return redirect()->route('login')->withErrors([$this->username() => 'The user cancelled '.$provider.' login']);
            }

            throw ValidationException::withMessages([
                $this->username() => [$e->getMessage()],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        }

        $user = User::where('email', '=', $data->email)->first();

        if($user) {
            // User found
            \DB::beginTransaction();

            Social::updateOrCreate(['user_id' => $user->id],
            [
                'social_id' => $data->id,
                'social_service' => $provider,
            ]);

            \DB::commit();

            \Auth::login($user);
            return redirect()->intended($this->redirectPath());
        }
        else {
            $user = User::create([
                'name'          => $data->getName(),
                'email'         => $data->getEmail(),
                'image'         => $data->getAvatar(),
                'provider_id'   => $data->getId(),
                'password'      => '123456',
            ]);

            Social::updateOrCreate(['user_id' => $user->id],
            [
                'social_id' => $data->getId(),
                'social_service' => $provider,
            ]);

            $user->attachRole(Role::where('name', 'customer')->first()->id);

            Auth::login($user);

            $user->notify(new NewUser('123456'));

            return redirect()->route('admin.dashboard');
        }

    }

}
