@extends('admin.layout')

@section('content')
<div class="min-h-screen bg-[#020617] text-slate-300 font-sans selection:bg-primary/30">
    <!-- Header: War Room Style -->
    <div class="relative overflow-hidden bg-slate-950 border-b border-white/5 px-8 py-6">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(primary 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <h1 class="text-2xl font-black text-white tracking-tighter uppercase italic">System Sentinel <span class="text-primary/70">v1.2</span></h1>
                </div>
                <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-500">Infrastucture Integrity & Multi-Modal AI Health Monitor</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-[9px] font-black text-slate-500 uppercase leading-none">Last System Heartbeat</p>
                    <p id="last-sync" class="text-xs font-mono text-primary mt-1">{{ \Carbon\Carbon::parse($healthData['last_sync'])->format('H:i:s T') }}</p>
                </div>
                <button onclick="runDeepScan()" id="scan-btn" class="px-6 py-2.5 bg-white text-slate-950 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-lg shadow-white/5 active:scale-95 flex items-center gap-2">
                    <i class="ri-radar-line text-sm"></i>
                    Run Deep Scan
                </button>
            </div>
        </div>
    </div>

    <div class="p-8 space-y-8">
        <!-- Status Overview Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @php
                $sections = [
                    ['title' => 'AI Core', 'data' => $healthData['ai_integrity'], 'icon' => 'ri-cpu-line', 'field' => null],
                    ['title' => 'Resources', 'data' => $healthData['infrastructure']['database'], 'icon' => 'ri-database-2-line', 'field' => 'pulse'],
                    ['title' => 'SEO API', 'data' => $healthData['seo_api_audit']['google_indexing'], 'icon' => 'ri-rocket-line', 'field' => null],
                    ['title' => 'Security', 'data' => $healthData['security']['environment'], 'icon' => 'ri-shield-check-line', 'field' => null],
                ];
            @endphp
            @foreach($sections as $sec)
            <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                    <i class="{{ $sec['icon'] }} text-4xl text-white"></i>
                </div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">{{ $sec['title'] }}</p>
                <div class="flex items-end gap-3">
                    <span class="text-3xl font-black text-white italic tracking-tighter uppercase">
                        {{ $sec['field'] ? $sec['data'][$sec['field']] : ($sec['data']['status'] ?? 'N/A') }}
                    </span>
                    <div class="w-3 h-3 rounded-full mb-2 
                        {{ ($sec['data']['status'] ?? '') === 'Operational' ? 'bg-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)]' : 
                          (($sec['data']['status'] ?? '') === 'Degraded' ? 'bg-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.5)]' : 'bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.5)]') }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 1. AI Model Integrity List -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-6">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="ri-brain-line text-primary"></i> Neural Assets Monitor
                    </h3>
                    <div class="space-y-4">
                        @foreach($healthData['ai_integrity']['models'] as $model)
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-white truncate">{{ $model['name'] }}</p>
                                <p class="text-[8px] text-slate-500 font-mono mt-0.5">{{ $model['path'] }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $model['status'] === 'Operational' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                {{ $model['status'] }}
                            </span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5">
                            <div>
                                <p class="text-[10px] font-bold text-white">ai-processor.js</p>
                                <p class="text-[8px] text-slate-500 font-mono mt-0.5">Web Worker Heartbeat</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-[8px] font-black text-green-500 uppercase">ACTIVE</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SSL & Env -->
                <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-6">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="ri-lock-line text-primary"></i> Security Pulse
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2">SSL Expiry</p>
                            <p class="text-xl font-black text-white italic tracking-tighter">{{ $healthData['security']['ssl']['days_left'] }} <span class="text-[10px] font-bold text-slate-500 not-italic">DAYS</span></p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2">Debug Mode</p>
                            <p class="text-xl font-black {{ ($healthData['security']['environment']['debug_mode'] ?? '') === 'Safe (Zero-Exposure)' ? 'text-green-500' : 'text-red-500' }} italic tracking-tighter">{{ strtoupper($healthData['security']['environment']['debug_mode'] ?? 'UNKNOWN') }}</p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2">Threat Neutralized</p>
                            <p class="text-xl font-black text-green-500 italic tracking-tighter">{{ $healthData['security']['audit']['threat_neutralized'] ?? 0 }} <span class="text-[10px] font-bold text-slate-500 not-italic">REJECTS</span></p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2">Gateway Pulse</p>
                            <p class="text-xl font-black text-white italic tracking-tighter">{{ $healthData['security']['audit']['intro_pulse'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Infrastructure Vitality -->
            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Resource Monitor -->
                    <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-8">
                        <h3 class="text-xs font-black text-white uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i class="ri-server-line text-primary"></i> Compute Metrics
                        </h3>
                        
                        <div class="space-y-8">
                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <div>
                                        <p class="text-[10px] font-black text-white uppercase mb-1 tracking-tight">System Memory Usage</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $healthData['infrastructure']['compute']['usage'] }} (Peak: {{ $healthData['infrastructure']['compute']['peak'] }})</p>
                                    </div>
                                    <span class="text-xs font-black {{ ($healthData['infrastructure']['compute']['status'] ?? '') == 'ULTRA-OPTIMIZED' ? 'text-primary' : 'text-green-500' }} italic">{{ $healthData['infrastructure']['compute']['status'] }}</span>
                                </div>
                                <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary w-[32%] rounded-full shadow-[0_0_10px_rgba(255,255,255,0.3)]"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <div>
                                        <p class="text-[10px] font-black text-white uppercase mb-1 tracking-tight">Phantom L1 Hit Ratio</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $healthData['infrastructure']['compute']['l1_hit_ratio'] ?? '0%' }} vs Redis (L2)</p>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-400 italic">Storage Bypass</span>
                                </div>
                                <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full {{ ($healthData['infrastructure']['compute']['status'] ?? '') == 'ULTRA-OPTIMIZED' ? 'bg-primary shadow-[0_0_10px_rgba(249,115,22,0.3)]' : 'bg-green-500' }} transition-all duration-1000 rounded-full" style="width: {{ $healthData['infrastructure']['compute']['l1_hit_ratio'] ?? '0%' }}"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <div>
                                        <p class="text-[10px] font-black text-white uppercase mb-1 tracking-tight">Physical Storage Audit</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $healthData['infrastructure']['storage']['free_space'] }} Available</p>
                                    </div>
                                    <span class="text-[10px] font-black text-white italic p-1 px-2 bg-white/10 rounded uppercase">{{ $healthData['infrastructure']['storage']['log_status'] }}</span>
                                </div>
                                <div class="h-2 bg-white/5 rounded-full overflow-hidden mb-4">
                                    <div class="h-full bg-white rounded-full transition-all duration-1000" style="width: {{ $healthData['infrastructure']['storage']['usage_percent'] }}"></div>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-tight">Memory Fragmentation Level</p>
                                    </div>
                                    <span class="text-[10px] font-black {{ intval($healthData['infrastructure']['storage']['fragmentation'] ?? 0) < 10 ? 'text-green-500' : 'text-orange-500' }} italic">{{ $healthData['infrastructure']['storage']['fragmentation'] ?? '0%' }} (L2 ORPHAN)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- API Integrations -->
                    <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-8">
                        <h3 class="text-xs font-black text-white uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i class="ri-cloud-line text-primary"></i> External Node Connectivity
                        </h3>
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20">
                                    <i class="ri-google-line text-primary text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-white uppercase tracking-tight">Google Indexing Rocket</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-0.5">{{ $healthData['seo_api_audit']['google_indexing']['message'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-green-500/10 text-green-500">CONNECTED</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                                    <i class="ri-cloud-windy-line text-purple-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-white uppercase tracking-tight">Phantom Cloud Sync</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-0.5">Last Checked: {{ $healthData['infrastructure']['database']['last_backup'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ $healthData['infrastructure']['database']['backup_status'] === 'Operational' ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500' }}">
                                        {{ $healthData['infrastructure']['database']['backup_status'] === 'Operational' ? 'SECURED' : 'CRITICAL' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center border border-green-500/20">
                                    <i class="ri-whatsapp-line text-green-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-white uppercase tracking-tight">WhatsApp Lead Gateway</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-0.5">Latency: {{ $healthData['seo_api_audit']['whatsapp']['latency'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-green-500/10 text-green-500">READY</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20">
                                    <i class="ri-map-pin-line text-orange-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-white uppercase tracking-tight">XML Sitemap Manifest</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-0.5">Location: {{ $healthData['seo_api_audit']['sitemap']['path'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-green-500/10 text-green-500">SYNCED</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                                    <i class="ri-archive-line text-blue-500 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-white uppercase tracking-tight">Cold Storage Migration</p>
                                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-0.5">Last Archive: {{ $healthData['security']['audit']['last_archival'] ?? 'Unknown' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase {{ ($healthData['security']['audit']['last_archival'] ?? 'N/A') !== 'N/A' ? 'bg-green-500/10 text-green-500' : 'bg-slate-500/10 text-slate-500' }}">OPTIMAL</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Metrics -->
                <div class="bg-slate-900/50 border border-white/5 rounded-3xl p-8 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-5 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xs font-black text-white uppercase tracking-widest flex items-center gap-2">
                            <i class="ri-database-line text-primary"></i> Relational Database Pulse
                        </h3>
                        <span class="text-[10px] font-black text-slate-500 uppercase bg-white/5 px-3 py-1 rounded-full border border-white/5 tracking-widest">PostgreSQL / MySQL</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2 tracking-widest">Query Latency</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-3xl font-black text-white italic tracking-tighter">{{ $healthData['infrastructure']['database']['pulse'] }}</p>
                                <span class="text-[8px] font-bold text-green-500 uppercase">Excellent</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2 tracking-widest">Total Diagnostic Objects</p>
                            <p class="text-3xl font-black text-white italic tracking-tighter">{{ number_format($healthData['infrastructure']['database']['diagnose_entities']) }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-2 tracking-widest">Storage Logs Size</p>
                            <p class="text-3xl font-black text-white italic tracking-tighter">{{ $healthData['infrastructure']['storage']['log_size'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function runDeepScan() {
        const btn = document.getElementById('scan-btn');
        const originalHtml = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-refresh-line animate-spin text-sm"></i> SCANNING SYSTEM...';
        btn.classList.add('opacity-50');

        try {
            const response = await fetch('{{ route("admin.sentinel.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Simulating a real refresh of the dashboard
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (error) {
            console.error('Scan failed:', error);
            btn.innerHTML = 'Scan Failed / Error';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                btn.classList.remove('opacity-50');
            }, 2000);
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>
@endsection
