<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index() {
        return view('components.contact-form');
    }    

    public function submit(Request $request) {
       $validated = $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|max:255',
           'project_type' => 'required|string',
           'message' => 'required|string',
       ]);
       
       // Handle message transmission...
       return back()->with('success', 'REQUEST_SENT: Payload validated and logged.');
   }
}
