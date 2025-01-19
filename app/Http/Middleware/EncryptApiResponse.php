<?php

namespace App\Http\Middleware;

use App\Helpers\EncryptionHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EncryptApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya mengenkripsi jika respons berupa JsonResponse
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true); // Ambil data JSON
            $encryptedData = EncryptionHelper::encrypt($data);

            return response()->json([
                'encrypted' => $encryptedData,
            ]);
        }

        return $response;
    }
}