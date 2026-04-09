<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Hospital;
use App\Models\NewsletterSubscriber;
use App\Models\SafeGirlSymptom;
use App\Models\SosRequest;
use App\Services\OverpassInterpreterClient;
use App\Services\SafeGirlAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicPagesController extends Controller
{
    public function __construct(
        private readonly OverpassInterpreterClient $overpass,
        private readonly SafeGirlAiService $safeGirlAi,
    ) {}

    public function home(Request $request): View
    {
        [$userLat, $userLng] = $this->parseUserGeoQuery($request);

        $networkHospitals = Hospital::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->limit(6)
            ->get();

        if ($userLat !== null && $userLng !== null) {
            $networkHospitals = $this->attachDistanceAndSort($networkHospitals, $userLat, $userLng);
        }

        return view('home', [
            'currentLocale' => session('locale', config('app.locale')),
            'networkHospitals' => $networkHospitals,
            'networkUserLat' => $userLat,
            'networkUserLng' => $userLng,
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function services(): View
    {
        return view('public.services');
    }

    public function ambulance(): View
    {
        return view('public.ambulance');
    }

    public function ambulanceSos(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address' => ['nullable', 'string', 'max:5000'],
        ]);

        SosRequest::create([
            'user_id' => $request->user()?->id,
            'phone' => $request->user()?->phone,
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
            'address' => $data['address'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->with('status', __('public.sos_received'));
    }

    public function hospitals(Request $request): View
    {
        [$userLat, $userLng] = $this->parseUserGeoQuery($request);

        if ($request->boolean('nogeo')) {
            $userLat = null;
            $userLng = null;
        }

        $autoGeoEnabled = $userLat === null && $userLng === null && ! $request->boolean('nogeo');

        $hospitals = Hospital::query()->orderBy('name')->get();

        if ($userLat !== null && $userLng !== null) {
            $hospitals = $this->attachDistanceAndSort($hospitals, $userLat, $userLng);
        }

        $osmNearby = collect();
        if ($userLat !== null && $userLng !== null) {
            $osmNearby = $this->overpass->healthFacilitiesAround($userLat, $userLng);
        }

        $hospitalCards = $this->mergeHospitalCards($hospitals, $osmNearby, $userLat, $userLng);

        return view('public.hospitals', [
            'hospitalCards' => $hospitalCards,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'overpassResultCount' => $osmNearby->count(),
            'autoGeoEnabled' => $autoGeoEnabled,
        ]);
    }

    public function docs(): View
    {
        return view('public.docs');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }

    public function ussd(): View
    {
        return view('public.ussd');
    }

    public function ussdInfo(): View
    {
        return view('public.ussd-info');
    }

    public function safeGirl(): View
    {
        return view('public.safe-girl');
    }

    public function videoConsult(Request $request): View
    {
        $roomName = 'SemaNami-Room-'.md5((string) $request->user()->id.'-'.(string) now()->timestamp);

        return view('public.video-consult', [
            'roomName' => $roomName,
        ]);
    }

    public function safeGirlSymptomSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'symptom_message' => ['required', 'string', 'max:5000'],
        ]);

        SafeGirlSymptom::create([
            'user_id' => (int) $request->user()->id,
            'message' => $data['symptom_message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->with('status', __('public.safe_girl_symptom_received'));
    }

    public function safeGirlAiChat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:5000'],
        ]);

        $history = array_values((array) ($data['history'] ?? []));
        $history[] = [
            'role' => 'user',
            'content' => (string) $data['message'],
        ];

        SafeGirlSymptom::create([
            'user_id' => (int) $request->user()->id,
            'message' => (string) $data['message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        try {
            $result = $this->safeGirlAi->respond($history);
        } catch (\Throwable $e) {
            report($e);

            $result = [
                'assistant_message' => __('safe_girl.ai_error_reply'),
                'type' => 'question',
                'possible_condition' => null,
                'urgency' => null,
                'advice' => [],
                'red_flags' => [],
            ];
        }

        return response()->json([
            'assistant_message' => $result['assistant_message'],
            'type' => $result['type'],
            'possible_condition' => $result['possible_condition'],
            'urgency' => $result['urgency'],
            'advice' => $result['advice'],
            'red_flags' => $result['red_flags'],
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($data + [
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->with('status', __('public.contact_success', ['name' => $data['name'], 'email' => $data['email']]));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subscriber_email' => ['required', 'email', 'max:255'],
        ]);

        $email = $data['subscriber_email'];

        $existing = NewsletterSubscriber::query()->where('email', $email)->first();
        if ($existing) {
            return redirect()->route('home')->with('status', __('public.subscribe_already'));
        }

        NewsletterSubscriber::create(['email' => $email]);

        return redirect()->route('home')->with('status', __('public.subscribe_success'));
    }

    /**
     * @return array{0: ?float, 1: ?float}
     */
    private function parseUserGeoQuery(Request $request): array
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return [null, null];
        }

        $latF = (float) $lat;
        $lngF = (float) $lng;

        if ($latF < -90 || $latF > 90 || $lngF < -180 || $lngF > 180) {
            return [null, null];
        }

        return [$latF, $lngF];
    }

    /**
     * @param  Collection<int, Hospital>  $hospitals
     * @return Collection<int, Hospital>
     */
    private function attachDistanceAndSort(Collection $hospitals, float $userLat, float $userLng): Collection
    {
        return $hospitals
            ->map(function (Hospital $h) use ($userLat, $userLng) {
                if ($h->latitude !== null && $h->longitude !== null) {
                    $h->setAttribute(
                        'distance_km',
                        Hospital::haversineDistanceKm($userLat, $userLng, (float) $h->latitude, (float) $h->longitude),
                    );
                } else {
                    $h->setAttribute('distance_km', null);
                }

                return $h;
            })
            ->sortBy(fn (Hospital $h) => $h->getAttribute('distance_km') ?? PHP_FLOAT_MAX)
            ->values();
    }

    /**
     * @param  Collection<int, Hospital>  $dbHospitals
     * @param  Collection<int, object>  $osmPlaces
     * @return Collection<int, object>
     */
    private function mergeHospitalCards(Collection $dbHospitals, Collection $osmPlaces, ?float $userLat, ?float $userLng): Collection
    {
        $cards = $dbHospitals->map(fn (Hospital $h) => $this->hospitalModelToCard($h));

        foreach ($osmPlaces as $place) {
            $cards->push($place);
        }

        if ($userLat !== null && $userLng !== null) {
            return $cards->sortBy(fn (object $c) => $c->distance_km ?? PHP_FLOAT_MAX)->values();
        }

        return $cards->sortBy(fn (object $c) => mb_strtolower($c->name))->values();
    }

    private function hospitalModelToCard(Hospital $h): object
    {
        return (object) [
            'name' => $h->name,
            'location' => $h->location,
            'type' => $h->type,
            'status' => $h->status,
            'distance_km' => $h->getAttribute('distance_km'),
            'from_osm' => false,
            'osm_url' => null,
        ];
    }
}