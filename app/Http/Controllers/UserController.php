<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(): View
    {
        $allUsers = User::paginate(20);
        return view('users.index')->with('allUsers', $allUsers);
    }

    public function showCase(): View
    {
        // No need to pass the variable $users to the view, because it is available through View::share
        // Check AppServiceProvider
        return view('users.showcase');
    }

    public function show(User $user): View
    {
        return view('users.show')->with('user', $user);
    }

    public function create(): View
    {
        $newUser = new User();
        return view('users.create')->with('user', $newUser);
    }

    public function store(userFormRequest $request): RedirectResponse
    {
        $newuser = user::create($request->validated());
        $url = route('users.show', ['user' => $newuser]);
        $htmlMessage = "user <a href='$url'><strong>{$newuser->abbreviation}</strong> 
                    - '{$newuser->name}'</a> has been created successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function edit(User $user): View
    {
        return view('users.edit')->with('user', $user);
    }

    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        $url = route('users.show', ['user' => $user]);
        $htmlMessage = "user <a href='$url'><strong>{$user->name}</strong> -
                    '{$user->name}'</a> has been updated successfully!";
        return redirect()->route('users.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
        $alertType = 'warning';
        $alertMsg = "";
         
        } catch (\Exception $error) {
            $alertType = 'danger';
            $alertMsg = "It was not possible to delete the user
                            <a><u>{$user->name}</u></a> ({$user->email})
                            because there was an error with the operation!";
        }
        return redirect()->back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}
