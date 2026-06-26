@extends('admin.layout')

@section('content')
<div class="space-y-12">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl sm:text-4xl font-heading font-black text-white tracking-tight">Testimonials <span class="text-primary italic">Vault.</span></h1>
            <p class="text-slate-500 font-medium mt-2 uppercase text-[10px] tracking-[0.3em]">Client Reviews Management</p>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="px-8 py-3 bg-primary text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:scale-105 transition-all shadow-xl shadow-primary/20">
            <i class="ri-add-line mr-2"></i> New Testimonial
        </a>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/50 rounded-[2rem] border border-white/5 overflow-hidden backdrop-blur-xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5 border-b border-white/5">
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-500 tracking-widest">Client Name</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-500 tracking-widest">Feedback</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-500 tracking-widest">Rating</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-500 tracking-widest">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black uppercase text-slate-500 tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($testimonials as $testimonial)
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            @if($testimonial->photo)
                                <img src="{{ $testimonial->photo }}" class="w-10 h-10 rounded-full border border-white/10 object-cover" alt="{{ $testimonial->name }}">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-white">{{ $testimonial->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <p class="text-sm text-slate-400 max-w-md line-clamp-2 leading-relaxed">{{ $testimonial->content }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-1 text-[#1FAF5A]">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $testimonial->rating ? 'ri-star-fill' : 'ri-star-line text-slate-700' }} text-base"></i>
                            @endfor
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <form action="{{ route('admin.testimonials.toggle-active', $testimonial->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-1 bg-white/5 rounded-full text-[8px] font-black uppercase tracking-widest {{ $testimonial->is_active ? 'text-primary' : 'text-slate-500' }} border border-white/5 hover:bg-white/10 transition-all cursor-pointer">
                                {{ $testimonial->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-white/10 transition-all">
                                <i class="ri-edit-line text-lg"></i>
                            </a>
                            <form id="deleteTestimonialForm_{{ $testimonial->id }}" action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="CMS.confirmAction('deleteTestimonialForm_{{ $testimonial->id }}', '{{ addslashes($testimonial->name) }}', 'Hapus Testimonial?')" class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-slate-400 hover:bg-red-500 hover:text-white transition-all">
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-slate-500 text-sm">
                        <i class="ri-chat-delete-line text-4xl block mb-2 text-slate-700"></i>
                        No testimonials found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($testimonials->hasPages())
        <div class="p-8 border-t border-white/5">
            {{ $testimonials->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
