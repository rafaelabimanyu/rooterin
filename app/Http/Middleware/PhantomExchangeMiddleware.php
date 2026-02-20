<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Security\PhantomSyncService;

class PhantomExchangeMiddleware
{
    protected $phantom;

    public function __construct(PhantomSyncService $phantom)
    {
        $this->phantom = $phantom;
    }

    /**
     * Handle an incoming request.
     * Validates Opaque Reference Token before allowing access.
     */
    public function handle(Request $request, Closure $next)
    {
        $exchange = $this->phantom->exchange($request);

        if (!$exchange) {
            return response()->json([
                'error' => 'Phantom Sync Failure',
                'message' => 'Identity exchange denied. Threat event recorded.'
            ], 403);
        }

        // Attach verified identity to request for backend use
        $request->attributes->add(['phantom_identity' => $exchange['identity']]);
        $request->attributes->add(['phantom_latency' => $exchange['latency']]);

        return $next($request);
    }
}
