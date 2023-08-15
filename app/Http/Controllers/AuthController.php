<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    /**
     * OAuth サーバへリダイレクト
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('state', $state);

        $codeVerifier = Str::random(128);
        $request->session()->put('code_verifier', $codeVerifier);

        $codeChallenge = strtr(rtrim(
            base64_encode(hash('sha256', $codeVerifier, true)),
            '='
        ), '+/', '-_');

        $query = http_build_query([
            'client_id' => env('LARAVELPASSPORT_CLIENT_ID'),
            'redirect_uri' => url('/auth/callback'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect(env('LARAVELPASSPORT_HOST') . '/oauth/authorize?' . $query);
    }

    /**
     * OAuth サーバからのコールバック
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $state = $request->session()->pull('state');
        if (strlen($state) <= 0 || $state !== $request->state) {
            throw new \InvalidArgumentException('Invalid state value.');
        }

        $codeVerifier = $request->session()->pull('code_verifier');
        $accessToken = $this->getToken($codeVerifier, $request->code);

        $response = $this->authorizedRequest($accessToken)
            ->get(env('LARAVELPASSPORT_HOST') . '/api/user');

        $user = User::create([
            'email' => $response['email'],
            'name' => $response['name'],
            'access_token' => $accessToken,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/');
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        /** @var User ログインユーザ */
        $user = Auth::user();
        $user = $user->makeVisible(['access_token']);

        $url = env('LARAVELPASSPORT_HOST') . '/api/token/' . env('LARAVELPASSPORT_CLIENT_ID');
        $this->authorizedRequest($user->access_token)->delete($url);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $query = http_build_query(['redirect_uri' => url('/')]);
        return redirect(env('LARAVELPASSPORT_HOST') . '/logout?' . $query);
    }

    /**
     * OAuth サーバからアクセストークンを取得
     *
     * @param string $codeVerifier
     * @param string $code
     * @return string
     */
    private function getToken(string $codeVerifier, string $code): string
    {
        $response = Http::asForm()->post(env('LARAVELPASSPORT_HOST') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('LARAVELPASSPORT_CLIENT_ID'),
            'redirect_uri' => url('/auth/callback'),
            'code_verifier' => $codeVerifier,
            'code' => $code,
        ]);

        if ($response->status() !== 200) {
            throw new HttpException($response->status());
        }
        return $response['access_token'];
    }

    /**
     * 認証済みリクエストの送信（ヘッダの付加）
     *
     * @param string $accessToken
     * @return PendingRequest
     */
    private function authorizedRequest(string $accessToken): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ]);
    }
}
