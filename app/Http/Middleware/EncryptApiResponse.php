<?php

namespace App\Http\Middleware;

use App\Helpers\EncryptionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EncryptApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        Log::info('Response type: ' . get_class($response));

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            Log::info('Original response data:', $data);

            if (!isset($data['encrypted'])) {
                $encryptedData = EncryptionHelper::encrypt($data);
                Log::info('Encrypted data: ' . $encryptedData);

                return new JsonResponse([
                    'encrypted' => $encryptedData,
                ], $response->status());
            }
        }

        return $response;
    }
}