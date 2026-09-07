<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();
        }

        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function login(Request $request)
    {
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->validate([
            'email' => 'required|string',
            'credential' => 'required|string',
        ]);

        // Chercher l'utilisateur par email OU par login
        $user = User::where('email', $request->email)
            ->orWhere('login', $request->email)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Essayer d'abord avec le mot de passe, puis avec le PIN
        $authenticated = false;
        
        if (Hash::check($request->credential, $user->password)) {
            $authenticated = true;
        } elseif ($user->verifyConfidentialCode($request->credential)) {
            $authenticated = true;
        }

        if (!$authenticated) {
            throw ValidationException::withMessages([
                'credential' => ['Le mot de passe ou code PIN est incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Votre compte est désactivé. Contactez l\'administrateur.'],
            ]);
        }

        if ($user->ip_whitelist_enabled && !$user->isIpAllowed($request->ip())) {
            throw ValidationException::withMessages([
                'email' => ['Votre adresse IP n\'est pas autorisée à se connecter.'],
            ]);
        }

        // Remember me pour 30 jours si demandé
        $remember = $request->filled('remember');
        if ($remember) {
            config(['session.lifetime' => 43200]); // 30 jours en minutes
        }

        Auth::login($user, $remember);

        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPasswordForm()
    {
        // Vérifier si SMTP est configuré
        $smtpConfigured = Setting::get('smtp_host') && Setting::get('smtp_from_address');
        
        if (!$smtpConfigured) {
            return redirect()->route('login')
                ->with('error', 'La réinitialisation de mot de passe n\'est pas disponible. Contactez l\'administrateur.');
        }
        
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Vérifier si SMTP est configuré
        $smtpConfigured = Setting::get('smtp_host') && Setting::get('smtp_from_address');
        
        if (!$smtpConfigured) {
            return back()->with('error', 'La réinitialisation de mot de passe n\'est pas disponible.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Ne pas révéler si l'email existe ou non
            return back()->with('success', 'Si cet email existe, un lien de réinitialisation a été envoyé.');
        }

        // Générer un token
        $token = Str::random(60);
        
        // Stocker le token
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Envoyer l'email avec le token
        try {
            $user->notify(new ResetPasswordNotification($token));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email reset: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer.');
        }

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = \DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return back()->withErrors(['email' => 'Le token de réinitialisation est invalide.']);
        }

        if (now()->diffInHours($reset->created_at) > 24) {
            return back()->withErrors(['email' => 'Le token de réinitialisation a expiré.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        \DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
