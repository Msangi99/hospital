<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Hospital;
use App\Models\NewsletterSubscriber;
use App\Models\SafeGirlSymptom;
use App\Models\SosRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPagesController extends Controller
{
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

    public function hospitals(): View
    {
        $hospitals = Hospital::query()->orderBy('name')->get();

        return view('public.hospitals', [
            'hospitals' => $hospitals,
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
}

