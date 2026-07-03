<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    private const PROJECT_TYPES = [
        'architecture' => 'System Architecture',
        'fullstack' => 'Full-Stack Development',
        'consultation' => 'Technical Consultation',
        'audit' => 'Code & Security Audit',
        'other' => 'Other / Custom',
    ];

    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'project_type' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            Mail::to(config('services.contact.email'))->send(new ContactFormSubmitted(
                name: $validated['name'],
                email: $validated['email'],
                projectType: self::PROJECT_TYPES[$validated['project_type']] ?? $validated['project_type'],
                message: $validated['message'],
            ));
        } catch (\Throwable) {
            return back()
                ->withInput($request->only(['name', 'email', 'project_type', 'message']))
                ->withErrors(['message' => 'We could not send your message right now. Please try again later or email us directly.']);
        }

        return back()
            ->with('success', 'Payload received and validated. Our architect will review the system metrics and respond within 24 hours.')
            ->withInput($request->only(['name', 'email', 'project_type']));
    }
}
