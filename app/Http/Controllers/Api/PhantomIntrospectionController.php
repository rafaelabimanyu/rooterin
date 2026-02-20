<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Security\PhantomSyncService;

class PhantomIntrospectionController extends Controller
{
    /**
     * The Phantom-Bridge (Hybrid Token Exchange / Introspection API)
     * For 3rd Party Integrations or Internal Microservices (Gateway Pattern).
     *
     * It avoids full session overhead, delivering <10ms validation 
     * using the cached binary states.
     */
    public function introspect(Request $request, PhantomSyncService $phantom)
    {
        // Require API Key for service-to-service auth (simplified example)
        $authKey = $request->header('Authorization');
        if (!$authKey || $authKey !== 'Bearer ' . env('PHANTOM_BRIDGE_KEY', 'default-v2-dev-key')) {
            return response()->json([
                'active' => false,
                'error' => 'Unauthorized RPC Bridge Access (401)'
            ], 401);
        }

        // Token can be sent in body or header
        $token = $request->input('token') ?: $request->header('X-Phantom-Token');
        
        if (!$token) {
            return response()->json([
                'active' => false,
                'reason' => 'Missing opaque token footprint.'
            ], 400);
        }

        // Utilize Tiered Cache + Edge Filters
        $start = microtime(true);
        // Inject token explicitly into request so PhantomSyncService can read it
        $request->merge(['phantom_token' => $token]);
        $exchange = $phantom->exchange($request);
        $latency = (microtime(true) - $start) * 1000;

        if (!$exchange) {
            return response()->json([
                'active' => false,
                'revoked' => true,
                'latency_ms' => round($latency, 2)
            ], 403);
        }

        // Return verified state and scopes for 3rd Party JWT issuance
        return response()->json([
            'active' => true,
            'client_id' => $exchange['identity']['user_id'] ?? 'anonymous',
            'claims' => $exchange['identity'],
            'exp' => now()->addHours(2)->timestamp,
            'latency_ms' => round($latency, 2),
            'pulse_status' => $exchange['status']
        ]);
    }
}
