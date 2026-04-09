<x-layouts.public>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background: #f8fafc; overflow-x: hidden; }
        #map-container {
            height: 450px;
            width: 100%;
            border-radius: 2.5rem;
            overflow: hidden;
            position: relative;
            border: 8px solid white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            background: #e2e8f0;
        }
        #map { height: 100%; width: 100%; z-index: 1; }
        .btn-active {
            background-color: #dc2626 !important;
            color: white !important;
            cursor: pointer !important;
            animation: pulse-red 2s infinite;
            opacity: 1 !important;
        }
        @keyframes pulse-red {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
            70% { transform: scale(1.02); box-shadow: 0 0 0 15px rgba(220, 38, 38, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
        }
    </style>

    <main class="max-w-6xl mx-auto px-6 py-12 grid lg:grid-cols-2 gap-12 items-center">
        <div class="z-10">
            <h1 class="text-6xl font-black italic tracking-tighter mb-6 uppercase">{!! __('ambulance.title_html') !!}</h1>

            @if (session('status'))
                <div class="mb-6 p-6 rounded-3xl bg-green-50 text-green-700 border border-green-100 font-bold text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl mb-6 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">{{ __('ambulance.satellite_status') }}</p>
                <h2 id="loc-text" class="text-xl font-bold text-slate-500 italic">{{ __('ambulance.searching_signal') }}</h2>
                <div class="mt-3 flex flex-wrap gap-3 text-xs font-black uppercase tracking-widest text-slate-500">
                    <span class="bg-slate-50 border border-slate-100 rounded-full px-3 py-2">
                        {{ __('ambulance.lat') }}: <span id="lat-text" class="text-slate-900">—</span>
                    </span>
                    <span class="bg-slate-50 border border-slate-100 rounded-full px-3 py-2">
                        {{ __('ambulance.lng') }}: <span id="lng-text" class="text-slate-900">—</span>
                    </span>
                </div>
                <p id="acc-text" class="mt-2 text-[11px] font-bold text-slate-400 italic"></p>
                <p id="addr-text" class="mt-2 text-[11px] font-bold text-slate-400 italic"></p>
            </div>

            <form action="{{ route('ambulance.sos') }}" method="POST" id="sos-form">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">
                <input type="hidden" name="address" id="address_field">

                <button id="sos-btn" type="submit" disabled
                    class="w-full bg-slate-200 text-slate-400 py-8 rounded-[2.5rem] font-black text-2xl uppercase shadow-xl transition-all duration-300 opacity-50 cursor-not-allowed">
                    {{ __('ambulance.wait_gprs') }}
                </button>
            </form>

            <p class="mt-4 text-xs font-bold text-slate-400 italic">
                {{ __('ambulance.manual_hint') }}
            </p>
        </div>

        <div id="map-container">
            <div id="map"></div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map, marker, circle;
        let lastFix = null;
        let reverseSeq = 0;
        let manualMode = false;

        function setUiFromCoords(latitude, longitude, accuracyMeters = null) {
            document.getElementById('lat').value = latitude;
            document.getElementById('lng').value = longitude;
            document.getElementById('lat-text').innerText = Number(latitude).toFixed(6);
            document.getElementById('lng-text').innerText = Number(longitude).toFixed(6);

            // Always keep a usable fallback "address" for backend storage.
            if (!document.getElementById('address_field').value) {
                document.getElementById('address_field').value = `${latitude},${longitude}`;
            }

            const accText = document.getElementById('acc-text');
            if (accuracyMeters !== null && Number.isFinite(accuracyMeters)) {
                accText.innerText = @json(__('ambulance.accuracy_label')).replace(':meters', Math.round(accuracyMeters).toString());
            }

            const btn = document.getElementById('sos-btn');
            btn.disabled = false;
            btn.innerText = @json(__('ambulance.call_now'));
            btn.classList.add('btn-active');
            btn.classList.remove('opacity-50', 'cursor-not-allowed');

            // Reverse geocode is optional; SOS uses lat/lng as truth.
            reverseGeocode(latitude, longitude);
        }

        function reverseGeocode(latitude, longitude) {
            const mySeq = ++reverseSeq;
            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`;
            fetch(url, { cache: 'no-store' })
                .then(r => r.json())
                .then(data => {
                    if (mySeq !== reverseSeq) return;
                    const street = (data.display_name || '').split(',').slice(0, 3).join(', ');
                    const locText = document.getElementById('loc-text');
                    // Keep coordinate-based title stable; show address separately.
                    locText.innerText = @json(__('ambulance.coords_ready'));
                    document.getElementById('address_field').value = data.display_name || '';
                    document.getElementById('addr-text').innerText = (street ? (@json(__('ambulance.address_label')) + ' ' + street) : '');
                    locText.classList.add('text-blue-600');
                });
        }

        function startTracking() {
            const locText = document.getElementById('loc-text');
            const accText = document.getElementById('acc-text');
            if (!navigator.geolocation) {
                locText.innerText = @json(__('ambulance.no_gps'));
                return;
            }

            locText.innerText = @json(__('ambulance.requesting_gps'));
            accText.innerText = '';
            document.getElementById('addr-text').innerText = '';

            // First try: force a fresh fix (reduces “stale”/cached results)
            navigator.geolocation.getCurrentPosition(success, fail, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });

            // Then keep tracking updates
            navigator.geolocation.watchPosition(success, fail, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        }

        function success(pos) {
            const { latitude, longitude, accuracy } = pos.coords;
            lastFix = { latitude, longitude, accuracy };

            if (!manualMode) {
                setUiFromCoords(latitude, longitude, accuracy);
            }

            if (!map) {
                map = L.map('map', { zoomControl: false, fadeAnimation: false }).setView([latitude, longitude], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);
                circle = L.circle([latitude, longitude], { radius: accuracy, color: 'red', fillOpacity: 0.1 }).addTo(map);
                setTimeout(() => { map.invalidateSize(); }, 400);

                map.on('click', (e) => {
                    manualMode = true;
                    const { lat, lng } = e.latlng;
                    marker.setLatLng([lat, lng]);
                    circle.setLatLng([lat, lng]).setRadius(0);
                    map.setView([lat, lng], Math.max(map.getZoom(), 16));
                    document.getElementById('loc-text').innerText = @json(__('ambulance.manual_selected'));
                    setUiFromCoords(lat, lng, null);
                });

                marker.on('dragend', () => {
                    manualMode = true;
                    const ll = marker.getLatLng();
                    circle.setLatLng(ll).setRadius(0);
                    document.getElementById('loc-text').innerText = @json(__('ambulance.manual_selected'));
                    setUiFromCoords(ll.lat, ll.lng, null);
                });
            } else {
                if (!manualMode) {
                    map.setView([latitude, longitude]);
                    marker.setLatLng([latitude, longitude]);
                    circle.setLatLng([latitude, longitude]).setRadius(accuracy);
                }
            }
            if (!manualMode && accuracy && accuracy > 800) {
                document.getElementById('acc-text').innerText =
                    @json(__('ambulance.low_accuracy_warning')).replace(':meters', Math.round(accuracy).toString());
            }
        }

        function fail(err) {
            const locText = document.getElementById('loc-text');
            if (err && err.code === 1) {
                locText.innerText = @json(__('ambulance.allow_gprs'));
                return;
            }
            if (err && err.code === 2) {
                locText.innerText = @json(__('ambulance.position_unavailable'));
                return;
            }
            if (err && err.code === 3) {
                locText.innerText = @json(__('ambulance.timeout'));
                return;
            }

            locText.innerText = @json(__('ambulance.searching_signal'));
        }

        document.addEventListener('DOMContentLoaded', startTracking);
    </script>
</x-layouts.public>

