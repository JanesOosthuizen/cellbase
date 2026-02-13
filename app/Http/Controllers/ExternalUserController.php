<?php

namespace App\Http\Controllers;

use App\Models\ExternalUser;
use Illuminate\Http\Request;

class ExternalUserController extends Controller
{
    /**
     * Display a listing of external users (suppliers/contacts).
     */
    public function index()
    {
        $externalUsers = ExternalUser::orderBy('company')->orderBy('surname')->orderBy('name')->paginate(15);

        return view('external-users.index', compact('externalUsers'));
    }

    /**
     * Store a newly created external user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'email_address' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        ExternalUser::create($validated);

        return redirect()->route('external-users.index')
            ->with('success', 'External user added successfully.');
    }

    /**
     * Update the specified external user in storage.
     */
    public function update(Request $request, ExternalUser $external_user)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'email_address' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $external_user->update($validated);

        return redirect()->route('external-users.index')
            ->with('success', 'External user updated successfully.');
    }

    /**
     * Remove the specified external user from storage.
     */
    public function destroy(ExternalUser $external_user)
    {
        $external_user->delete();

        return redirect()->route('external-users.index')
            ->with('success', 'External user deleted successfully.');
    }
}
