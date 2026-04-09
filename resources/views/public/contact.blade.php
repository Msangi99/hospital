<x-layouts.public>
    <div class="min-h-screen pt-16 pb-24 px-6" style="background: radial-gradient(circle at top right, #f0f9ff, #ffffff);">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div class="space-y-10">
                <div>
                    <span class="bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-[0.3em] mb-6 inline-block">
                        {{ __('public.contact_badge') }}
                    </span>
                    <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1] mb-6 tracking-tighter italic">
                        {!! __('public.contact_title_html') !!}
                    </h1>
                    <p class="text-xl text-slate-600 font-medium">
                        {{ __('public.contact_subtitle') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-blue-50 border border-blue-50">
                        <i class="fas fa-phone-alt text-blue-600 text-2xl mb-4"></i>
                        <h2 class="font-black uppercase text-xs tracking-widest mb-2">{{ __('public.contact_phone_label') }}</h2>
                        <p class="text-slate-600 font-bold">{{ __('public.contact_phone_value') }}</p>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-blue-50 border border-blue-50">
                        <i class="fas fa-envelope text-blue-600 text-2xl mb-4"></i>
                        <h2 class="font-black uppercase text-xs tracking-widest mb-2">{{ __('public.contact_email_label') }}</h2>
                        <p class="text-slate-600 font-bold">{{ __('public.contact_email_value') }}</p>
                    </div>
                </div>

                <div class="w-full h-64 rounded-[3rem] bg-slate-200 overflow-hidden shadow-2xl relative">
                    <iframe
                        class="w-full h-full grayscale"
                        src="{{ __('public.contact_map_iframe_src') }}"
                        allowfullscreen
                        loading="lazy"
                    ></iframe>
                </div>
            </div>

            <div class="bg-white rounded-[3.5rem] shadow-2xl border border-slate-100 p-10 lg:p-16">
                @if (session('status'))
                    <div class="mb-8 p-6 rounded-3xl bg-green-50 text-green-700 border border-green-100 font-bold text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-8 p-6 rounded-3xl bg-red-50 text-red-700 border border-red-100 font-bold text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block px-4">{{ __('public.contact_form_name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe" class="w-full bg-slate-50 border border-slate-100 p-5 rounded-3xl outline-none focus:border-blue-500 transition shadow-inner">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block px-4">{{ __('public.contact_form_email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@email.com" class="w-full bg-slate-50 border border-slate-100 p-5 rounded-3xl outline-none focus:border-blue-500 transition shadow-inner">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block px-4">{{ __('public.contact_form_subject') }}</label>
                        <select name="subject" class="w-full bg-slate-50 border border-slate-100 p-5 rounded-3xl outline-none focus:border-blue-500 transition shadow-inner appearance-none">
                            <option value="general">{{ __('public.contact_subject_general') }}</option>
                            <option value="account">{{ __('public.contact_subject_account') }}</option>
                            <option value="bug">{{ __('public.contact_subject_bug') }}</option>
                            <option value="feedback">{{ __('public.contact_subject_feedback') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block px-4">{{ __('public.contact_form_message') }}</label>
                        <textarea name="message" required rows="5" placeholder="{{ __('public.contact_message_placeholder') }}" class="w-full bg-slate-50 border border-slate-100 p-5 rounded-3xl outline-none focus:border-blue-500 transition shadow-inner resize-none">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white p-6 rounded-[2rem] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-xl transform hover:scale-[1.02] active:scale-95">
                        {{ __('public.contact_submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.public>

