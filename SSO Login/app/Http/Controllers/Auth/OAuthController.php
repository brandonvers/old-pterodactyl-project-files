<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Services\Users\UserUpdateService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Pterodactyl\Contracts\Repository\UserRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthController extends Controller
{

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $auth;

    /**
     * @var \Pterodactyl\Services\Users\UserUpdateService
     */
    private $updateService;

    /**
     * @var \Pterodactyl\Contracts\Repository\UserRepositoryInterface
     */
    private $repository;

    /**
     * LoginController constructor.
     *
     * @param \Illuminate\Auth\AuthManager $auth
     * @param \Pterodactyl\Services\Users\UserUpdateService $updateService
     * @param \Pterodactyl\Contracts\Repository\UserRepositoryInterface $repository
     */
    public function __construct(AuthManager $auth,  UserUpdateService $updateService, UserRepositoryInterface $repository)
    {
        $this->auth = $auth;
        $this->updateService = $updateService;
        $this->repository = $repository;
    }

    /**
     * Redirect to the provider's website
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect(Request $request): RedirectResponse
    {
        if (!app('config')->get('pterodactyl.auth.oauth.enabled')) {
            throw new NotFoundHttpException();
        }

        $drivers = json_decode(app('config')->get('pterodactyl.auth.oauth.drivers'), true);
        $driver = $request->get('driver');

        if ($driver == null || !array_has($drivers, $driver) || !$drivers[$driver]['enabled']) {
            return redirect()->route('auth.login');
        }

        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $request->session()->put('oauth_driver', $driver);

        return Socialite::with($driver)->redirect();
    }

    /**
     * Validate and login OAuth user.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function callback(Request $request): RedirectResponse
    {

        // If logged in link provider to user
        if ($request->user() != null) {
            return $this->linkHandler($request);
        }

        $driver = $request->session()->pull('oauth_driver');

        if (empty($driver)) {
            return redirect()->route('auth.login');
        }

        $drivers = json_decode(app('config')->get('pterodactyl.auth.oauth.drivers'), true);

        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $oauthUser = Socialite::driver($driver)->user();

        try {
            $user = $this->repository->findFirstWhere([['oauth->'. $driver, $oauthUser->getId()]]);
        } catch (RecordNotFoundException $e) {
            return redirect()->route('auth.login');
        }

        $this->auth->guard()->login($user, true);

        return redirect('/');
    }


    /**
     * Link OAuth id to user
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\RedirectResponse
     * @throws RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     */
    protected function linkHandler(Request $request): RedirectResponse
    {
        $driver = $request->session()->pull('oauth_linking');

        if (empty($driver)) {
            return redirect('account/oauth');
        }

        $drivers = json_decode(app('config')->get('pterodactyl.auth.oauth.drivers'), true);

        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $oauthUser = Socialite::driver($driver)->user();

        $oauth = json_decode($request->user()->oauth, true);

        $oauth[$driver] = $oauthUser->getId();

		$userUuid = $request->user()->uuid;

		DB::table('users')->where('uuid', '=', $userUuid)->update([
				'oauth' => json_encode($oauth),
		]);

        return redirect('account/oauth');
    }
}