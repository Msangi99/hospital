@php($sidebarTitle = __('roleui.ambulance_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.ambulance_run_title').' #'.$sos->id, 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.ambulance._sidebar', ['active' => 'dashboard'])
    @endslot

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ambulance_run_status') }}</p>
                <h1 class="text-2xl font-black tracking-tighter">{{ $sos->status }}</h1>
            </div>
            <a href="{{ route('ambulance.portal.dashboard') }}" class="text-xs font-black uppercase tracking-widest text-blue-600 hover:text-slate-900">{{ __('roleui.back_to_dashboard') }}</a>
        </div>

        <dl class="mb-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ambulance_run_phone') }}</dt>
                <dd class="font-bold text-slate-900">{{ $sos->phone }}</dd>
            </div>
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ambulance_run_coords') }}</dt>
                <dd class="font-bold text-slate-900">{{ $sos->latitude }}, {{ $sos->longitude }}</dd>
            </div>
            <div class="sm:col-span-2 rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ambulance_run_address') }}</dt>
                <dd class="font-bold text-slate-900">{{ $sos->address ?? '—' }}</dd>
            </div>
        </dl>

        @php($destQuery = rawurlencode((string) $sos->latitude.','.(string) $sos->longitude))
        <div class="mb-8 rounded-[2rem] border border-orange-100 bg-orange-50/40 p-4 sm:p-6">
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <h2 class="mb-2 text-xs font-black uppercase tracking-[0.2em] text-orange-700">{{ __('roleui.ambulance_run_map_heading') }}</h2>
            <p class="mb-4 text-xs font-bold text-slate-600">{{ __('roleui.ambulance_run_map_hint') }}</p>
            <div id="ambulance-run-map" class="mb-4 h-[min(26rem,52vh)] w-full overflow-hidden rounded-2xl border-4 border-white shadow-xl"></div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $destQuery }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white hover:bg-orange-600"
                >
                    {{ __('roleui.ambulance_run_directions_google') }}
                </a>
                <a
                    href="https://maps.apple.com/?daddr={{ (float) $sos->latitude }},{{ (float) $sos->longitude }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex rounded-xl border border-slate-200 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-800 hover:border-orange-200"
                >
                    {{ __('roleui.ambulance_run_directions_apple') }}
                </a>
            </div>
        </div>

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dest = [{{ (float) $sos->latitude }}, {{ (float) $sos->longitude }}];
                const mapEl = document.getElementById('ambulance-run-map');
                if (!mapEl || typeof L === 'undefined') {
                    return;
                }

                const map = L.map(mapEl, { zoomControl: true }).setView(dest, 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

                const sceneLabel = @json(__('roleui.ambulance_run_scene_marker'));
                const sceneDetails = @json(\Illuminate\Support\Str::limit(strip_tags((string) ($sos->address ?? '')), 220, '…').' · #'.(string) $sos->id);
                L.marker(dest).addTo(map).bindPopup('<strong>' + sceneLabel + '</strong><br>' + sceneDetails);

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            const crew = [pos.coords.latitude, pos.coords.longitude];
                            const youLabel = @json(__('roleui.ambulance_run_you_marker'));
                            L.marker(crew, { title: youLabel }).addTo(map);
                            L.polyline([crew, dest], { color: '#ea580c', weight: 4, opacity: 0.85, dashArray: '8 10' }).addTo(map);
                            map.fitBounds(L.latLngBounds(crew, dest), { padding: [36, 36], maxZoom: 16 });
                        },
                        function () {
                            map.setView(dest, 15);
                        },
                        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 },
                    );
                }

                setTimeout(function () {
                    map.invalidateSize();
                }, 250);
                window.addEventListener('resize', function () {
                    map.invalidateSize();
                });
            });
        </script>

        @if (! $sos->isTerminal())
            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('ambulance.portal.advance', $sos) }}">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-orange-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-slate-900">
                        {{ __('roleui.ambulance_advance_button') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('ambulance.portal.cancel', $sos) }}" onsubmit="return confirm(@json(__('roleui.ambulance_cancel_confirm')));">
                    @csrf
                    <button type="submit" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-black uppercase tracking-widest text-red-700 hover:bg-red-100">
                        {{ __('roleui.ambulance_cancel_run') }}
                    </button>
                </form>
            </div>
        @else
            <p class="font-bold text-slate-500">{{ __('roleui.ambulance_run_closed') }}</p>
        @endif
    </div>
@endcomponent
