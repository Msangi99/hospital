@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_ai_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'ai'])
    @endslot

    <div class="mx-auto max-w-3xl rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
        <h1 class="mb-2 text-2xl font-black tracking-tighter sm:text-3xl">{{ __('roleui.admin_ai_title') }}</h1>
        <p class="mb-8 font-bold text-slate-500">{{ __('roleui.admin_ai_desc') }}</p>

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 p-4 text-sm font-bold text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.ai-settings.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ai_provider') }}</label>
                <select name="provider" required class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500">
                    @foreach ($providers as $provider)
                        <option value="{{ $provider }}" @selected($setting->provider === $provider)>{{ strtoupper($provider) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ai_api_key') }}</label>
                <input
                    type="password"
                    name="api_key"
                    id="ai-api-key"
                    autocomplete="off"
                    class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500"
                    placeholder="{{ __('roleui.ai_api_key_hint') }}"
                >
                <p class="mt-2 text-xs font-bold text-slate-400">{{ __('roleui.ai_api_key_note') }}</p>
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ai_model') }}</label>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-stretch">
                    <select
                        name="model"
                        id="ai-model-select"
                        required
                        class="min-w-0 flex-1 rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500"
                    >
                        @php($currentModel = old('model', $setting->model))
                        <option value="{{ $currentModel }}">{{ $currentModel }}</option>
                    </select>
                    <button
                        type="button"
                        id="ai-load-models"
                        data-loading="0"
                        class="shrink-0 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-500 hover:text-blue-600"
                    >
                        <span class="inline-flex items-center gap-2">
                            <svg id="ai-load-models-spinner" class="hidden h-4 w-4 animate-spin text-slate-500" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"></path>
                            </svg>
                            <span id="ai-load-models-text">{{ __('roleui.ai_load_models') }}</span>
                        </span>
                    </button>
                </div>
                <p id="ai-models-status" class="mt-2 hidden text-xs font-bold" role="status"></p>
            </div>

            <div>
                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.ai_system_prompt') }}</label>
                <textarea name="system_prompt" rows="8" class="w-full rounded-2xl border border-slate-100 bg-white p-4 text-sm font-bold outline-none transition focus:border-blue-500" placeholder="{{ __('safe_girl.ai_default_system_prompt') }}">{{ old('system_prompt', $setting->system_prompt) }}</textarea>
            </div>

            <label class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white px-4 py-3">
                <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $setting->is_enabled))>
                <span class="text-sm font-black text-slate-700">{{ __('roleui.ai_enable_for_safe_girl') }}</span>
            </label>

            <button type="submit" class="w-full rounded-2xl bg-slate-900 p-5 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-600">
                {{ __('roleui.ai_save_settings') }}
            </button>
        </form>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .ai-settings-select2 + .select2-container .select2-selection--single {
            border-radius: 1rem;
            border: 1px solid rgb(241 245 249);
            min-height: 56px;
            display: flex;
            align-items: center;
            background: rgb(248 250 252);
            padding-left: .25rem;
            font-weight: 700;
            font-size: .875rem;
        }
        .ai-settings-select2 + .select2-container .select2-selection__arrow {
            height: 54px;
            right: 12px;
        }
        .ai-settings-select2 + .select2-container {
            width: 100% !important;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function () {
            var loadBtn = document.getElementById('ai-load-models');
            var select = document.getElementById('ai-model-select');
            var providerEl = document.querySelector('select[name="provider"]');
            var keyEl = document.getElementById('ai-api-key');
            var statusEl = document.getElementById('ai-models-status');
            var token = document.querySelector('input[name="_token"]');
            var spinner = document.getElementById('ai-load-models-spinner');
            var btnText = document.getElementById('ai-load-models-text');

            function setStatus(kind, text) {
                if (!statusEl) return;
                statusEl.textContent = text || '';
                statusEl.classList.remove('hidden', 'text-red-600', 'text-green-700', 'text-slate-500');
                if (!text) {
                    statusEl.classList.add('hidden');
                    return;
                }
                statusEl.classList.add(kind === 'ok' ? 'text-green-700' : kind === 'err' ? 'text-red-600' : 'text-slate-500');
            }

            var strLoading = @json(__('roleui.ai_models_loading'));
            var strFetchFailed = @json(__('roleui.ai_models_fetch_failed'));
            var strEmpty = @json(__('roleui.ai_models_empty'));
            var strLoaded = @json(__('roleui.ai_models_loaded'));
            var modelsUrl = @json(route('admin.ai-settings.models'));
            var strLoadModels = @json(__('roleui.ai_load_models'));

            if (window.jQuery && select) {
                select.classList.add('ai-settings-select2');
                window.jQuery(select).select2({
                    width: '100%',
                    placeholder: strLoadModels,
                    allowClear: false
                });
            }

            function setLoading(isLoading) {
                if (!loadBtn) return;
                loadBtn.disabled = isLoading;
                loadBtn.dataset.loading = isLoading ? '1' : '0';
                loadBtn.classList.toggle('opacity-70', isLoading);
                loadBtn.classList.toggle('cursor-wait', isLoading);
                if (spinner) spinner.classList.toggle('hidden', !isLoading);
                if (btnText) btnText.textContent = isLoading ? strLoading : strLoadModels;
            }

            loadBtn?.addEventListener('click', function () {
                if (!select || !providerEl || !token) return;
                setStatus('muted', strLoading);
                setLoading(true);

                var body = {
                    provider: providerEl.value,
                    api_key: keyEl ? keyEl.value : '',
                    use_saved_key: keyEl && keyEl.value.trim() === '' ? true : false
                };

                fetch(modelsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token.value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(body)
                })
                    .then(function (r) {
                        return r.json().then(function (data) {
                            return { ok: r.ok, status: r.status, data: data };
                        });
                    })
                    .then(function (res) {
                        if (!res.ok) {
                            var msg = (res.data && res.data.message) ? res.data.message : strFetchFailed;
                            setStatus('err', msg);
                            return;
                        }
                        var models = res.data.models || [];
                        if (!models.length) {
                            setStatus('err', strEmpty);
                            return;
                        }
                        var current = select.value;
                        select.innerHTML = '';
                        models.forEach(function (id) {
                            var opt = document.createElement('option');
                            opt.value = id;
                            opt.textContent = id;
                            if (id === current) opt.selected = true;
                            select.appendChild(opt);
                        });
                        if (!select.querySelector('option[selected]') && select.options.length) {
                            select.selectedIndex = 0;
                        }
                        if (window.jQuery) {
                            window.jQuery(select).trigger('change.select2');
                        }
                        setStatus('ok', strLoaded.replace(':count', String(models.length)));
                    })
                    .catch(function () {
                        setStatus('err', strFetchFailed);
                    })
                    .finally(function () {
                        setLoading(false);
                    });
            });
        })();
    </script>
@endcomponent