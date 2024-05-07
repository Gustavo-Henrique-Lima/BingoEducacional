<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use DateTimeZone;

class ProtectedRoute
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText("s7m5d9g0yh2imZ0kQbIXTIJNz2jper7PfI0MbIs77GoXQmqce4uKXFZaSAV5Icno") // Use uma chave forte
        );
    }

    public function handle(Request $request, Closure $next): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(["error" => "Unauthorized"], 401);
        }

        try {
            $parsedToken = $this->config->parser()->parse($token);
        } catch (\Exception $e) {
            return response()->json(["error" => "Invalid token"], 401);
        }

       $clock = new SystemClock(new DateTimeZone(date_default_timezone_get()));

       $constraints = [
           new ValidAt($clock)
       ];

        if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
            return response()->json(["error" => "Expired or invalid token"], 401);
        }

        return $next($request);
    }
}
