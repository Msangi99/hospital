<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            :root { --sn-primary: #1e293b; --sn-accent: #2563eb; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased" style="font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;">
        <div class="min-h-screen flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-[2.5rem] shadow-2xl p-10 border-t-8 border-blue-600">
                    <div class="text-center mb-8">
                        <a href="{{ route('home') }}" class="inline-block">
                            <div class="text-3xl font-black text-slate-900 tracking-tighter italic">
                                {{ __('home.brand_a') }}<span class="text-blue-600">{{ __('home.brand_b') }}</span>
                            </div>
                        </a>
                        <div class="mt-4 flex justify-center">
                            <form method="POST" action="{{ route('locale.set') }}">
                                @csrf
                                @php($loc = $currentLocale ?? app()->getLocale())
                                <select name="locale" onchange="this.form.submit()" class="border border-slate-200 rounded-full px-3 py-2 text-[12px] font-extrabold text-slate-900 bg-white">
                                    <option value="sw" @selected($loc === 'sw')>SW</option>
                                    <option value="en" @selected($loc === 'en')>EN</option>
                                    <option value="fr" @selected($loc === 'fr')>FR</option>
                                    <option value="ar" @selected($loc === 'ar')>AR</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>

