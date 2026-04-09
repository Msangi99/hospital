@php($sidebarTitle = __('roleui.admin_portal'))

@component('layouts.role-dashboard', ['title' => __('roleui.admin_console_title'), 'sidebarTitle' => $sidebarTitle])
    @slot('sidebar')
        @include('role.admin._sidebar', ['active' => 'console'])
    @endslot

    <div class="mx-auto max-w-4xl space-y-8">
        <div class="rounded-[2.5rem] border border-amber-200 bg-amber-50 p-6 shadow-xl sm:p-8">
            <h1 class="mb-2 text-2xl font-black tracking-tighter text-amber-950 sm:text-3xl">{{ __('roleui.admin_console_title') }}</h1>
            <p class="text-sm font-bold text-amber-900/90">{{ __('roleui.admin_console_warning') }}</p>
        </div>

        @if (session('console_output') !== null)
            <div
                class="rounded-[2.5rem] border p-6 shadow-xl sm:p-8 {{ session('console_error') ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }}"
            >
                <h2 class="mb-3 text-[10px] font-black uppercase tracking-widest {{ session('console_error') ? 'text-red-700' : 'text-green-700' }}">
                    {{ __('roleui.admin_console_output') }}
                </h2>
                @if (session('console_error'))
                    <p class="mb-3 text-sm font-bold text-red-800">{{ __('roleui.admin_console_failed') }}</p>
                @endif
                <pre class="max-h-[28rem] overflow-auto whitespace-pre-wrap break-words rounded-2xl border border-black/5 bg-white/80 p-4 text-xs font-mono text-slate-800">{{ session('console_output') }}</pre>
            </div>
        @endif

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
            <h2 class="mb-6 text-lg font-black tracking-tighter text-slate-900">{{ __('roleui.admin_console_migrate_heading') }}</h2>

            <div class="space-y-8">
                <div>
                    <p class="mb-3 text-sm font-bold text-slate-500">{{ __('roleui.admin_console_migrate_pending_help') }}</p>
                    <form method="POST" action="{{ route('admin.console.migrate') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-2xl bg-slate-900 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-slate-800"
                        >
                            {{ __('roleui.admin_console_migrate_pending') }}
                        </button>
                    </form>
                </div>

                <div class="border-t border-slate-100 pt-8">
                    <p class="mb-3 text-sm font-bold text-slate-600">{{ __('roleui.admin_console_migrate_one') }}</p>
                    @if (count($migrationPaths) === 0)
                        <p class="text-sm font-bold text-slate-400">{{ __('roleui.admin_console_no_migrations') }}</p>
                    @else
                        <form method="POST" action="{{ route('admin.console.migrate-path') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                            @csrf
                            <div class="min-w-0 flex-1">
                                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_console_select_migration') }}</label>
                                <select
                                    name="path"
                                    required
                                    class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-bold outline-none transition focus:border-blue-500"
                                >
                                    @foreach ($migrationPaths as $rel)
                                        <option value="{{ $rel }}" @selected(old('path') === $rel)>{{ $rel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="shrink-0 rounded-2xl border border-slate-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-500 hover:text-blue-600"
                            >
                                {{ __('roleui.admin_console_run_migrate') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
            <h2 class="mb-6 text-lg font-black tracking-tighter text-slate-900">{{ __('roleui.admin_console_seed_heading') }}</h2>

            <div class="space-y-8">
                <div>
                    <p class="mb-3 text-sm font-bold text-slate-500">{{ __('roleui.admin_console_seed_all_help') }}</p>
                    <form method="POST" action="{{ route('admin.console.seed') }}">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-2xl bg-blue-600 px-6 py-3 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-blue-700"
                        >
                            {{ __('roleui.admin_console_seed_all') }}
                        </button>
                    </form>
                </div>

                <div class="border-t border-slate-100 pt-8">
                    @if (count($seederClasses) === 0)
                        <p class="text-sm font-bold text-slate-400">{{ __('roleui.admin_console_no_seeders') }}</p>
                    @else
                        <form method="POST" action="{{ route('admin.console.seed-class') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                            @csrf
                            <div class="min-w-0 flex-1">
                                <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_console_select_seeder') }}</label>
                                <select
                                    name="class"
                                    required
                                    class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-bold outline-none transition focus:border-blue-500"
                                >
                                    @foreach ($seederClasses as $fqcn)
                                        <option value="{{ $fqcn }}" @selected(old('class') === $fqcn)>{{ $fqcn }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="shrink-0 rounded-2xl border border-slate-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-500 hover:text-blue-600"
                            >
                                {{ __('roleui.admin_console_run_seed') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-[2.5rem] border border-slate-100 bg-white p-6 shadow-xl sm:p-10">
            <h2 class="mb-6 text-lg font-black tracking-tighter text-slate-900">{{ __('roleui.admin_console_maintenance_heading') }}</h2>
            <form method="POST" action="{{ route('admin.console.tool') }}" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                @csrf
                <div class="min-w-0 flex-1">
                    <label class="mb-2 block px-2 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('roleui.admin_console_tool_label') }}</label>
                    <select
                        name="tool"
                        required
                        class="w-full rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm font-bold outline-none transition focus:border-blue-500"
                    >
                        <option value="migrate_status">{{ __('roleui.admin_console_tool_migrate_status') }}</option>
                        <option value="optimize_clear">{{ __('roleui.admin_console_tool_optimize_clear') }}</option>
                        <option value="cache_clear">{{ __('roleui.admin_console_tool_cache_clear') }}</option>
                        <option value="config_clear">{{ __('roleui.admin_console_tool_config_clear') }}</option>
                        <option value="route_clear">{{ __('roleui.admin_console_tool_route_clear') }}</option>
                        <option value="view_clear">{{ __('roleui.admin_console_tool_view_clear') }}</option>
                        <option value="about">{{ __('roleui.admin_console_tool_about') }}</option>
                    </select>
                </div>
                <button
                    type="submit"
                    class="shrink-0 rounded-2xl border border-slate-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-800 transition hover:border-blue-500 hover:text-blue-600"
                >
                    {{ __('roleui.admin_console_run_tool') }}
                </button>
            </form>
        </div>
    </div>
@endcomponent
