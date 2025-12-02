<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                        ? response()->json([], 204)
                        : redirect($this->redirectPath())
                            ->with('info', 'Twój email jest już zweryfikowany.');
        }

        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $request->user()->id, 'hash' => sha1($request->user()->email)]
            );

            Mail::to($request->user()->email)->send(
                new EmailVerificationMail($request->user(), $verificationUrl)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification email: ' . $e->getMessage());
            
            return $request->wantsJson()
                        ? response()->json(['message' => 'Wystąpił błąd podczas wysyłania emaila.'], 500)
                        : redirect()->back()
                            ->with('error', 'Wystąpił błąd podczas wysyłania emaila. Spróbuj ponownie później.');
        }

        return $request->wantsJson()
                    ? response()->json([], 202)
                    : back()->with('resent', true);
    }
}
