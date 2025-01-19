<?php

namespace App\Http\Middleware;

use App\Helpers\EncryptionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DecryptApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $encryptedData = $request->input('encrypted');

            if ($encryptedData) {
                try {
                    $decryptedData = EncryptionHelper::decrypt($encryptedData);
                    $request->merge($decryptedData);
                    // Remove encrypted field from request
                    $request->request->remove('encrypted');
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => 'Invalid encrypted data provided',
                        'message' => $e->getMessage()
                    ], 400);
                }
            }
        }

        return $next($request);
    }
}