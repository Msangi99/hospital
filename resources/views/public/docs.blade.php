<x-layouts.public>
    <style>
        :root { --blublu-primary: #2563eb; }
        .code-block { background: #1e293b; color: #e2e8f0; font-family: 'Courier New', Courier, monospace; }
        .sidebar-link:hover { background: rgba(37, 99, 235, 0.1); color: var(--blublu-primary); }
        .endpoint-get { background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .endpoint-post { background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        section { scroll-margin-top: 100px; }
    </style>

    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row">
        <aside class="w-full lg:w-64 p-8 border-r border-slate-200 min-h-screen sticky top-16 hidden lg:block bg-white/50">
            <div class="space-y-8">
                <div>
                    <h2 class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-4">{{ __('docs.side_intro') }}</h2>
                    <ul class="space-y-3 text-sm font-bold text-slate-600">
                        <li><a href="#welcome" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_welcome') }}</a></li>
                        <li><a href="#auth" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_auth') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-4">{{ __('docs.side_endpoints') }}</h2>
                    <ul class="space-y-3 text-sm font-bold text-slate-600">
                        <li><a href="#telemedicine" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_tele') }}</a></li>
                        <li><a href="#emergency" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_emerge') }}</a></li>
                        <li><a href="#triage" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_triage') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-4">{{ __('docs.side_interop') }}</h2>
                    <ul class="space-y-3 text-sm font-bold text-slate-600">
                        <li><a href="#emr" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_emr') }}</a></li>
                        <li><a href="#errors" class="sidebar-link block py-1 px-2 rounded transition">{{ __('docs.side_errors') }}</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-8 lg:p-16 max-w-4xl">
            <section id="welcome" class="mb-20">
                <h1 class="text-4xl font-black mb-6">{{ __('docs.doc_title') }}</h1>
                <p class="text-slate-600 leading-relaxed mb-6">
                    {{ __('docs.doc_desc') }}
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-xl text-blue-800 text-sm italic">
                    <i class="fas fa-info-circle mr-2"></i> {!! __('docs.doc_note_html') !!}
                </div>
            </section>

            <section id="auth" class="mb-20">
                <h2 class="text-2xl font-black mb-4">{{ __('docs.auth_title') }}</h2>
                <p class="text-sm text-slate-600 mb-6">
                    {!! __('docs.auth_desc_html') !!}
                </p>
                <div class="code-block p-6 rounded-2xl text-xs overflow-x-auto shadow-lg">
<pre>{{ __('docs.auth_curl') }}</pre>
                </div>
            </section>

            <section id="telemedicine" class="mb-20">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="endpoint-post">POST</span>
                    <h2 class="text-2xl font-black">{{ __('docs.tele_title') }}</h2>
                </div>
                <p class="text-sm text-slate-600 mb-6">{{ __('docs.tele_desc') }}</p>
                <div class="code-block p-6 rounded-2xl text-xs shadow-lg mb-4">
<pre>{{ __('docs.tele_payload') }}</pre>
                </div>
            </section>

            <section id="emergency" class="mb-20">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="endpoint-post">POST</span>
                    <h2 class="text-2xl font-black">{{ __('docs.emerge_title') }}</h2>
                </div>
                <p class="text-sm text-slate-600 mb-6">{{ __('docs.emerge_desc') }}</p>
                <div class="code-block p-6 rounded-2xl text-xs shadow-lg">
<pre>{{ __('docs.emerge_payload') }}</pre>
                </div>
            </section>

            <footer class="mt-32 pt-12 border-t border-slate-200 text-center">
                <p class="text-slate-400 text-sm mb-4">{{ __('docs.footer_help') }}</p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:devs@semanamimi.com" class="text-blue-600 font-bold hover:underline">devs@semanamimi.com</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('contact') }}" class="text-blue-600 font-bold hover:underline">{{ __('docs.footer_partnership_help') }}</a>
                </div>
                <p class="mt-8 text-[10px] uppercase tracking-widest text-slate-300 font-bold">
                    {{ __('docs.footer_small') }}
                </p>
            </footer>
        </main>
    </div>
</x-layouts.public>

