<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::query()
            ->with('userLanguages')
            ->findOrFail(Auth::id());

        return view('profile.show', [
            'user' => $user,
            'languages' => Language::query()->orderBy('name')->pluck('name', 'code'),
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'mother_tongue_code' => ['nullable', Rule::exists('languages', 'code')],
        ]);

        $user->update($validated);

        return back()->with('status', 'Perfil actualizado.');
    }

    public function destroy(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/index.html');
    }
}