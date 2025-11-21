<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            // Redirect based on user role
            if ($user->hasRole('Candidate')) {
                return redirect()->route('candidate.dashboard');
            } elseif ($user->hasRole('Employer')) {
                return redirect()->route('employer.dashboard');
            } elseif ($user->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('candidate.dashboard');
        }
        
        return Inertia::render('auth/VerifyEmail', ['status' => $request->session()->get('status')]);
    }
}
