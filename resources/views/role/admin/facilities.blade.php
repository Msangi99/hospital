@php($sidebarTitle = __('roleui.admin_portal'))
@php($detailLabels = [
    'name' => __('roleui.facility_name'),
    'type' => __('roleui.facility_type'),
    'status' => __('roleui.facility_status'),
    'verification' => __('roleui.facility_verification_status'),
    'owner' => __('roleui.facility_owner'),
    'owner_email' => __('roleui.facility_owner_email'),
    'location' => __('roleui.facility_location'),
    'address' => __('roleui.facility_address'),
    'city' => __('roleui.facility_city'),
    'country' => __('roleui.facility_country'),
    'postcode' => __('roleui.facility_postcode'),
    'latitude' => __('roleui.facility_latitude'),
    'longitude' => __('roleui.facility_longitude'),
    'phone' => __('roleui.facility_phone'),
    'email' => __('roleui.facility_email'),
    'website' => __('roleui.facility_website'),
    'license' => __('roleui.facility_license'),
    'emergency' => __('roleui.facility_emergency_services'),
    'note' => __('roleui.facility_note'),
    'description' => __('roleui.facility_description'),
    'kyc_submitted' => __('roleui.facility_kyc_submitted'),
    'empty' => __('roleui.facility_details_empty'),
])

@component('layouts.role-dashboard', ['title' => __('roleui.admin_facilities_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'facilities'])
    @endslot

    <div class="space-y-8">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <x-admin.hero
            :kicker="__('roleui.admin_facilities_title')"
            :title="__('roleui.sidebar_facility_management')"
            :description="__('roleui.admin_facilities_desc')"
        >
            <x-slot:pills>
                <span class="rounded-full border border-blue-200/30 bg-blue-400/10 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-blue-100">{{ __('roleui.facilities_pending_verification') }}: {{ $stats['pending_verification'] }}</span>
                <span class="rounded-full border border-emerald-200/30 bg-emerald-400/10 px-3 py-1 text-[10px] font-black uppercase tracking-wider text-emerald-100">{{ __('roleui.facilities_approved') }}: {{ $stats['approved'] }}</span>
            </x-slot:pills>
            <x-slot:actions>
                <button type="button" id="toggle-add-facility" class="rounded-2xl border border-white/70 bg-white px-4 py-2.5 text-[10px] font-black uppercase tracking-widest text-slate-900 transition hover:-translate-y-0.5 hover:bg-slate-100">
                    {{ __('roleui.facilities_add_button') }}
                </button>
            </x-slot:actions>
        </x-admin.hero>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">{{ __('roleui.facilities_add_new') }}</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-600">Admin only</span>
            </div>

            <form method="POST" action="{{ route('admin.facilities.store') }}" id="add-facility-form" class="hidden grid gap-4 lg:grid-cols-12">
                @csrf
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_location') }}</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_type') }}</label>
                    <select name="type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                        @foreach($types as $type)
                            <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_status') }}</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'Online') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_latitude') }}</label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>
                <div class="lg:col-span-4">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.facility_longitude') }}</label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                </div>

                <div class="lg:col-span-12 rounded-2xl border border-slate-200 bg-white p-4">
                    <label class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-600">
                        <input id="create-facility-account" type="checkbox" name="create_account" value="1" @checked(old('create_account'))>
                        <span>{{ __('roleui.facilities_create_account') }}</span>
                    </label>
                    <div id="facility-account-fields" class="mt-3 grid gap-3 md:grid-cols-3 {{ old('create_account') ? '' : 'hidden' }}">
                        <input type="text" name="account_name" value="{{ old('account_name') }}" placeholder="{{ __('roleui.facility_account_name') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                        <input type="email" name="account_email" value="{{ old('account_email') }}" placeholder="{{ __('roleui.facility_account_email') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                        <input type="password" name="account_password" placeholder="{{ __('roleui.facility_account_password') }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none transition focus:border-blue-500">
                    </div>
                </div>

                <div class="lg:col-span-12">
                    <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-slate-800">
                        {{ __('roleui.facilities_create_button') }}
                    </button>
                </div>
            </form>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card :label="__('roleui.facilities_total')" :value="$stats['total']" tone="neutral" />
            <x-admin.stat-card :label="__('roleui.facilities_pending_verification')" :value="$stats['pending_verification']" tone="amber" />
            <x-admin.stat-card :label="__('roleui.facilities_approved')" :value="$stats['approved']" tone="emerald" />
            <x-admin.stat-card :label="__('roleui.facilities_suspended')" :value="$stats['suspended']" tone="rose" />
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50/80 px-6 py-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <label class="w-full max-w-xl">
                        <span class="sr-only">{{ __('roleui.facilities_filter_placeholder') }}</span>
                        <input id="facility-search" type="text" placeholder="{{ __('roleui.facilities_filter_placeholder') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none transition focus:border-blue-400">
                    </label>
                    <div class="relative">
                        <button type="button" id="column-toggle" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-black text-slate-700 transition hover:bg-slate-100">
                            {{ __('roleui.facilities_columns') }}
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                        <div id="column-menu" class="absolute right-0 z-20 mt-2 hidden w-56 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="0" checked> {{ __('roleui.facility_name') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="1" checked> {{ __('roleui.facility_location') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="2" checked> {{ __('roleui.facility_owner') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="3" checked> {{ __('roleui.facility_type') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="4" checked> {{ __('roleui.facility_status') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="5" checked> {{ __('roleui.facility_verification_status') }}</label>
                            <label class="mb-2 flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="6" checked> {{ __('roleui.facility_actions') }}</label>
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" data-col="7" checked> {{ __('roleui.users_table_registered') }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="admin-facilities-table" class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-900 text-white">
                        <tr class="text-left text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_name') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_location') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_owner') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_type') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_status') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_verification_status') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.facility_actions') }}</th>
                            <th class="px-6 py-4 text-slate-300">{{ __('roleui.users_table_registered') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($facilities as $facility)
                            <tr id="hospital-{{ $facility->id }}" class="transition hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-black text-slate-900">{{ $facility->name }}</td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">{{ $facility->location }}</td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                    @if($facility->owner)
                                        <div>{{ $facility->owner->name }}</div>
                                        <div class="text-[10px] font-medium text-slate-500">{{ $facility->owner->email }}</div>
                                    @else
                                        {{ __('roleui.facility_owner_unassigned') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">{{ $facility->type }}</td>
                                <td class="px-6 py-4 text-xs font-black">
                                    <span class="rounded-full px-3 py-1 {{ $facility->status === 'Online' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $facility->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-black">
                                    @php($vs = (string) ($facility->verification_status ?? 'PENDING'))
                                            <span class="rounded-full px-3 py-1 {{ $vs === 'APPROVED' ? 'bg-emerald-50 text-emerald-700' : ($vs === 'SUSPENDED' ? 'bg-amber-50 text-amber-700' : ($vs === 'REJECTED' ? 'bg-rose-50 text-rose-700' : 'bg-slate-200 text-slate-700')) }}">
                                        {{ $vs }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        type="button"
                                        class="mb-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-700 transition hover:border-blue-400 hover:text-blue-700"
                                        data-view-more
                                        data-name="{{ $facility->name }}"
                                        data-location="{{ $facility->location }}"
                                        data-address="{{ $facility->address_line }}"
                                        data-city="{{ $facility->city }}"
                                        data-country="{{ $facility->country }}"
                                        data-postal="{{ $facility->postal_code }}"
                                        data-type="{{ $facility->type }}"
                                        data-status="{{ $facility->status }}"
                                        data-verification="{{ $facility->verification_status }}"
                                        data-kyc-submitted="{{ $facility->kyc_submitted_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '' }}"
                                        data-owner="{{ $facility->owner?->name }}"
                                        data-owner-email="{{ $facility->owner?->email }}"
                                        data-phone="{{ $facility->contact_phone }}"
                                        data-email="{{ $facility->contact_email }}"
                                        data-website="{{ $facility->website }}"
                                        data-license="{{ $facility->license_number }}"
                                        data-emergency="{{ $facility->has_emergency_services ? 'Yes' : 'No' }}"
                                        data-lat="{{ $facility->latitude }}"
                                        data-lng="{{ $facility->longitude }}"
                                        data-note="{{ $facility->verification_note }}"
                                        data-description="{{ $facility->description }}"
                                    >
                                        {{ __('roleui.facility_view_more') }}
                                    </button>
                                    <form method="POST" action="{{ route('admin.facilities.moderate', $facility) }}" class="space-y-2">
                                        @csrf
                                        <input type="text" name="note" placeholder="{{ __('roleui.facility_note_placeholder') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11px] font-semibold text-slate-700">
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" name="action" value="approve" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">{{ __('roleui.facility_action_approve') }}</button>
                                            <button type="submit" name="action" value="reject" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-rose-700">{{ __('roleui.facility_action_reject') }}</button>
                                            <button type="submit" name="action" value="suspend" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-amber-700">{{ __('roleui.facility_action_suspend') }}</button>
                                            <button type="submit" name="action" value="reactivate" class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-blue-700">{{ __('roleui.facility_action_reactivate') }}</button>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-xs font-black text-slate-500">{{ optional($facility->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sm font-bold text-slate-500">{{ __('roleui.facilities_empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="facility-details-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-900/60 p-4">
        <div class="w-full max-w-6xl rounded-3xl border border-slate-200 bg-white text-slate-700 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900">{{ __('roleui.facility_details_title') }}</h3>
                <button type="button" id="facility-details-close" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-black text-slate-700">{{ __('roleui.close_menu') }}</button>
            </div>
            <div id="facility-details-content" class="grid max-h-[78vh] gap-3 overflow-y-auto p-6 text-sm sm:grid-cols-2 xl:grid-cols-3"></div>
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('add-facility-form');
            var toggle = document.getElementById('toggle-add-facility');
            var createAccount = document.getElementById('create-facility-account');
            var accountFields = document.getElementById('facility-account-fields');

            if (form && toggle) {
                toggle.addEventListener('click', function () {
                    form.classList.remove('hidden');
                    toggle.classList.add('hidden');
                });

                @if ($errors->any())
                    form.classList.remove('hidden');
                    toggle.classList.add('hidden');
                @endif
            }

            if (createAccount && accountFields) {
                createAccount.addEventListener('change', function () {
                    accountFields.classList.toggle('hidden', !createAccount.checked);
                });
            }
        })();

        (function () {
            var table = document.getElementById('admin-facilities-table');
            if (!table) return;

            var tbody = table.querySelector('tbody');
            var allRows = Array.from(tbody.querySelectorAll('tr')).filter(function (tr) {
                return tr.querySelectorAll('td').length > 1;
            });
            var searchInput = document.getElementById('facility-search');
            var columnToggle = document.getElementById('column-toggle');
            var columnMenu = document.getElementById('column-menu');
            var columnChecks = columnMenu ? Array.from(columnMenu.querySelectorAll('input[type="checkbox"][data-col]')) : [];

            function applySearch() {
                var q = (searchInput && searchInput.value ? searchInput.value : '').toLowerCase().trim();
                allRows.forEach(function (tr) {
                    var text = tr.innerText.toLowerCase();
                    tr.classList.toggle('hidden', q !== '' && text.indexOf(q) === -1);
                });
            }

            function applyColumnVisibility() {
                var headers = Array.from(table.querySelectorAll('thead th'));
                columnChecks.forEach(function (input) {
                    var idx = parseInt(input.getAttribute('data-col') || '-1', 10);
                    if (idx < 0) return;
                    if (headers[idx]) headers[idx].style.display = input.checked ? '' : 'none';
                    allRows.forEach(function (tr) {
                        var cells = tr.querySelectorAll('td');
                        if (cells[idx]) cells[idx].style.display = input.checked ? '' : 'none';
                    });
                });
            }

            searchInput && searchInput.addEventListener('input', applySearch);

            columnToggle && columnToggle.addEventListener('click', function () {
                if (!columnMenu) return;
                columnMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function (e) {
                if (!columnMenu || !columnToggle) return;
                if (!columnMenu.contains(e.target) && !columnToggle.contains(e.target)) {
                    columnMenu.classList.add('hidden');
                }
            });

            columnChecks.forEach(function (input) {
                input.addEventListener('change', applyColumnVisibility);
            });

            var modal = document.getElementById('facility-details-modal');
            var content = document.getElementById('facility-details-content');
            var closeBtn = document.getElementById('facility-details-close');

            var labels = @json($detailLabels);

            function row(label, value) {
                return '<div class="rounded-xl border border-slate-200 bg-white px-3 py-2.5"><div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">' + label + '</div><div class="mt-1 break-words font-semibold text-slate-800">' + (value || labels.empty) + '</div></div>';
            }

            document.querySelectorAll('[data-view-more]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var d = btn.dataset;
                    content.innerHTML =
                        row(labels.name, d.name) +
                        row(labels.type, d.type) +
                        row(labels.status, d.status) +
                        row(labels.verification, d.verification) +
                        row(labels.kyc_submitted, d.kycSubmitted || labels.empty) +
                        row(labels.owner, d.owner) +
                        row(labels.owner_email, d.ownerEmail) +
                        row(labels.location, d.location) +
                        row(labels.address, d.address) +
                        row(labels.city, d.city) +
                        row(labels.country, d.country) +
                        row(labels.postcode, d.postal) +
                        row(labels.latitude, d.lat) +
                        row(labels.longitude, d.lng) +
                        row(labels.phone, d.phone) +
                        row(labels.email, d.email) +
                        row(labels.website, d.website) +
                        row(labels.license, d.license) +
                        row(labels.emergency, d.emergency) +
                        row(labels.note, d.note) +
                        row(labels.description, d.description);

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            closeBtn && closeBtn.addEventListener('click', closeModal);
            modal && modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });

            applySearch();
            applyColumnVisibility();
        })();
    </script>
@endcomponent

