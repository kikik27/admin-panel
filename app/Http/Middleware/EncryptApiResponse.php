<?php

namespace App\Http\Middleware;

use App\Helpers\EncryptionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // Jangan enkripsi jika response sudah mengandung key 'encrypted'
            if (!isset($data['encrypted'])) {
                $encryptedData = EncryptionHelper::encrypt($data);

                return new JsonResponse([
                    'encrypted' => $encryptedData,
                ], $response->status());
            }
        }

        return $response;
    }
}
