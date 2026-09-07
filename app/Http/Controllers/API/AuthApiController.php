<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    public function resendEmail(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('client_auth.messages.must_login'),
                ], 401);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('client_auth.messages.email_already_verified'),
                ], 400);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => __('client_auth.messages.verification_resent'),
            ]);
        } catch (\Throwable $th) {
            Log::error('ResendEmailError', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('client_auth.messages.verification_resend_failed'),
            ], 500);
        }
    }
}
