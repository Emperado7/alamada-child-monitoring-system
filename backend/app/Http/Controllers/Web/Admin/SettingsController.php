<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // ── Password change (Security tab) ───────────────────────────────
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->with('tab', 'security');
            }

            if (!$request->filled('new_password')) {
                return back()->withErrors(['new_password' => 'Please enter a new password.'])
                    ->with('tab', 'security');
            }

            $user->update(['password' => Hash::make($request->new_password)]);
            return back()->with('success', 'Password updated successfully.')->with('tab', 'security');
        }

        // ── Profile update (Account tab) ─────────────────────────────────
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'phone'));
        return back()->with('success', 'Settings updated successfully.');
    }
}
