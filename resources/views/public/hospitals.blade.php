<x-layouts.public>
    <style>
        :root { --blublu-primary: #2563eb; --blublu-dark: #0f172a; }
        .hospital-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .hospital-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px -12px rgba(37, 99, 235, 0.15); border-color: #2563eb; }
        .search-gradient { background: radial-gradient(circle at top right, #f8fafc, #eff6ff); }
    </style>

    <section class="search-gradient py-20 px-6 border-b border-slate-100">
        <div class="max-w-4xl mx-auto text-center">
            <span class="bg-blue-100 text-blue-700 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.3em] mb-6 inline-block">{{ __('hospitals.badge') }}</span>
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 tracking-tight leading-none">{!! __('hospitals.title_html') !!}</h1>
            <p class="text-lg text-slate-600 font-medium mb-10 opacity-80 italic">{{ __('hospitals.subtitle') }}</p>

            <div class="relative max-w-2xl mx-auto">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                <input type="text" id="searchInput" onkeyup="filterHospitals()" placeholder="{{ __('hospitals.search_placeholder') }}" class="w-full p-6 pl-16 rounded-[2.5rem] shadow-2xl border-none outline-none focus:ring-4 ring-blue-100 font-bold text-slate-700">
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto py-20 px-6">
        <div id="hospitalGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach ($hospitals as $h)
                <div class="hospital-card bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-sm transition-all cursor-pointer group">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                            <i class="fas fa-h-square"></i>
                        </div>
                        <span class="text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest {{ $h->status === 'Online' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                            <i class="fas fa-circle mr-1 text-[7px] {{ $h->status === 'Online' ? 'animate-pulse' : '' }}"></i> {{ $h->status }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-3 leading-tight tracking-tighter">{{ $h->name }}</h2>
                    <p class="text-sm font-bold text-slate-400 mb-8 italic"><i class="fas fa-map-marker-alt mr-2 text-blue-500"></i> {{ $h->location }}</p>
                    <div class="flex items-center justify-between pt-8 border-t border-slate-50">
                        <span class="text-[10px] font-black uppercase text-blue-600 tracking-widest">{{ $h->type }}</span>
                        <button class="text-slate-900 font-black text-[10px] uppercase hover:text-blue-600 tracking-widest transition">{{ __('hospitals.book_cta') }} &rarr;</button>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <script>
        function filterHospitals() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let cards = document.getElementsByClassName('hospital-card');
            for (let i = 0; i < cards.length; i++) {
                let name = cards[i].getElementsByTagName('h2')[0].innerText.toLowerCase();
                let loc = cards[i].getElementsByTagName('p')[0].innerText.toLowerCase();
                let type = cards[i].getElementsByTagName('span')[1].innerText.toLowerCase();
                if (name.includes(input) || loc.includes(input) || type.includes(input)) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>
</x-layouts.public>

