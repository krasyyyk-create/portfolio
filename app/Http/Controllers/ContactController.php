<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'project_type' => 'required|string',
            'message' => 'required|string',
        ]);

        return back()
            ->with('success', 'Payload received and validated. Our architect will review the system metrics and respond within 24 hours.')
            ->withInput($request->only(['name', 'email', 'project_type']));
    }
}
