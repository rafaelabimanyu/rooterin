@props(['items' => []])

@if(count($items) > 0)
<section class="pt-20 pb-36 sm:py-32 bg-slate-950 text-white relative overflow-hidden border-t border-white/5">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 left-0 w-96 h-96 bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-0 w-96 h-96 bg-[#1FAF5A]/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-16 sm:mb-24">
            <span class="text-[#1FAF5A] font-black tracking-[0.3em] uppercase text-[10px] sm:text-xs block mb-4">
                TESTIMONIALS & TRUST
            </span>
            <h2 class="text-3xl sm:text-5xl font-heading font-black text-white leading-tight">
                Ulasan Pelanggan <span class="text-[#1FAF5A] italic">Setia Kami.</span>
            </h2>
            <div class="mt-4 flex justify-center">
                <div class="h-1.5 w-16 bg-[#1FAF5A] rounded-full"></div>
            </div>
        </div>

        <!-- Slider/Carousel Container with Alpine.js -->
        <div x-data="{ 
            active: 0,
            skip: 1,
            isPaused: false,
            total: {{ count($items) }},
            init() {
                setInterval(() => {
                    if (!this.isPaused) {
                        this.next();
                    }
                }, 4500);
            },
            next() {
                this.active = (this.active + this.skip >= this.total) ? 0 : this.active + this.skip;
                this.scroll();
            },
            prev() {
                this.active = (this.active - this.skip < 0) ? this.total - 1 : this.active - this.skip;
                this.scroll();
            },
            scroll() {
                const el = this.$refs.slider;
                const card = el.firstElementChild;
                if (card) {
                    const width = card.getBoundingClientRect().width + 24; // Width + gap
                    el.scrollTo({
                        left: width * this.active,
                        behavior: 'smooth'
                    });
                }
            }
        }" 
        @mouseenter="isPaused = true" 
        @mouseleave="isPaused = false"
        @touchstart="isPaused = true"
        @touchend="isPaused = false"
        class="relative">

            <!-- Slider Wrapper -->
            <div x-ref="slider" class="flex gap-6 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-10">
                @foreach($items as $index => $item)
                    <div class="snap-start shrink-0 w-full sm:w-[450px] md:w-[480px] lg:w-[500px]">
                        <div class="bg-slate-900/50 p-8 sm:p-10 rounded-[2.5rem] border border-white/5 backdrop-blur-xl h-full flex flex-col justify-between transition-all duration-300 hover:border-[#1FAF5A]/30 hover:shadow-2xl hover:shadow-[#1FAF5A]/5 group relative overflow-hidden">
                            <!-- Premium Tech/Industrial Corner Lines -->
                            <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-transparent group-hover:border-[#1FAF5A]/20 transition-all duration-500 rounded-tr-[2.5rem]"></div>
                            
                            <div>
                                <!-- Rating Stars -->
                                <div class="flex items-center gap-1.5 mb-6 text-[#1FAF5A]">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 {{ $i <= $item->rating ? 'text-[#1FAF5A]' : 'text-slate-800' }}">
                                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                        </svg>
                                    @endfor
                                </div>

                                <!-- Content Review -->
                                <p class="text-slate-300 text-base sm:text-lg leading-relaxed mb-8 font-medium">
                                    "{{ $item->content }}"
                                </p>
                            </div>

                            <!-- Client Profile Info -->
                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                @if($item->photo)
                                    <div class="w-14 h-14 rounded-full border border-white/10 overflow-hidden relative flex-shrink-0">
                                        <img src="{{ $item->photo }}" class="w-full h-full object-cover" alt="{{ $item->name }}">
                                    </div>
                                @else
                                    <div class="w-14 h-14 rounded-full bg-[#1FAF5A]/10 border border-[#1FAF5A]/20 flex items-center justify-center text-[#1FAF5A] font-bold text-lg flex-shrink-0">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold text-white text-base sm:text-lg tracking-wide">{{ $item->name }}</h4>
                                    <p class="text-[10px] text-slate-500 font-black tracking-[0.2em] uppercase mt-0.5">Verified Client</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Controls (Dot Navigation & Arrows) -->
            @if(count($items) > 1)
            <div class="flex items-center justify-between mt-6 pb-6 sm:pb-0">
                <!-- Dot indicators -->
                <div class="flex gap-2">
                    @foreach($items as $index => $item)
                        <button @click="active = {{ $index }}; scroll();" 
                                :class="active === {{ $index }} ? 'bg-[#1FAF5A] w-8' : 'bg-slate-800 w-2.5'" 
                                class="h-2.5 rounded-full transition-all duration-300 focus:outline-none"></button>
                    @endforeach
                </div>

                <!-- Navigation buttons -->
                <div class="flex gap-3">
                    <button @click="prev()" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:border-[#1FAF5A]/50 hover:bg-slate-900 transition-all focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button @click="next()" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-slate-400 hover:text-white hover:border-[#1FAF5A]/50 hover:bg-slate-900 transition-all focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
</section>
@endif
