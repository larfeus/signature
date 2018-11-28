<?php

namespace Larfeus\Signature\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Larfeus\Signature\Facade\Signature as SignatureManager;

class Signature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $name
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $name)
    {
        $signature = $request->headers->get('Signature');
        $data = $request->input();

        if (! SignatureManager::signer($name)->verify($signature, $data)) {
            throw new UnauthorizedHttpException('signature-auth', 'Signature has invalid.');
        }

        return $next($request);
    }
}