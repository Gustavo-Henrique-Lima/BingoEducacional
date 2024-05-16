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

class AdminToken
{
    private Configuration $config;

    public function __construct()
    {
        // Configure the signing configuration (secret key here is just an example)
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText("s7m5d9g0yh2imZ0kQbIXTIJNz2jper7PfI0MbIs77GoXQmqce4uKXFZaSAV5Icno") // Use a strong key here
        );
    }

    public function handle(Request $request, Closure $next): JsonResponse
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(["error" => "Unauthorized"], 401);
        }
        $parsedToken = $this->config->parser()->parse($token);

        if (!$parsedToken->claims()->has('role') || $parsedToken->claims()->get('role') !== 'ADMIN') {
            return response()->json(["error" => "Forbidden"], 403);
        }

        // Configure o relógio do sistema para validar se o token não expirou
       $clock = new SystemClock(new DateTimeZone(date_default_timezone_get()));

       // Valide a assinatura e se não expirou
       $constraints = [
           new ValidAt($clock)
       ];

        if (!$this->config->validator()->validate($parsedToken, ...$constraints)) {
            return response()->json(["error" => "Expired or invalid token"], 401);
        }

        if ($parsedToken->claims()->has('active') && $parsedToken->claims()->get('active') === 0) {
            return response()->json(["error" => "Você precisa ativar sua conta"], 403);
        }
 
        return $next($request);
    }
}
