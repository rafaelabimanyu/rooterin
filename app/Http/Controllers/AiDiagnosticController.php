<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use App\Models\AiDiagnose;

class AiDiagnosticController extends Controller
{
    public function index()
    {
        SEOTools::setTitle('AI Visual Pipe Diagnostics - Deteksi Mampet Otomatis');
        SEOTools::setDescription('Gunakan teknologi AI (Computer Vision) Rooterin untuk mendeteksi masalah pipa Anda hanya dengan foto. Cepat, akurat, dan canggih.');
        
        return response()
            ->view('ai-diagnostic.diagnosa')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'result_label'     => 'required|string',
            'confidence_score' => 'required|integer',
            'audio_label'      => 'nullable|string',
            'audio_confidence' => 'nullable|integer',
            'survey_data'      => 'required|array',
            'recommended_tools'=> 'nullable|string',
            'city_location'    => 'nullable|string',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'metadata'         => 'nullable|array',
        ]);

        // --- ROOTERIN INFERENCE ENGINE (WEIGHTED MULTI-INPUT) ---
        $vScore = $validated['confidence_score'];
        $aScore = $validated['audio_confidence'] ?? 0;
        $sScore = !empty($validated['survey_data']) ? 90 : 0;

        // Composite Weighted Score Calculation
        $compositeScore = ($vScore * 0.5) + ($aScore * 0.3) + ($sScore * 0.2);

        // Deep Ranking Logic (A-E)
        $severity = 'E';
        if ($compositeScore >= 90) $severity = 'A';
        elseif ($compositeScore >= 75) $severity = 'B';
        elseif ($compositeScore >= 50) $severity = 'C';
        elseif ($compositeScore >= 25) $severity = 'D';

        // Generate ID: #RT-YYYY-XXXX
        $year  = date('Y');
        $count = AiDiagnose::whereYear('created_at', $year)->count() + 1;
        $diagnoseId = "#RT-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Use REAL GPS coordinates sent by the browser.
        // Fall back to Jakarta center ONLY if browser did not provide coords.
        $lat = isset($validated['latitude'])  ? (float) $validated['latitude']  : -6.200000;
        $lng = isset($validated['longitude']) ? (float) $validated['longitude'] : 106.816666;

        // --- SERVICE INTEGRATION MAPPING ---
        $serviceMap = [
            'A' => ['slug' => 'saluran-pembuangan-mampet', 'name' => 'Saluran Pembuangan Mampet'],
            'B' => ['slug' => 'saluran-pembuangan-mampet', 'name' => 'Saluran Pembuangan Mampet'],
            'C' => ['slug' => 'air-bersih-cuci-toren',      'name' => 'Air Bersih & Cuci Toren'],
            'D' => ['slug' => 'instalasi-sanitary-pipa',   'name' => 'Instalasi Sanitary & Pipa'],
            'E' => ['slug' => 'instalasi-sanitary-pipa',   'name' => 'Instalasi Sanitary & Pipa']
        ];
        $targetService = $serviceMap[$severity] ?? $serviceMap['B'];

        try {
            $lead = AiDiagnose::create([
                'diagnose_id' => $diagnoseId,
                'result_label' => $validated['result_label'],
                'confidence_score' => $vScore,
                'final_deep_score' => $severity,
                'material_type' => $validated['survey_data']['material'] ?? 'pvc',
                'location_context' => $validated['survey_data']['sub_context'] ?? $validated['survey_data']['location'] ?? 'general',
                'audio_label' => $validated['audio_label'] ?? 'Standard Flow',
                'audio_confidence' => $aScore,
                'survey_data' => $validated['survey_data'],
                'recommended_tools' => $validated['recommended_tools'] ?? 'Rooter Machine',
                'city_location' => $validated['city_location'] ?? 'Auto Detect',
                'latitude' => $lat,
                'longitude' => $lng,
                'metadata' => array_merge($validated['metadata'] ?? [], [
                    'recommended_service_slug' => $targetService['slug'],
                    'recommended_service_name' => $targetService['name']
                ]),
                'status' => 'pending'
            ]);

            // Optional cache eviction
            try {
                \Illuminate\Support\Facades\Cache::forget('ai_intelligence_heatmap');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Cache eviction failed: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'diagnose_id' => $lead->diagnose_id,
                'deep_ranking' => $severity,
                'data' => $lead
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Diagnostic Storage Failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function getHandshake()
    {
        $phantom = app(\App\Services\Security\PhantomSyncService::class);
        $token = $phantom->generateToken([
            'ip' => request()->ip(),
            'agent' => request()->userAgent()
        ]);
        
        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }
}
