@php($sidebarTitle = __('roleui.owner_portal'))
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

@component('layouts.role-dashboard', ['title' => __('roleui.owner_profile_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.owner._sidebar', ['active' => 'profile'])
    @endslot

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @php($isPending = (string) (auth()->user()?->status ?? '') !== 'ACTIVE')
    @if ($isPending)
        <div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-800">
            {{ __('roleui.owner_verification_pending_desc') }}
        </div>
    @endif

    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.owner_profile_title') }}</h1>
        <p class="mb-6 font-bold text-slate-500">{{ __('roleui.owner_profile_desc') }}</p>

        <form method="POST" action="{{ route('owner.profile.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_hospital_name') }}</label>
                <input type="text" name="name" required value="{{ old('name', $hospital?->name) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
            </div>

            <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_hospital_location') }}</label>
                <input id="location-input" type="text" name="location" required readonly value="{{ old('location', $hospital?->location) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
            </div>

            <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_address_line') }}</label>
                <input id="address-input" type="text" name="address_line" value="{{ old('address_line', $hospital?->address_line) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_city') }}</label>
                    <input id="city-input" type="text" name="city" value="{{ old('city', $hospital?->city) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_country') }}</label>
                    <input id="country-input" type="text" name="country" value="{{ old('country', $hospital?->country) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_postal_code') }}</label>
                    <input id="postcode-input" type="text" name="postal_code" value="{{ old('postal_code', $hospital?->postal_code) }}" readonly class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_hospital_type') }}</label>
                <input type="text" name="type" required value="{{ old('type', $hospital?->type) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_contact_email') }}</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $hospital?->contact_email) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_website') }}</label>
                    <input type="url" name="website" value="{{ old('website', $hospital?->website) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm" placeholder="https://example.com">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_license_number') }}</label>
                    <input type="text" name="license_number" value="{{ old('license_number', $hospital?->license_number) }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4">
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_map_pick_title') }}</label>
                <div class="mb-3 flex gap-2">
                    <input id="osm-search" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" placeholder="{{ __('roleui.owner_map_search_placeholder') }}">
                    <button id="osm-search-btn" type="button" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-black uppercase tracking-wider text-white">{{ __('roleui.owner_map_search_button') }}</button>
                </div>
                <div id="owner-map" class="h-72 w-full rounded-xl border border-slate-200"></div>
                <p class="mt-2 text-xs font-bold text-slate-500">{{ __('roleui.owner_map_hint') }}</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-500">Latitude</label>
                        <input id="lat-input" type="text" name="latitude" value="{{ old('latitude', $hospital?->latitude) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-slate-500">Longitude</label>
                        <input id="lng-input" type="text" name="longitude" value="{{ old('longitude', $hospital?->longitude) }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-xs font-black uppercase tracking-wider text-slate-500">{{ __('roleui.owner_description') }}</label>
                <textarea name="description" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-medium text-sm">{{ old('description', $hospital?->description) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="has_emergency_services" name="has_emergency_services" value="1" @checked(old('has_emergency_services', (int) ($hospital?->has_emergency_services ?? 0))) class="h-4 w-4 rounded border-slate-300">
                <label for="has_emergency_services" class="text-sm font-bold text-slate-700">{{ __('roleui.owner_has_emergency_services') }}</label>
            </div>

            <button type="submit" class="rounded-2xl bg-blue-600 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-900">
                {{ __('roleui.owner_save_profile') }}
            </button>
        </form>
    </div>
@endcomponent
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
    (function () {
        var latInput = document.getElementById('lat-input');
        var lngInput = document.getElementById('lng-input');
        var locationInput = document.getElementById('location-input');
        var addressInput = document.getElementById('address-input');
        var cityInput = document.getElementById('city-input');
        var countryInput = document.getElementById('country-input');
        var postcodeInput = document.getElementById('postcode-input');
        var searchInput = document.getElementById('osm-search');
        var searchBtn = document.getElementById('osm-search-btn');
        var mapEl = document.getElementById('owner-map');
        if (!latInput || !lngInput || !mapEl || typeof L === 'undefined') {
            return;
        }

        var startLat = parseFloat(latInput.value || '-6.8');
        var startLng = parseFloat(lngInput.value || '39.28');
        var map = L.map(mapEl).setView([startLat, startLng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

        function syncInputs(lat, lng) {
            latInput.value = Number(lat).toFixed(7);
            lngInput.value = Number(lng).toFixed(7);
        }

        function fillAddress(data) {
            var addr = data && data.address ? data.address : {};
            if (locationInput) locationInput.value = data && data.display_name ? data.display_name : (locationInput.value || '');
            if (addressInput) {
                var road = addr.road || addr.pedestrian || addr.footway || '';
                var house = addr.house_number || '';
                addressInput.value = (house && road) ? (house + ' ' + road) : (road || house || '');
            }
            if (cityInput) cityInput.value = addr.city || addr.town || addr.village || addr.county || '';
            if (countryInput) countryInput.value = addr.country || '';
            if (postcodeInput) postcodeInput.value = addr.postcode || '';
        }

        function reverseGeocode(lat, lng) {
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) { return res.json(); })
                .then(function (row) { fillAddress(row || {}); })
                .catch(function () {});
        }

        marker.on('dragend', function (e) {
            var p = e.target.getLatLng();
            syncInputs(p.lat, p.lng);
            reverseGeocode(p.lat, p.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            syncInputs(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        function searchPlace() {
            var q = (searchInput && searchInput.value ? searchInput.value.trim() : '');
            if (!q) return;
            fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) { return res.json(); })
                .then(function (rows) {
                    if (!rows || !rows.length) return;
                    var lat = parseFloat(rows[0].lat);
                    var lon = parseFloat(rows[0].lon);
                    marker.setLatLng([lat, lon]);
                    map.setView([lat, lon], 15);
                    syncInputs(lat, lon);
                    fillAddress(rows[0]);
                })
                .catch(function () {});
        }

        searchBtn && searchBtn.addEventListener('click', searchPlace);
        searchInput && searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPlace();
            }
        });

        if (!locationInput.value && latInput.value && lngInput.value) {
            reverseGeocode(latInput.value, lngInput.value);
        }
    })();
</script>
