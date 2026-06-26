<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RooterIn Admin - Dashboard Hub</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-300">

    <div class="flex min-h-screen w-full" x-data="{ sidebarOpen: false }">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden" @click="sidebarOpen = false" x-transition.opacity style="display: none;"></div>

        <!-- Sidebar (Fixed) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="w-64 transform lg:translate-x-0 transition-transform duration-300 border-r border-white/5 flex flex-col fixed top-0 left-0 h-screen z-50 overflow-hidden bg-slate-900 shadow-2xl">
            <!-- Pinned Header -->
            <div class="p-8 border-b border-white/5 flex-shrink-0 bg-slate-900">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/20">
                        <i class="ri-flashlight-fill text-2xl"></i>
                    </div>
                    <span class="font-heading font-black text-xl text-white tracking-widest">ROOTER<span class="text-primary">IN</span></span>
                </div>
                <p class="text-[9px] text-gray-500 font-black tracking-[0.4em] uppercase mt-2">CMS Control Hub</p>
            </div>

            <!-- Scrollable Navigation Area -->
            <nav class="flex-grow p-4 space-y-1 overflow-y-auto no-scrollbar scroll-smooth">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-dashboard-3-line text-xl"></i>
                    <span class="font-bold text-sm">Dashboard</span>
                </a>

                <!-- GROUP A: KONTEN UTAMA -->
                <div class="pt-6 pb-2 px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                    <i class="ri-folder-open-line"></i> Core Assets
                </div>
                
                <a href="{{ route('admin.posts.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.posts.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-article-line text-xl"></i>
                    <span class="text-sm font-bold">Tips & Trik</span>
                </a>
                <a href="{{ route('admin.services.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.services.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-customer-service-2-line text-xl"></i>
                    <span class="text-sm font-bold">Layanan</span>
                </a>
                <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.projects.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-gallery-line text-xl"></i>
                    <span class="text-sm font-bold">Galeri Proyek</span>
                </a>
                <a href="{{ route('admin.testimonials.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.testimonials.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-chat-heart-line text-xl"></i>
                    <span class="text-sm font-bold">Testimonial</span>
                </a>
                <a href="{{ route('admin.media.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.media.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-image-2-line text-xl"></i>
                    <span class="text-sm font-bold">Media Library</span>
                </a>
                <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-question-answer-line text-xl"></i>
                    <span class="text-sm font-bold">Pusat FAQ</span>
                </a>
                <div class="py-2">
                    <a href="{{ route('admin.wiki.index') }}" class="flex items-center justify-between gap-4 px-4 py-3 rounded-xl transition-all border {{ request()->routeIs('admin.wiki.*') ? 'bg-accent/10 border-accent/50 text-accent shadow-lg shadow-accent/20' : 'bg-gradient-to-r from-slate-900 to-slate-800 border-white/5 hover:border-accent text-slate-300' }}">
                        <div class="flex items-center gap-4">
                            <i class="ri-book-read-line text-xl {{ request()->routeIs('admin.wiki.*') ? 'animate-pulse' : 'text-accent' }}"></i>
                            <span class="text-sm font-bold font-heading">WikiPipa Automator</span>
                        </div>
                        <i class="ri-magic-line text-xs opacity-50"></i>
                    </a>
                </div>

                <!-- GROUP B: KONFIGURASI -->
                <div class="pt-6 pb-2 px-4 text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-2">
                    <i class="ri-settings-3-line"></i> Systems
                </div>
                
                <a href="{{ route('admin.partners.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.partners.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-building-2-line text-xl"></i>
                    <span class="text-sm font-bold">Industrial Alliances</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-global-line text-xl"></i>
                    <span class="text-sm font-bold">Site Settings</span>
                </a>
                <a href="{{ route('admin.seo.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.seo.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-search-eye-line text-xl"></i>
                    <span class="text-sm font-bold">SEO Central</span>
                </a>
                <a href="{{ route('admin.ai.intelligence.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.ai.intelligence.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-radar-box-line text-xl"></i>
                    <span class="text-sm font-bold font-heading">AI Business Analytics</span>
                </a>
                <a href="{{ route('admin.messages.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.messages.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-mail-line text-xl"></i>
                    <span class="text-sm font-bold">Messages</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary border-l-4 border-primary' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-group-line text-xl"></i>
                    <span class="text-sm font-bold">Admin Users</span>
                </a>

                <!-- GROUP C: KEAMANAN & LOG -->
                <div class="pt-6 pb-2 px-4 text-[10px] font-black text-rose-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="ri-shield-keyhole-line"></i> Security Vault
                </div>
                
                <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.activity-logs.*') ? 'bg-rose-900/20 text-rose-500 border-l-4 border-rose-500' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-history-line text-xl"></i>
                    <span class="text-sm font-bold">System Logs</span>
                </a>
                <a href="{{ route('admin.vault.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.vault.*') ? 'bg-rose-900/20 text-rose-500 border-l-4 border-rose-500' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-lock-2-line text-xl"></i>
                    <span class="text-sm font-bold">Vault Access</span>
                </a>

                <a href="{{ route('admin.sentinel.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.sentinel.*') ? 'bg-rose-900/20 text-rose-500 border-l-4 border-rose-500' : 'hover:bg-white/5 text-slate-400 hover:text-white' }}">
                    <i class="ri-radar-line text-xl"></i>
                    <span class="text-sm font-bold">System Sentinel</span>
                </a>
                
                <!-- Spacer for bottom scroll -->
                <div class="h-6"></div>
            </nav>

            <!-- Pinned Footer -->
            <div class="p-6 border-t border-white/5 bg-slate-900 flex-shrink-0">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-950 shadow-inner">
                    <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center">
                        <i class="ri-user-settings-fill text-primary"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Admin RooterIn</p>
                        <p class="text-[9px] text-gray-500 font-black uppercase">Super Admin</p>
                    </div>
                </div>
                <a href="/" target="_blank" class="mt-4 flex items-center justify-center gap-2 w-full py-2 rounded-lg bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all border border-primary/20 hover:border-primary">
                    <i class="ri-external-link-line"></i>
                    View Live Site
                </a>
            </div>
        </aside>

        <!-- Main Content Area (Offset by Sidebar Width) -->
        <div class="flex-grow flex flex-col min-w-0 bg-slate-950 min-h-screen lg:ml-64">
            <!-- Mobile Navigation Bar -->
            <header class="lg:hidden flex items-center justify-between bg-slate-900 border-b border-white/5 p-4 sticky top-0 z-30 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary/20">
                        <i class="ri-flashlight-fill text-lg"></i>
                    </div>
                    <span class="font-heading font-black text-lg text-white tracking-widest">ROOTER<span class="text-primary">IN</span></span>
                </div>
                <button @click="sidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-lg bg-white/5 text-white hover:bg-primary hover:text-white transition-all">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </header>

            <main class="flex-grow p-4 sm:p-8 lg:p-12 overflow-x-hidden">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- NEW LIGHT-THEMED GLOBAL MODAL & TOAST --}}
    {{-- ============================================================ --}}
    <div id="cmsGlobalModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="CMS.modal.hide()"></div>
        <div id="cmsModalPanel" class="relative w-full max-w-[420px] bg-slate-900 border border-white/5 rounded-[2rem] shadow-2xl p-8 transform transition-all duration-300 scale-95 opacity-0">
            <!-- Icon -->
            <div class="mx-auto w-16 h-16 rounded-full bg-slate-950 border border-white/5 shadow-inner flex items-center justify-center mb-6 relative z-10">
                <div id="cmsModalIconWrap" class="w-10 h-10 rounded-full flex items-center justify-center">
                    <i id="cmsModalIcon" class="text-2xl"></i>
                </div>
            </div>
            
            <!-- Text -->
            <h3 id="cmsModalTitle" class="text-xl font-bold text-white text-center mb-3">Konfirmasi</h3>
            <p id="cmsModalMessage" class="text-[13px] text-slate-400 text-center leading-relaxed mb-8 mx-auto max-w-sm"></p>
            
            <!-- Buttons -->
            <div class="flex items-center gap-4">
                <button onclick="CMS.modal.hide()" class="flex-1 py-3.5 px-4 rounded-xl border border-white/10 text-slate-300 font-bold hover:bg-white/5 transition-colors text-sm">Batal</button>
                <button id="cmsModalConfirmBtn" onclick="CMS.modal.proceed()" class="flex-1 py-3.5 px-4 rounded-xl text-white font-bold transition-all duration-300 text-sm shadow-lg hover:-translate-y-0.5">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <div id="cmsToastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-4 pointer-events-none w-full max-w-[340px]"></div>

    <script>
    window.CMS = (() => {
        let currentCallback = null;

        const modal = {
            show(message, callback, options = {}) {
                currentCallback = callback;
                const m = document.getElementById('cmsGlobalModal');
                const panel = document.getElementById('cmsModalPanel');
                const title = document.getElementById('cmsModalTitle');
                const msg = document.getElementById('cmsModalMessage');
                const btn = document.getElementById('cmsModalConfirmBtn');
                const iconWrap = document.getElementById('cmsModalIconWrap');
                const icon = document.getElementById('cmsModalIcon');

                msg.innerHTML = message;
                title.innerText = options.title || 'Konfirmasi';
                btn.innerText = options.okText || 'Ya, Hapus';

                const type = options.type || 'danger';
                if(type === 'danger') {
                    iconWrap.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-rose-500/20 text-rose-500';
                    icon.className = 'ri-error-warning-line text-2xl';
                    btn.className = 'flex-1 py-3.5 px-4 rounded-xl text-white font-bold transition-all duration-300 text-sm shadow-[0_8px_20px_rgba(244,63,94,0.3)] bg-rose-500 hover:bg-rose-600 hover:-translate-y-0.5';
                } else if(type === 'warning') {
                    iconWrap.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-amber-500/20 text-amber-500';
                    icon.className = 'ri-alert-line text-2xl';
                    btn.className = 'flex-1 py-3.5 px-4 rounded-xl text-white font-bold transition-all duration-300 text-sm shadow-[0_8px_20px_rgba(245,158,11,0.3)] bg-amber-500 hover:bg-amber-600 hover:-translate-y-0.5';
                } else {
                    iconWrap.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-emerald-500/20 text-emerald-500';
                    icon.className = 'ri-checkbox-circle-line text-2xl';
                    btn.className = 'flex-1 py-3.5 px-4 rounded-xl text-white font-bold transition-all duration-300 text-sm shadow-[0_8px_20px_rgba(16,185,129,0.3)] bg-emerald-500 hover:bg-emerald-600 hover:-translate-y-0.5';
                }

                m.style.removeProperty('display');
                m.style.display = 'flex';
                setTimeout(() => {
                    panel.classList.remove('scale-95', 'opacity-0');
                    panel.classList.add('scale-100', 'opacity-100');
                }, 10);
            },
            hide() {
                const m = document.getElementById('cmsGlobalModal');
                const panel = document.getElementById('cmsModalPanel');
                panel.classList.remove('scale-100', 'opacity-100');
                panel.classList.add('scale-95', 'opacity-0');
                setTimeout(() => { m.style.display = 'none'; }, 300);
            },
            proceed() {
                if(currentCallback) currentCallback();
                this.hide();
            }
        };

        const toast = {
            show(title, message, type = 'success', duration = 4000) {
                const container = document.getElementById('cmsToastContainer');
                const id = 'toast_' + Date.now();
                
                const cfg = {
                    success: { bg: 'bg-emerald-500', text: 'text-emerald-500', light: 'bg-emerald-500/20', icon: 'ri-checkbox-circle-line' },
                    error: { bg: 'bg-rose-500', text: 'text-rose-500', light: 'bg-rose-500/20', icon: 'ri-error-warning-line' },
                    warning: { bg: 'bg-amber-500', text: 'text-amber-500', light: 'bg-amber-500/20', icon: 'ri-alert-line' },
                    info: { bg: 'bg-blue-500', text: 'text-blue-500', light: 'bg-blue-500/20', icon: 'ri-information-line' }
                }[type] || { bg: 'bg-slate-500', text: 'text-slate-500', light: 'bg-slate-500/20', icon: 'ri-information-line' };

                const el = document.createElement('div');
                el.id = id;
                el.className = `bg-slate-900 border border-white/5 rounded-[1rem] shadow-lg p-4 pr-10 flex items-start gap-3 relative overflow-hidden pointer-events-auto transform transition-all duration-500 translate-x-full opacity-0`;
                el.innerHTML = `
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 ${cfg.bg}"></div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center ${cfg.text} flex-shrink-0 mt-0.5">
                        <i class="${cfg.icon} text-[26px]"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-[14px] font-extrabold text-white mb-0.5 leading-tight">${title}</h4>
                        <p class="text-[13px] text-slate-400 leading-snug">${message}</p>
                    </div>
                    <button onclick="CMS.toast.dismiss('${id}')" class="absolute top-4 right-3 text-slate-500 hover:text-slate-300 outline-none w-6 h-6 flex items-center justify-center">
                        <i class="ri-close-line text-lg"></i>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 h-1 ${cfg.light}">
                        <div class="h-full ${cfg.bg} transition-all origin-left" style="width:100%; transition-duration: ${duration}ms; transition-timing-function: linear;"></div>
                    </div>
                `;
                container.appendChild(el);

                setTimeout(() => {
                    el.classList.remove('translate-x-full', 'opacity-0');
                    const prog = el.querySelector('div.absolute.bottom-0 > div');
                    if(prog) { setTimeout(() => { prog.style.width = '0%'; }, 50); }
                }, 10);

                setTimeout(() => this.dismiss(id), duration);
            },
            dismiss(id) {
                const el = document.getElementById(id);
                if(el){
                    el.classList.add('translate-x-full', 'opacity-0');
                    setTimeout(() => el.remove(), 500);
                }
            }
        };

        function confirmAction(formId, itemName, titleLabel="Hapus Data?") {
            const msg = `Apakah Anda yakin ingin menghapus <b>&quot;${itemName}&quot;</b>?<br>Tindakan ini tidak dapat dibatalkan.`;
            modal.show(msg, () => document.getElementById(formId).submit(), { title: titleLabel, type: 'danger', okText: 'Ya, Hapus' });
        }

        return { modal, toast, confirmAction };
    })();

    @if(session('success')) document.addEventListener('DOMContentLoaded', () => CMS.toast.show('Success', @json(session('success')), 'success')); @endif
    @if(session('error')) document.addEventListener('DOMContentLoaded', () => CMS.toast.show('Error', @json(session('error')), 'error')); @endif
    @if(session('warning')) document.addEventListener('DOMContentLoaded', () => CMS.toast.show('Warning', @json(session('warning')), 'warning')); @endif
    </script>
    
    @stack('scripts')
</body>
</html>
