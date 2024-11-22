<?php
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
Route::get('/auth/saml/login', function () {
    return Socialite::driver('saml2')->redirect();
})->name('auth.saml.login');




Route::any('/auth/callback', function () {
    $saml = Socialite::driver('saml2')->stateless()->user();
    $user = User::updateOrCreate([
        'email' => $saml->getEmail(),
    ], [
        'name' => $saml->first_name,
        'last_name' => $saml->last_name,
        // 'password' => 'DUMMY',
    ]);
    Auth::login($user);
    return redirect('auth/saml2/arrival');

})->name('auth.callback');







Route::get('/auth/saml/metadata', function () {
    return Socialite::driver('saml2')->getServiceProviderMetadata();
});


Route::get('/auth/saml/logout', function (Request $request) {

    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');

})->name('auth.saml.logout');
 