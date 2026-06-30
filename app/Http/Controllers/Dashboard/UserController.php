<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('dashboard.users.index', [
            'users' => User::latest()->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        User::create($this->validatedData($request));

        return redirect()
            ->route('dashboard.users.index')
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('dashboard.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user->update($this->validatedData($request, $user));

        return redirect()
            ->route('dashboard.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['user' => 'You cannot delete your own account while logged in.']);
        }

        $user->delete();

        return redirect()
            ->route('dashboard.users.index')
            ->with('status', 'User deleted successfully.');
    }

    /**
     * @return array{name: string, email: string, password?: string}
     */
    private function validatedData(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                new \App\Rules\StrictEmail(),
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user && blank($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
