@extends('admin.layout')

@section('content')
@php 
    $phantomToken = app(\App\Services\Security\PhantomSyncService::class)->generateToken([
        'user_id' => auth()->id(),
        'ip' => request()->ip(),
        'action' => 'wikipipa_automator'
    ]); 
@endphp
<div class="space-y-12" x-data="wikiAutomator()">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-4 inline-block">Knowledge Base Engine</span>
            <h1 class="text-4xl font-heading font-black text-white leading-none">Wiki<span class="text-primary italic">Pipa</span> Automator.</h1>
        </div>
        <button @click="showModal = true" class="px-8 py-4 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-primary/20 flex items-center gap-3">
            <i class="ri-add-line text-lg"></i>
            Tambah Entitas Baru
        </button>
    </div>

    <!-- Stats Hub -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-8 bg-white/5 border border-white/5 rounded-3xl">
            <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Total Entities</div>
            <div class="text-3xl font-black text-white">{{ \App\Models\WikiEntity::count() }}</div>
        </div>
        <div class="p-8 bg-white/5 border border-white/5 rounded-3xl">
            <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Top Authority Category</div>
            <div class="text-3xl font-black text-primary">Infrastruktur</div>
        </div>
        <div class="p-8 bg-white/5 border border-white/5 rounded-3xl">
            <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4">Semantic Signals</div>
            <div class="text-3xl font-black text-green-500">Active</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white/5 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">Title & Category</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">Wikidata ID</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest">Attributes</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($entities as $entity)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="px-8 py-6">
                        <div class="font-bold text-white mb-1">{{ $entity->title }}</div>
                        <div class="text-[9px] font-black text-primary uppercase tracking-widest">{{ $entity->category }}</div>
                    </td>
                    <td class="px-8 py-6 text-xs text-slate-500 font-mono">
                        {{ $entity->wikidata_id ?: 'NO_LINK' }}
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($entity->attributes ?? [] as $key => $val)
                            <span class="px-2 py-1 bg-white/5 rounded-lg text-[8px] font-bold text-slate-400 uppercase tracking-tighter">{{ $key }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <form action="{{ route('admin.wiki.destroy', $entity->id) }}" method="POST" onsubmit="return confirm('Hapus entitas ini?')">
                            @csrf @method('DELETE')
                            <button class="text-slate-600 hover:text-red-500 transition-colors">
                                <i class="ri-delete-bin-line text-xl"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-8 border-t border-white/5">
            {{ $entities->links() }}
        </div>
    </div>

    <!-- Magic Modal (Creation + AI Automator) -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6" x-transition x-cloak>
        <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-xl" @click="showModal = false"></div>
        <div class="relative w-full max-w-2xl bg-slate-900 border border-white/10 rounded-[3rem] p-12 shadow-3xl overflow-y-auto max-h-[90vh] no-scrollbar">
            <h2 class="text-3xl font-heading font-black text-white mb-8">Deploy New <span class="text-primary italic">Entity</span>.</h2>
            
            <form action="{{ route('admin.wiki.store') }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Entity Name</label>
                    <div class="flex gap-4">
                        <input type="text" x-model="name" name="title" placeholder="e.g. Spiral Cleaning Machine" class="flex-grow bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white focus:border-primary outline-none">
                        <button type="button" @click="autoInference" class="px-6 bg-secondary text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-secondary/80 disabled:opacity-50" :disabled="loading">
                            <span x-show="!loading">AI Automator</span>
                            <span x-show="loading">Thinking...</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Category</label>
                        <select name="category" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white appearance-none focus:border-primary outline-none transition-all">
                            <option value="Material Pipa" class="bg-slate-900">Material Pipa</option>
                            <option value="Alat Teknisi" class="bg-slate-900">Alat Teknisi</option>
                            <option value="Infrastruktur" class="bg-slate-900">Infrastruktur</option>
                            <option value="Kimia" class="bg-slate-900">Kimia</option>
                            <option value="Masalah Plumbing" class="bg-slate-900">Masalah Plumbing</option>
                            <option value="Spesialis" class="bg-slate-900">Spesialis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Wikidata ID (Optional)</label>
                        <input type="text" name="wikidata_id" placeholder="Q12345" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Technical Description</label>
                    <x-admin.rich-editor name="description" :value="''" />
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Technical Attributes (JSON)</label>
                    <textarea x-model="attributes" name="attributes_json" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white font-mono text-xs h-24"></textarea>
                    <p class="mt-2 text-[8px] text-slate-600 uppercase font-black italic">Formata: {"Kapasitas": "50L", "Material": "Steel"}</p>
                </div>

                <button type="submit" class="w-full py-5 bg-primary text-white rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-[#e65a00] transition-all">
                    Finalize & Inject to Wiki
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function wikiAutomator() {
    return {
        showModal: false,
        name: '',
        description: '',
        attributes: '',
        loading: false,
        
        async autoInference() {
            if (!this.name) return alert('Masukkan nama entitas dulu!');
            
            this.loading = true;
            try {
                const response = await fetch('{{ route('admin.wiki.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Phantom-Token': '{{ $phantomToken }}'
                    },
                    body: JSON.stringify({ name: this.name })
                });
                
                const data = await response.json();
                this.description = data.description;
                if (window.cmsEditors && window.cmsEditors['description']) {
                    window.cmsEditors['description'].setData(data.description);
                }
                this.attributes = JSON.stringify(data.attributes, null, 2);
            } catch (e) {
                alert('AI Engine failure. Manual input required.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
