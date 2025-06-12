<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use App\Traits\PhotoFileStorage;




class UserController extends Controller
{
    use AuthorizesRequests;
    use PhotoFileStorage;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);


        $usersQuery = User::withTrashed();

        $filterByName = $request->name;
        $filterByGender = $request->input('gender');
        $filterByType = $request->input('type');

        if ($filterByName !== null) {
            $usersQuery->where('name', 'LIKE', $filterByName . '%')
                ->orWhere('email', 'LIKE', $filterByName . '%');
        }

        if (!empty($filterByGender)) {
            $usersQuery->where('gender', $filterByGender);
        }

        if (!empty($filterByType)) {
            $usersQuery->where('type', $filterByType);
        }

        $allUsers = $usersQuery
            ->orderByRaw('CASE WHEN photo IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->orderBy('type')
            ->orderBy('gender')
            ->paginate(20)
            ->withQueryString();

        $listGenders = [
            'F' => 'Feminino',
            'M' => 'Masculino',
            'O' => 'Outro',
        ];

        $listTypes = [
            'board' => 'Board',
            'member' => 'Member',
            'employee' => 'Employee',
        ];

        return view('users.index', compact(
            'allUsers',
            'filterByName',
            'filterByGender',
            'filterByType',
            'listGenders',
            'listTypes',
        ));
    }


    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $newUser = new User(['type' => 'employee']);

        $fields = [
            'name',
            'email',
            'type',
            'gender',
            'password',
            'default_delivery_address',
            'nif',
            'default_payment_type',
            //'payment_details',
            'photo'
        ];

        $editableFields = [];

        foreach ($fields as $field) {
            if (auth()->user()->can('updateField', [$newUser, $field])) {
                $editableFields[] = $field;
            }
        }

        return view('users.create', [
            'user' => $newUser,
            'editableFields' => $editableFields,
        ]);
    }


    public function store(UserFormRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        $data['type'] = 'employee'; // board só pode criar employee
        $data['blocked'] = 0;   // employee nunca começa bloqueado

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = new User($data);
        $user->save();

        if ($request->hasFile('photo')) {
            $filename = $this->storePhoto($request->file('photo'), $user, 'photo', 'users');
            $user->photo = $filename;
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }






    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $fields = [
            'name',
            'email',
            'type',
            'gender',
            'password',
            'default_delivery_address',
            'nif',
            'default_payment_type',
            //'payment_details',
            'photo'
        ];

        $editableFields = [];

        foreach ($fields as $field) {
            if (auth()->user()->can('updateField', [$user, $field])) {
                $editableFields[] = $field;
            }
        }

        return view('users.edit', [
            'user' => $user,
            'editableFields' => $editableFields,
        ]);
    }


    public function update(UserFormRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {

            $this->deletePhoto($user, 'photo', 'users');


            $filename = $this->storePhoto($request->file('photo'), $user, 'photo', 'users');

            $data['photo'] = $filename;
        }

        $user->update($data);
        

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }






    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            $user->delete();

            return redirect()->back()
                ->with('alert-type', 'warning')
                ->with('alert-msg', "User <strong>{$user->name}</strong> membership canceled successfully.");
        } catch (\Exception $error) {
            $message = $error->getMessage();

            if (str_contains($message, 'Integrity constraint violation: 1451')) {
                $alertMsg = "Cannot delete the user <strong>{$user->name}</strong> because they are associated with one or more supply orders.";
            } else {
                $alertMsg = "It was not possible to delete the user <a><u>{$user->name}</u></a> ({$user->email}) due to an unexpected error.";
            }

            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', $alertMsg);
        }

    }
    public function toggleBlocked(User $user)
    {
        $this->authorize('update', $user);

        if ($user->type === 'member') {
            $user->blocked = !$user->blocked;
            $user->save();
        }

        return redirect()->back()->with('success', 'Blocked status updated.');
    }

    public function forceDestroy(User $user): RedirectResponse
    {
        $this->authorize('forceDelete', $user);

        $this->deletePhoto($user, 'photo', 'users');

        $user->forceDelete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted permanently.');
    }


    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $user);
        
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored successfully!');
    }




}
