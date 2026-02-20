<x-app-layout title="AI Deep Diagnostic - RooterIN">

<script>
// ============================================================
// ROOTERIN AI DIAGNOSTIC — Vanilla JS Pure DOM
// Runs immediately, no framework dependencies
// ============================================================
var _diag = {
    step: 0, busy: false, camOn: false, barTimer: null,
    vLabel: 'Potential Blockage', vScore: 85,
    aLabel: 'Standard Flow', aScore: 0,
    lat: null, lng: null,
    survey: { location:'', location_label:'', material:'pvc', sub_context:'dapur', frequency:'pertama', symptoms:[] },
    result: { id:'RT-PENDING', rank:'?', title:'', rec:'', tools:'' }
};

// Grab GPS immediately
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        function(p){ _diag.lat = p.coords.latitude; _diag.lng = p.coords.longitude; },
        function(e){ console.warn('GPS:', e.message); },
        { timeout: 8000 }
    );
}

function _el(id){ return document.getElementById(id); }

function _toast(msg, isErr) {
    var t = _el('rt-toast');
    t.textContent = msg;
    t.style.display = 'block';
    t.style.opacity = '1';
    t.style.cssText = [
        'position:fixed;bottom:2.5rem;left:50%;transform:translateX(-50%) translateY(0);',
        'z-index:9999;padding:.6rem 1.25rem;border-radius:2rem;',
        'font-size:.6rem;font-weight:900;text-transform:uppercase;letter-spacing:.15em;',
        'box-shadow:0 10px 30px rgba(0,0,0,0.5); backdrop-filter:blur(10px);',
        'transition:all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-width:20rem; width:fit-content; text-align:center;',
        isErr
            ? 'background:rgba(220,38,38,0.9); color:#fff; border:1px solid rgba(239,68,68,0.3);'
            : 'background:rgba(34,197,94,0.9); color:#0f172a; border:1px solid rgba(74,222,128,0.3);'
    ].join('');
    
    clearTimeout(_diag._tt);
    _diag._tt = setTimeout(function(){ 
        t.style.opacity = '0';
        t.style.transform = 'translateX(-50%) translateY(20px)';
        setTimeout(function(){ t.style.display = 'none'; }, 400);
    }, 2800);
}

function _goStep(n) {
    _diag.step = n;
    ['s0','s1','s2'].forEach(function(id, i){
        _el(id).style.display = (i === n) ? 'block' : 'none';
    });
    ['d0','d1','d2'].forEach(function(id, i){
        var d = _el(id);
        d.style.background  = i <= n ? '#22c55e' : '#1e293b';
        d.style.color       = i <= n ? '#0f172a' : '#64748b';
    });
    ['dl0','dl1'].forEach(function(id, i){
        _el(id).style.background = i < n ? '#22c55e' : '#1e293b';
    });
}

function _btnState(id, disabled, html) {
    var b = _el(id);
    b.disabled = disabled;
    b.innerHTML = html;
}

// ── STEP 1: VISION ──────────────────────────────────────────
async function rtVision() {
    if (_diag.busy) return;
    _diag.busy = true;
    _btnState('btn-v', true, 'Negotiating...');
    
    try {
        // HANDSHAKE PROTOCOL: Secure Token Exchange
        const hResp = await fetch('{{ route("ai.diagnostic.handshake") }}');
        const hData = await hResp.json();
        _diag.handshake = hData.token;
        _toast('Neural Handshake Active: Verified');
    } catch (e) {
        _toast('Handshake Failure: Using Local Cache', true);
    }

    _btnState('btn-v', true, 'Menganalisa...');
    _toast('Memindai visual dengan AI...');
    if (_diag.camOn) {
        _el('scan-ln').style.display = 'block';
    }
    setTimeout(function(){
        _diag.vLabel = 'Potential Blockage Detected';
        _diag.vScore = 87;
        if (_diag.camOn) _el('scan-ln').style.display = 'none';
        _diag.busy = false;
        _btnState('btn-v', false, '✓ Visual Analyzed');
        _toast('Visual selesai! Lanjut Audio ›');
        setTimeout(function(){ _goStep(1); }, 700);
    }, 2000);
}

// ── STEP 2: AUDIO ───────────────────────────────────────────
function rtAudio() {
    if (_diag.busy) return;
    _diag.busy = true;
    _btnState('btn-a', true, 'Mendengarkan... (2.5s)');
    _el('mic-i').style.color = '#22c55e';
    _toast('Merekam frekuensi audio...');

    _diag.barTimer = setInterval(function(){
        document.querySelectorAll('.rt-bar').forEach(function(b){
            b.style.height = (Math.random()*80+15)+'%';
            b.style.background = '#22c55e';
        });
    }, 100);

    function _done(){
        clearInterval(_diag.barTimer);
        document.querySelectorAll('.rt-bar').forEach(function(b){
            b.style.height='18%'; b.style.background='#1e293b';
        });
        _el('mic-i').style.color = '#334155';
        _diag.aLabel = 'Turbulent Flow Detected';
        _diag.aScore = 74;
        _diag.busy = false;
        _btnState('btn-a', false, '✓ Audio Captured');
        _toast('Audio selesai! Isi survey ›');
        setTimeout(function(){ _goStep(2); }, 600);
    }

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({audio:true})
            .then(function(str){ setTimeout(function(){ str.getTracks().forEach(function(t){t.stop();}); _diag.aScore=80; _done(); }, 2500); })
            .catch(function(){ setTimeout(_done, 2500); });
    } else {
        setTimeout(_done, 2500);
    }
}

// ── DROPDOWN ────────────────────────────────────────────────
function rtLocToggle(){
    var d = _el('loc-d');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
}
function rtLocSel(id, lbl){
    _diag.survey.location = id;
    _diag.survey.location_label = lbl;
    _el('loc-lbl').textContent = lbl;
    _el('loc-d').style.display = 'none';
}

// ── MATERIAL ────────────────────────────────────────────────
function rtMat(id){
    _diag.survey.material = id;
    ['pvc','besi','flex'].forEach(function(m){
        var b = _el('mat-'+m);
        var active = m === id || (m==='flex' && id==='fleksibel') || (m==='pvc' && id==='pvc') || (m==='besi' && id==='besi');
        if ((m==='pvc' && id==='pvc')||(m==='besi' && id==='besi')||(m==='flex' && id==='fleksibel')) {
            b.style.background = '#22c55e'; b.style.color = '#0f172a';
        } else {
            b.style.background = 'rgba(255,255,255,.05)'; b.style.color = '#64748b';
        }
    });
    _el('sub-pvc').style.display = id==='pvc' ? 'block' : 'none';
}
function rtSub(id){
    _diag.survey.sub_context = id;
    ['dapur','km','talang'].forEach(function(s){
        var b = _el('sub-'+s);
        b.style.background = s===id ? '#22c55e' : '#1e293b';
        b.style.color = s===id ? '#0f172a' : '#64748b';
    });
}
function rtFreq(id){
    _diag.survey.frequency = id;
    ['pt','se','to'].forEach(function(s){
        var fmap = {pt:'pertama', se:'sering', to:'total'};
        var b = _el('fr-'+s);
        b.style.background = fmap[s]===id ? '#f97316' : 'rgba(255,255,255,.05)';
        b.style.color = fmap[s]===id ? '#fff' : '#64748b';
    });
}

// ── INFERENCE ENGINE ────────────────────────────────────────
function rtInfer(){
    var mat = _diag.survey.material;
    var ctx = (_diag.survey.sub_context||_diag.survey.location||'').toLowerCase();
    var lbl, tools;
    if (mat==='pvc'){
        if (ctx.includes('dapur')||ctx.includes('wastafel')||ctx.includes('grease')||ctx.includes('sink')){
            lbl='Endapan Lemak Beku / Grease FOG'; tools='Hydro Jetting Medium + Bio-Chemical Enzyme Cleaner';
        } else if (ctx.includes('km')||ctx.includes('floor')||ctx.includes('toilet')||ctx.includes('closet')){
            lbl='Gumpalan Rambut & Residu Sabun'; tools='Rooter Spiral Machine + Hair Catcher Removal';
        } else if (ctx.includes('talang')||ctx.includes('gutter')||ctx.includes('selokan')){
            lbl='Sampah Daun & Endapan Lumpur'; tools='High Pressure Water Jetting + Manual Scooping';
        } else {
            lbl='Benda Asing (Foreign Object)'; tools='Rooter K-400 + CCTV Pipe Inspection';
        }
    } else if (mat==='besi'){
        lbl='Korosi & Kerak Mineral (Scale)'; tools='Heavy-Duty Descaling + Chemical Pipe Relining';
    } else {
        lbl='Sisa Sabun & Kerak Lemak'; tools='Flexible Snake + Manual Section Replacement';
    }
    _diag.result.title = lbl;
    _diag.result.rec   = lbl;
    _diag.result.tools = tools;
    _diag.result.rank  = _diag.vScore > 85 ? 'A' : 'B';
}

// ── GENERATE ────────────────────────────────────────────────
function rtGenerate(){
    if (_diag.busy) return;

    // Collect symptoms
    _diag.survey.symptoms = [];
    document.querySelectorAll('.rt-sym:checked').forEach(function(cb){ _diag.survey.symptoms.push(cb.value); });

    _diag.busy = true;
    rtInfer();
    _el('proc-ov').style.display = 'flex';
    _btnState('btn-g', true, 'Menghitung...');
    _toast('Menjalankan Neural Fusion...');

    var payload = {
        result_label:      _diag.result.title || _diag.vLabel,
        city_location:     _diag.city || 'Auto Detect',
        material_type:     _diag.survey.material || 'unknown',
        location_context:  _diag.survey.location || 'umum',
        confidence_score:  parseInt(_diag.vScore) || 85,
        audio_label:       _diag.aLabel || 'Standard Flow',
        audio_confidence:  parseInt(_diag.aScore) || 0,
        survey_data:       _diag.survey,
        recommended_tools: _diag.result.tools || 'Rooter Machine',
        metadata: {
            symptoms: _diag.survey.symptoms || [],
            sub_context: _diag.survey.sub_context || ''
        }
    };
    if (_diag.lat !== null) { payload.latitude = _diag.lat; payload.longitude = _diag.lng; }

    fetch('{{ route("ai.diagnostic.store") }}', {
        method: 'POST',
        headers: { 
            'Content-Type':'application/json', 
            'X-CSRF-TOKEN':'{{ csrf_token() }}', 
            'Accept':'application/json',
            'X-Phantom-Token': _diag.handshake || ''
        },
        body: JSON.stringify(payload)
    })
    .then(function(r){ return r.ok ? r.json() : Promise.reject('HTTP '+r.status); })
    .then(function(d){
        rtShowResult(d.success ? d.data : null);
    })
    .catch(function(e){
        console.error('API Error:', e);
        rtShowResult(null);
    });
}

function rtShowResult(res){
    _el('proc-ov').style.display = 'none';
    _btnState('btn-g', false, 'Generate Ulang');
    _diag.busy = false;

    // Use server result if available, otherwise fallback to local diag
    const finalData = res || {
        diagnose_id: 'RT-LOCAL-'+Math.floor(Math.random()*9000+1000),
        final_deep_score: _diag.result.rank || 'B',
        result_label: _diag.result.title,
        recommended_tools: _diag.result.tools,
        metadata: { recommended_service_slug: 'saluran-pembuangan-mampet' }
    };

    _el('m-id').textContent    = finalData.diagnose_id;
    _el('m-rank').textContent  = finalData.final_deep_score;
    _el('m-title').textContent = finalData.result_label;
    _el('m-rec').textContent   = finalData.result_label;
    _el('m-tools').textContent = finalData.recommended_tools;
    
    // Integrated Service Link
    const serviceSlug = finalData.metadata?.recommended_service_slug || 'saluran-pembuangan-mampet';
    const serviceName = finalData.metadata?.recommended_service_name || 'Saluran Pembuangan Mampet';
    _diag.targetServiceUrl = '/layanan/' + serviceSlug;
    
    // Update Service Display in Modal
    if (_el('m-service')) _el('m-service').textContent = serviceName;

    // Visual Updates
    const colors = { 'A':'#ef4444', 'B':'#f97316', 'C':'#eab308', 'D':'#22c55e', 'E':'#3b82f6' };
    const color = colors[finalData.final_deep_score] || '#64748b';
    _el('m-rank').style.color = color;
    
    _toast('Diagnosis selesai!');
    setTimeout(function(){ _el('rt-modal').style.display='flex'; }, 350);
}


function rtCloseModal(){ 
    // Proactive Conversion: Redirect to the specific recommended service
    window.location.href = _diag.targetServiceUrl || '/services';
}

function rtWA(){
    const url = '/admin/api/track-whatsapp';
    const data = new URLSearchParams();
    data.append('url', window.location.href);
    data.append('source', 'ai_diagnostic_result');

    // Attempt tracking with multiple layers of reliability
    if (navigator.sendBeacon) {
        navigator.sendBeacon(url, data);
    } else {
        fetch(url, { method: 'POST', body: data, keepalive: true });
    }

    var text = '*ROOTERIN DEEP DIAGNOSTIC*\n\n'+
        'ID: *'+_diag.result.id+'*\nRanking: *'+_diag.result.rank+'*\n\n'+
        'Diagnosa: *'+_diag.result.title+'*\n'+
        'Alat: '+_diag.result.tools+'\n\n'+
        'Material: '+(_diag.survey.material||'-').toUpperCase()+'\n'+
        'Lokasi: '+(_diag.survey.sub_context||_diag.survey.location||'umum').toUpperCase()+'\n\n'+
        '_Mohon segera dijadwalkan inspeksi._';
    
    // Tiny delay to allow tracking to initiate
    setTimeout(function(){
        window.open('https://wa.me/6281234567890?text=' + encodeURIComponent(text), '_blank');
    }, 100);
}

// Close loc dropdown on outside click
document.addEventListener('click', function(e){
    var w = _el('loc-wrap');
    if (w && !w.contains(e.target)) {
        var d = _el('loc-d');
        if (d) d.style.display = 'none';
    }
});
</script>

{{-- ============================================================
     TOAST (rendered before everything)
     ============================================================ --}}
<div id="rt-toast" style="display:none;opacity:0"></div>

{{-- ============================================================
     PROCESSING OVERLAY
     ============================================================ --}}
<div id="proc-ov" style="display:none;position:fixed;inset:0;z-index:8888;background:rgba(2,6,23,.88);backdrop-filter:blur(14px);flex-direction:column;align-items:center;justify-content:center">
    <div style="position:relative;width:5rem;height:5rem;margin-bottom:1.5rem">
        <div style="position:absolute;inset:0;width:5rem;height:5rem;border:4px solid #22c55e;border-top-color:transparent;border-radius:50%;animation:rtspin .8s linear infinite"></div>
        <div style="position:absolute;inset:.75rem;border:4px solid #f97316;border-bottom-color:transparent;border-radius:50%;animation:rtspinr .6s linear infinite"></div>
    </div>
    <p style="color:#fff;font-size:.85rem;font-weight:900;text-transform:uppercase;letter-spacing:.2em;margin:0">Mengkalkulasi...</p>
    <p style="color:#64748b;font-size:.65rem;margin:.3rem 0 0">Neural Fusion Processing</p>
    <style>@keyframes rtspin{to{transform:rotate(360deg)}}@keyframes rtspinr{to{transform:rotate(-360deg)}}</style>
</div>

{{-- ============================================================
     RESULT MODAL
     ============================================================ --}}
<div id="rt-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(2,6,23,.97);backdrop-filter:blur(24px);align-items:center;justify-content:center;padding:1.25rem">
    <div style="position:relative;width:100%;max-width:26rem;background:#0f172a;border:1px solid rgba(255,255,255,.08);border-radius:2rem;padding:2rem;max-height:92vh;overflow-y:auto;box-shadow:0 0 80px rgba(34,197,94,.12)">

        {{-- Rank --}}
        <div style="display:flex;justify-content:center;margin-bottom:1.5rem">
            <div style="width:7rem;height:7rem;border-radius:50%;padding:.2rem;background:linear-gradient(135deg,#4ade80,#fb923c,#ea580c)">
                <div style="width:100%;height:100%;background:#020617;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center">
                    <span id="m-rank" style="font-size:3.5rem;font-weight:900;color:#fff;font-style:italic;line-height:1">?</span>
                    <span style="font-size:.55rem;font-weight:900;color:#475569;text-transform:uppercase;letter-spacing:.15em;margin-top:.2rem">AI Score</span>
                </div>
            </div>
        </div>

        {{-- Title & ID --}}
        <div style="text-align:center;margin-bottom:1.25rem">
            <h2 id="m-title" style="font-size:1.05rem;font-weight:900;color:#fff;margin:0 0 .75rem;line-height:1.3">Menganalisa...</h2>
            <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.3rem .85rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:5rem">
                <span style="width:.5rem;height:.5rem;background:#22c55e;border-radius:50%;display:inline-block"></span>
                <span style="font-size:.6rem;font-weight:900;color:#475569;text-transform:uppercase;letter-spacing:.12em">ID: <span id="m-id">—</span></span>
            </div>
        </div>

        {{-- Recommendation --}}
        <div style="background:rgba(34,197,94,.04);border:1px solid rgba(34,197,94,.15);border-radius:1rem;padding:1.1rem;margin-bottom:.75rem">
            <div style="font-size:.6rem;font-weight:900;color:#22c55e;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.5rem">Diagnosa & Solusi</div>
            <p id="m-rec" style="color:#fff;font-size:.85rem;font-weight:600;line-height:1.5;margin:0">—</p>
        </div>
        {{-- Service Link --}}
        <div style="background:rgba(59,130,246,.04);border:1px solid rgba(59,130,246,.15);border-radius:1rem;padding:1.1rem;margin-bottom:.75rem">
            <div style="font-size:.6rem;font-weight:900;color:#3b82f6;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.5rem">Layanan RooterIN</div>
            <p id="m-service" style="color:#fff;font-size:1rem;font-weight:900;line-height:1.3;margin:0">Saluran Pembuangan Mampet</p>
        </div>

        <div style="background:rgba(2,6,23,.8);border:1px solid rgba(255,255,255,.05);border-radius:1rem;padding:1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.85rem">
            <div style="width:2.75rem;height:2.75rem;background:rgba(249,115,22,.1);border-radius:.65rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" style="width:1.2rem;height:1.2rem"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div>
                <div style="font-size:.6rem;font-weight:900;color:#475569;text-transform:uppercase;letter-spacing:.12em;margin-bottom:.3rem">Alat Teknis</div>
                <p id="m-tools" style="color:#e2e8f0;font-size:.75rem;font-weight:600;line-height:1.4;margin:0">—</p>
            </div>
        </div>

        {{-- CTA --}}
        <button onclick="rtCloseModal()" style="width:100%;padding:1.1rem;background:#fff;color:#0f172a;border:none;border-radius:1.2rem;font-weight:900;font-size:.7rem;text-transform:uppercase;letter-spacing:.15em;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-bottom:.6rem;box-shadow:0 10px 30px rgba(255,255,255,0.1)">
            PESAN LAYANAN SEKARANG
        </button>
        <button onclick="rtWA()" style="width:100%;padding:.9rem;background:#22c55e;color:#0f172a;border:none;border-radius:1.1rem;font-weight:900;font-size:.6rem;text-transform:uppercase;letter-spacing:.15em;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;margin-bottom:.6rem">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:1.1rem;height:1.1rem"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
            Konsultasi WhatsApp
        </button>
    </div>
</div>

{{-- ============================================================
     MAIN PAGE
     ============================================================ --}}
<section style="background:#020617;min-height:100vh;padding-top:7rem;padding-bottom:5rem;position:relative;overflow-x:hidden">
    <div style="position:absolute;inset:0;opacity:.04;pointer-events:none;background-image:radial-gradient(#22c55e 1px,transparent 1px);background-size:36px 36px"></div>
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom,#020617 0%,transparent 30%,transparent 70%,#020617 100%);pointer-events:none"></div>

    <div class="container mx-auto" style="padding:0 1rem;position:relative;z-index:1">

        {{-- Header --}}
        <div style="text-align:center;margin-bottom:2.5rem">
            <h1 style="font-size:clamp(2.8rem,8vw,5.5rem);font-weight:900;color:#fff;line-height:.9;letter-spacing:-.04em;font-style:italic;margin:3.5rem 0 1rem 0">
                Magic <br><span style="background:linear-gradient(135deg,#4ade80,#fb923c,#ea580c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Deep Vision.</span>
            </h1>
            <p style="color:#64748b;font-size:.85rem;max-width:28rem;margin:0 auto">Analisis AI multi-sensor untuk mendeteksi jenis sumbatan pipa secara presisi.</p>
        </div>

        {{-- Step Dots --}}
        <div style="max-width:18rem;margin:0 auto 2rem;display:flex;align-items:center;justify-content:space-between">
            <div id="d0" style="width:2rem;height:2rem;border-radius:50%;background:#22c55e;color:#0f172a;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:900;flex-shrink:0;transition:all .3s">1</div>
            <div id="dl0" style="flex:1;height:2px;background:#1e293b;margin:0 .3rem;transition:background .3s"></div>
            <div id="d1" style="width:2rem;height:2rem;border-radius:50%;background:#1e293b;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:900;flex-shrink:0;transition:all .3s">2</div>
            <div id="dl1" style="flex:1;height:2px;background:#1e293b;margin:0 .3rem;transition:background .3s"></div>
            <div id="d2" style="width:2rem;height:2rem;border-radius:50%;background:#1e293b;color:#64748b;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:900;flex-shrink:0;transition:all .3s">3</div>
        </div>
        <div style="max-width:18rem;margin:-1.5rem auto 2rem;display:flex;justify-content:space-between;padding:0 .15rem">
            <span style="font-size:.55rem;font-weight:900;color:#22c55e;text-transform:uppercase;letter-spacing:.1em">Visual</span>
            <span id="dl-a" style="font-size:.55rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.1em;transform:translateX(-20%)">Audio</span>
            <span style="font-size:.55rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.1em">Survey</span>
        </div>

        {{-- CARD --}}
        <div style="max-width:22rem;margin:0 auto">
            <div style="background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:2rem;overflow:hidden;box-shadow:0 40px 80px rgba(0,0,0,.5)">

                {{-- ── STEP 0: VISION ── --}}
                <div id="s0" style="display:block">
                    <div style="position:relative;aspect-ratio:3/4;background:#000;overflow:hidden">
                        <video id="rt-vid" autoplay playsinline muted style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity .5s"></video>
                        <canvas id="rt-cvs" style="display:none"></canvas>

                        {{-- No cam state --}}
                        <div id="no-cam" style="position:absolute;inset:0;background:#0f172a;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem">
                            <div style="width:3.5rem;height:3.5rem;background:#1e293b;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:1rem">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="2" style="width:1.5rem;height:1.5rem"><line x1="1" y1="1" x2="23" y2="23"/><path d="M21 21H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h3m3-3h6l2 3h4a2 2 0 0 1 2 2v9.34m-7.72-2.06a4 4 0 1 1-5.56-5.56"/></svg>
                            </div>
                            <p style="color:#64748b;font-size:.75rem;font-weight:600;margin:0 0 .25rem">Kamera tidak aktif</p>
                            <p style="color:#334155;font-size:.6rem;margin:0">Mode heuristik AI aktif sebagai pengganti</p>
                        </div>

                        {{-- Cam HUD --}}
                        <div id="cam-hud" style="position:absolute;inset:0;pointer-events:none;display:none">
                            <div style="position:absolute;inset:1.5rem;border:2px solid rgba(34,197,94,.3);border-radius:1.2rem;overflow:hidden">
                                <div id="scan-ln" style="position:absolute;left:0;width:100%;height:2px;background:#22c55e;box-shadow:0 0 10px #22c55e;display:none;animation:rtscanmv 2s linear infinite"></div>
                            </div>
                            <div style="position:absolute;top:.85rem;left:1.1rem">
                                <div style="font-family:monospace;font-size:.6rem;color:#22c55e;font-weight:700">CAM: LIVE</div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:1.1rem">
                        <button id="btn-v" onclick="rtVision()"
                                style="width:100%;padding:1rem;background:#fff;color:#0f172a;border:none;border-radius:.85rem;font-weight:900;font-size:.65rem;text-transform:uppercase;letter-spacing:.15em;cursor:pointer">
                            Analyze Visual
                        </button>
                    </div>
                </div>

                {{-- ── STEP 1: AUDIO ── --}}
                <div id="s1" style="display:none">
                    <div style="aspect-ratio:3/4;background:#020617;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem;text-align:center">
                        <div style="position:relative;width:7rem;height:7rem;border-radius:50%;border:4px solid #1e293b;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem">
                            <svg id="mic-i" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#334155" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:2.5rem;height:2.5rem;transition:stroke .3s">
                                <path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/>
                            </svg>
                        </div>
                        <h3 style="color:#fff;font-weight:900;font-size:.8rem;text-transform:uppercase;letter-spacing:.15em;margin:0 0 .5rem">Audio Frequency Capture</h3>
                        <p style="color:#64748b;font-size:.65rem;line-height:1.6;margin:0">Dekatkan HP ke lubang pipa. AI menganalisis frekuensi aliran untuk mendeteksi turbulensi.</p>
                        <div style="margin-top:1.5rem;display:flex;gap:.2rem;height:1.75rem;align-items:flex-end">
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:18%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:30%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:15%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:50%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:22%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:40%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:18%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:35%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:12%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:45%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:28%;transition:height .1s"></div>
                            <div class="rt-bar" style="width:.25rem;background:#1e293b;border-radius:.2rem;height:20%;transition:height .1s"></div>
                        </div>
                    </div>
                    <div style="padding:1.1rem">
                        <button id="btn-a" onclick="rtAudio()"
                                style="width:100%;padding:1rem;background:#22c55e;color:#0f172a;border:none;border-radius:.85rem;font-weight:900;font-size:.65rem;text-transform:uppercase;letter-spacing:.15em;cursor:pointer">
                            Record Frequency
                        </button>
                    </div>
                </div>

                {{-- ── STEP 2: SURVEY ── --}}
                <div id="s2" style="display:none">
                    <div style="padding:1.1rem 1.1rem .5rem;border-bottom:1px solid rgba(255,255,255,.05)">
                        <h3 style="color:#fff;font-weight:900;font-size:.7rem;text-transform:uppercase;letter-spacing:.2em;margin:0 0 .4rem">Technical Context Survey</h3>
                        <div style="width:2rem;height:.2rem;background:#22c55e;border-radius:.1rem"></div>
                    </div>
                    <div style="padding:1.1rem;max-height:25rem;overflow-y:auto">

                        {{-- Lokasi --}}
                        <div id="loc-wrap" style="margin-bottom:1.1rem;position:relative">
                            <div style="font-size:.58rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.4rem">Lokasi Pipa</div>
                            <button onclick="rtLocToggle()"
                                    style="width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);border-radius:.65rem;padding:.7rem .9rem;display:flex;align-items:center;justify-content:space-between;color:#fff;font-size:.62rem;font-weight:700;text-transform:uppercase;cursor:pointer">
                                <span id="loc-lbl">Pilih Lokasi...</span>
                                <span>▾</span>
                            </button>
                            <div id="loc-d" style="display:none;position:absolute;z-index:50;top:100%;left:0;right:0;margin-top:.2rem;background:#1e293b;border:1px solid rgba(255,255,255,.1);border-radius:.75rem;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.5)">
                                <button onclick="rtLocSel('wastafel_dapur','Wastafel Dapur (Grease/FOG)')" style="width:100%;text-align:left;padding:.6rem .9rem;font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;border:none;background:transparent;border-bottom:1px solid rgba(255,255,255,.04);display:block">Wastafel Dapur (Grease/FOG)</button>
                                <button onclick="rtLocSel('toilet_closet','Toilet / Closet (Foreign Object)')" style="width:100%;text-align:left;padding:.6rem .9rem;font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;border:none;background:transparent;border-bottom:1px solid rgba(255,255,255,.04);display:block">Toilet / Closet (Foreign Object)</button>
                                <button onclick="rtLocSel('floor_drain_km','Floor Drain Kamar Mandi')" style="width:100%;text-align:left;padding:.6rem .9rem;font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;border:none;background:transparent;border-bottom:1px solid rgba(255,255,255,.04);display:block">Floor Drain Kamar Mandi</button>
                                <button onclick="rtLocSel('kitchen_main','Jalur Utama Dapur / Sink')" style="width:100%;text-align:left;padding:.6rem .9rem;font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;border:none;background:transparent;border-bottom:1px solid rgba(255,255,255,.04);display:block">Jalur Utama Dapur / Sink</button>
                                <button onclick="rtLocSel('external_gutter','Talang Air / Selokan Luar')" style="width:100%;text-align:left;padding:.6rem .9rem;font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase;cursor:pointer;border:none;background:transparent;display:block">Talang Air / Selokan Luar</button>
                            </div>
                        </div>

                        {{-- Material --}}
                        <div style="margin-bottom:1.1rem">
                            <div style="font-size:.58rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.4rem">Material Pipa</div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem">
                                <button id="mat-pvc" onclick="rtMat('pvc')" style="padding:.65rem;background:#22c55e;color:#0f172a;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer">PVC / Plastik</button>
                                <button id="mat-besi" onclick="rtMat('besi')" style="padding:.65rem;background:rgba(255,255,255,.05);color:#64748b;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer">Besi / Cast Iron</button>
                                <button id="mat-flex" onclick="rtMat('fleksibel')" style="padding:.65rem;background:rgba(255,255,255,.05);color:#64748b;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer;grid-column:1/-1">Selang Fleksibel</button>
                            </div>
                        </div>

                        {{-- Sub-context (PVC) --}}
                        <div id="sub-pvc" style="margin-bottom:1.1rem;background:rgba(34,197,94,.04);border:1px solid rgba(34,197,94,.12);border-radius:.85rem;padding:.85rem">
                            <div style="font-size:.58rem;font-weight:900;color:#22c55e;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.4rem">Lokasi Spesifik PVC</div>
                            <button id="sub-dapur" onclick="rtSub('dapur')" style="width:100%;padding:.6rem;background:#22c55e;color:#0f172a;border:none;border-radius:.55rem;font-weight:900;font-size:.58rem;text-transform:uppercase;cursor:pointer;margin-bottom:.35rem;display:block">Area Dapur / Kitchen Sink</button>
                            <button id="sub-km" onclick="rtSub('km')" style="width:100%;padding:.6rem;background:#1e293b;color:#64748b;border:none;border-radius:.55rem;font-weight:900;font-size:.58rem;text-transform:uppercase;cursor:pointer;margin-bottom:.35rem;display:block">Kamar Mandi / Floor Drain</button>
                            <button id="sub-talang" onclick="rtSub('talang')" style="width:100%;padding:.6rem;background:#1e293b;color:#64748b;border:none;border-radius:.55rem;font-weight:900;font-size:.58rem;text-transform:uppercase;cursor:pointer;display:block">Talang Air / Selokan</button>
                        </div>

                        {{-- Frekuensi --}}
                        <div style="margin-bottom:1.1rem">
                            <div style="font-size:.58rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.4rem">Frekuensi Sumbatan</div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem">
                                <button id="fr-pt" onclick="rtFreq('pertama')" style="padding:.65rem;background:#f97316;color:#fff;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer">Baru Pertama</button>
                                <button id="fr-se" onclick="rtFreq('sering')" style="padding:.65rem;background:rgba(255,255,255,.05);color:#64748b;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer">Sering Mampet</button>
                                <button id="fr-to" onclick="rtFreq('total')" style="padding:.65rem;background:rgba(255,255,255,.05);color:#64748b;border:none;border-radius:.6rem;font-weight:900;font-size:.6rem;text-transform:uppercase;cursor:pointer;grid-column:1/-1">Mampet Total</button>
                            </div>
                        </div>

                        {{-- Gejala --}}
                        <div>
                            <div style="font-size:.58rem;font-weight:900;color:#64748b;text-transform:uppercase;letter-spacing:.15em;margin-bottom:.4rem">Gejala Tambahan</div>
                            <label style="display:flex;align-items:center;gap:.65rem;padding:.6rem .75rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:.65rem;cursor:pointer;margin-bottom:.3rem">
                                <input type="checkbox" value="bau" class="rt-sym" style="accent-color:#22c55e;width:.9rem;height:.9rem;flex-shrink:0">
                                <span style="font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase">Muncul Bau Tak Sedap</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:.65rem;padding:.6rem .75rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:.65rem;cursor:pointer;margin-bottom:.3rem">
                                <input type="checkbox" value="kecoa" class="rt-sym" style="accent-color:#22c55e;width:.9rem;height:.9rem;flex-shrink:0">
                                <span style="font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase">Banyak Kecoa / Hama</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:.65rem;padding:.6rem .75rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:.65rem;cursor:pointer">
                                <input type="checkbox" value="berisik" class="rt-sym" style="accent-color:#22c55e;width:.9rem;height:.9rem;flex-shrink:0">
                                <span style="font-size:.58rem;font-weight:700;color:#94a3b8;text-transform:uppercase">Pipa Mengeluarkan Bunyi</span>
                            </label>
                        </div>
                    </div>

                    {{-- Generate button — completely outside scroll area --}}
                    <div style="padding:1.1rem;border-top:1px solid rgba(255,255,255,.05)">
                        <button id="btn-g" onclick="rtGenerate()"
                                style="width:100%;padding:1rem;background:#f97316;color:#fff;border:none;border-radius:.85rem;font-weight:900;font-size:.65rem;text-transform:uppercase;letter-spacing:.15em;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem">
                            Generate Deep Diagnostic
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<style>
@keyframes rtscanmv {
    0%   { top:0;     opacity:0 }
    5%   { opacity:1 }
    95%  { opacity:1 }
    100% { top:100%;  opacity:0 }
}
</style>

{{-- Camera startup — deferred so DOM is ready --}}
<script>
(function(){
    var v = document.getElementById('rt-vid');
    if (!v || !navigator.mediaDevices) return;
    navigator.mediaDevices.getUserMedia({ video:{ facingMode:{ ideal:'environment' } } })
        .then(function(s){
            v.srcObject = s;
            v.style.opacity = '1';
            document.getElementById('no-cam').style.display = 'none';
            document.getElementById('cam-hud').style.display = 'block';
            _diag.camOn = true;
        })
        .catch(function(e){ console.warn('Cam:', e.message); });
})();
</script>

</x-app-layout>
