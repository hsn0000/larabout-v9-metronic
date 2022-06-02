<?php
namespace App\Library\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;
use Laravel\Jetstream\ConfirmsPasswords;

use Illuminate\Support\Collection;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\RecoveryCode;

class TwoFactor
{
    use ConfirmsPasswords;

    protected $provider;
    protected $user;

    /**
     * Indicates if two factor authentication QR code is being displayed.
     *
     * @var bool
     */
    public $showingQrCode = false;

    public $confirmed_password = false;

    public function confirmPassword()
    {
        session(['auth.password_confirmed_at' => time()]);
    }

    public function __construct(TwoFactorAuthenticationProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->user = Auth::user();

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $this->confirmed_password = (time() - $request->session()->get('auth.password_confirmed_at', 0)) < $request->input('seconds', config('auth.password_timeout', 900));
        }
        else{
            $this->confirmed_password = true;
        }
    }

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public $showingRecoveryCodes = false;

    public function enableTwoFactorAuthentication()
    {
        if($this->confirmed_password)
        {
            $this->user->forceFill([
                'two_factor_secret' => encrypt($this->provider->generateSecretKey()),
                'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                    return RecoveryCode::generate();
                })->all())),
            ])->save();

            $this->showingQrCode = true;
            $this->showingRecoveryCodes = true;
        }

        return $this->confirmed_password ?: abort(401, 'Need action for confirm the password!');
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return void
     */
    public function regenerateRecoveryCodes()
    {
        if($this->confirmed_password)
        {
            $this->user->forceFill([
                'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                    return RecoveryCode::generate();
                })->all())),
            ])->save();

            $this->showingRecoveryCodes = true;
        }

        return $this->confirmed_password ?: abort(401, 'Need action for confirm the password!');
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
     * @return void
     */
    public function disableTwoFactorAuthentication()
    {
        if($this->confirmed_password)
        {
            $this->user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
            ])->save();
        }

        return $this->confirmed_password ?: abort(401, 'Need action for confirm the password!');
    }

    /**
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes()
    {
        $this->showingRecoveryCodes = true;

        return $this->confirmed_password ?: abort(401, 'Need action for confirm the password!');
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty()
    {
        return ! empty($this->user->two_factor_secret);
    }
}
