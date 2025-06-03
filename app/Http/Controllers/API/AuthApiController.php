<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    public function resendEmail(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $user = User::findOrFail($userId);

            if ($user->hasVerifiedEmail()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email đã được xác minh.'
                ], 400);
            }

            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => 'Email xác minh đã được gửi lại. Kiểm Tra Email để xác Nhận!'
            ]);
        } catch (\Throwable $th) {
            Log::error('ResendEmailError', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gửi lại email thất bại. Vui lòng thử lại.'
            ], 500);
        }
    }
}
