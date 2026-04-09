@php($hospitalGeoStorageKey = 'semanami_hospitals_geo_v1')

@push('head-scripts')
    @if (request()->boolean('nogeo'))
        <script>
            try { sessionStorage.removeItem(@json($hospitalGeoStorageKey)); } catch (e) {}
        </script>
    @endif
    @if ($autoGeoEnabled ?? false)
        <script>
            (function () {
                var base = @json(route('hospitals'));
                var key = @json($hospitalGeoStorageKey);
                var maxAgeMs = 86400000 * 7;
                try {
                    var params = new URLSearchParams(window.location.search);
                    if (params.get('nogeo') === '1') return;
                    if (params.get('lat') && params.get('lng')) return;
                    var raw = sessionStorage.getItem(key);
                    if (! raw) return;
                    var d = JSON.parse(raw);
                    if (! d || d.lat == null || d.lng == null) return;
                    if (Date.now() - d.t > maxAgeMs) {
                        sessionStorage.removeItem(key);
                        return;
                    }
                    var u = new URL(base, window.location.origin);
                    u.searchParams.set('lat', String(d.lat));
                    u.searchParams.set('lng', String(d.lng));
                    window.location.replace(u.toString());
                } catch (e) {}
            })();
        </script>
    @endif
    @if ($userLat !== null && $userLng !== null)
        <script>
            (function () {
                try {
                    sessionStorage.setItem(
                        @json($hospitalGeoStorageKey),
                        JSON.stringify({
                            lat: @json($userLat),
                            lng: @json($userLng),
                            t: Date.now(),
                        })
                    );
                } catch (e) {}
            })();
        </script>
    @endif
@endpush

<x-layouts.public>
    <style>
        :root { --blublu-primary: #2563eb; --blublu-dark: #0f172a; }
        .hospital-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .hospital-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px -12px rgba(37, 99, 235, 0.15); border-color: #2563eb; }
    </style>

    <section class="border-b border-slate-100 bg-slate-50/90 py-10 px-4 sm:py-12 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <h1 class="mb-6 text-center text-3xl font-black leading-tight tracking-tight text-slate-900 sm:text-4xl">
                {{ __('home.nav_hospitals') }}
            </h1>

            @if ($autoGeoEnabled ?? false)
                <p id="hospital-locating-msg" class="mb-6 text-center text-sm font-bold text-slate-500">
                    {{ __('hospitals.locating') }}
                </p>
            @endif

            <div class="relative mx-auto max-w-2xl">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-lg text-slate-400 sm:left-6 sm:text-xl"></i>
                <input
                    type="text"
                    id="searchInput"
                    onkeyup="filterHospitals()"
                    placeholder="{{ __('hospitals.search_placeholder') }}"
                    class="w-full rounded-2xl border border-slate-200 bg-white p-4 pl-14 text-sm font-bold text-slate-700 shadow-sm outline-none ring-blue-100 focus:border-blue-200 focus:ring-2 sm:rounded-[2rem] sm:p-5 sm:pl-16 sm:text-base"
                >
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-16">
        <div id="hospitalGrid" class="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-10 lg:grid-cols-3 lg:gap-10">
            @foreach ($hospitalCards as $h)
                @php($fromOsm = ! empty($h->from_osm))
                @php($searchBlob = mb_strtolower($h->name.' '.$h->location.' '.$h->type))
                <div
                    class="hospital-card group cursor-pointer rounded-[3.5rem] border border-slate-100 bg-white p-8 shadow-sm transition-all sm:p-10 {{ $fromOsm ? 'ring-1 ring-slate-200/80' : '' }}"
                    data-search="{{ e($searchBlob) }}"
                >
                    <div class="mb-8 flex items-start justify-between">
                        <div class="flex h-16 w-16 items-center justify-center rounded-3xl text-2xl transition-all duration-500 {{ $fromOsm ? 'bg-slate-100 text-slate-700 group-hover:bg-slate-800 group-hover:text-white' : 'bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white' }}">
                            <i class="fas {{ $fromOsm ? 'fa-map-location-dot' : 'fa-h-square' }}"></i>
                        </div>
                        @if ($fromOsm)
                            <span class="rounded-full bg-slate-100 px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-slate-600">
                                {{ __('hospitals.source_osm') }}
                            </span>
                        @else
                            <span class="rounded-full px-4 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $h->status === 'Online' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                <i class="fas fa-circle mr-1 text-[7px] {{ $h->status === 'Online' ? 'animate-pulse' : '' }}"></i> {{ $h->status }}
                            </span>
                        @endif
                    </div>
                    <h2 class="hospital-card__name mb-3 text-2xl font-black leading-tight tracking-tighter text-slate-900">{{ $h->name }}</h2>
                    <p class="hospital-card__location mb-2 text-sm font-bold italic text-slate-400">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i> {{ $h->location }}
                    </p>
                    @if ($userLat !== null && $userLng !== null && $h->distance_km !== null)
                        <p class="mb-6 text-xs font-black uppercase tracking-widest text-blue-600">
                            {{ __('hospitals.distance_km', ['distance' => number_format((float) $h->distance_km, 1)]) }}
                        </p>
                    @else
                        <div class="mb-6"></div>
                    @endif
                    <div class="flex items-center justify-between border-t border-slate-50 pt-8">
                        <span class="hospital-card__type text-[10px] font-black uppercase tracking-widest text-blue-600">{{ $h->type }}</span>
                        @if ($fromOsm && isset($h->latitude, $h->longitude))
                            <a
                                href="https://www.google.com/maps?q={{ rawurlencode((string) $h->latitude.','.(string) $h->longitude) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-900 transition hover:text-blue-600"
                            >
                                {{ __('hospitals.open_google_maps') }} &rarr;
                            </a>
                        @else
                            <button type="button" class="text-[10px] font-black uppercase tracking-widest text-slate-900 transition hover:text-blue-600">{{ __('hospitals.book_cta') }} &rarr;</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if (($overpassResultCount ?? 0) > 0)
            <p class="mt-12 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">
                {{ __('hospitals.osm_attribution') }}
            </p>
        @endif
    </div>

    @if ($autoGeoEnabled ?? false)
        <script>
            (function () {
                var base = @json(route('hospitals'));
                var key = @json($hospitalGeoStorageKey);
                function goNoGeo() {
                    try {
                        sessionStorage.removeItem(key);
                    } catch (e) {}
                    var u = new URL(base, window.location.origin);
                    u.searchParams.set('nogeo', '1');
                    window.location.replace(u.toString());
                }
                try {
                    var params = new URLSearchParams(window.location.search);
                    if (params.get('lat') && params.get('lng')) return;
                } catch (e) {}
                if (! navigator.geolocation) {
                    goNoGeo();
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var u = new URL(base, window.location.origin);
                        u.searchParams.set('lat', String(pos.coords.latitude));
                        u.searchParams.set('lng', String(pos.coords.longitude));
                        window.location.replace(u.toString());
                    },
                    function () {
                        goNoGeo();
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 120000 }
                );
            })();
        </script>
    @endif

    <script>
        function filterHospitals() {
            var input = document.getElementById('searchInput');
            if (! input) return;
            var q = input.value.toLowerCase();
            var cards = document.querySelectorAll('#hospitalGrid .hospital-card');
            for (var i = 0; i < cards.length; i++) {
                var blob = (cards[i].getAttribute('data-search') || '').toLowerCase();
                cards[i].style.display = ! q || blob.indexOf(q) !== -1 ? '' : 'none';
            }
        }
    </script>
</x-layouts.public>
