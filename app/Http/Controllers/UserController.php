<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use AuthorizesRequests;
   public function index(Request $request): View
    {
        // Verifica se o utilizador autenticado tem permissão
        $this->authorize('viewAny', User::class);

        // Para incluir deletados:
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
            ->orderBy('email')
            ->orderBy('type')
            ->orderBy('name')
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

    public function showCase(): View
    {
        return view('users.showcase');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function create(): View
    {
        $newUser = new User();
        return view('users.create')->with('user', $newUser);
    }


 
    public function store(UserFormRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $data['deleted_at'] = null;

    
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }



   public function edit(User $user): View
    {
        $this->authorize('update', $user);
        return view('users.edit')->with('user', $user);
    }


  public function update(UserFormRequest $request, User $user): RedirectResponse
{
    $authUser = auth()->user();

    // Impede o board ou member de editar a si próprio
    if (($authUser->type === 'board' || $authUser->type === 'member') && $authUser->id === $user->id) {
        return redirect()->back()->withErrors(['error' => 'You cannot edit your own profile.']);
    }

    $data = $request->validated();

    // Se for board ou member, só pode alterar o 'type'
    if (in_array($authUser->type, ['board', 'member'])) {
        $data = array_intersect_key($data, ['type' => true]);

        if (!isset($data['type'])) {
            return redirect()->back()->withErrors(['error' => 'No type provided for update.']);
        }

        // Regras adicionais apenas para board
        if ($authUser->type === 'board') {
            if ($user->type === 'member' && $data['type'] !== 'board') {
                return redirect()->back()->withErrors(['error' => 'Board members can only promote members to board.']);
            }

            if ($user->type === 'board' && !in_array($data['type'], ['employee', 'member'])) {
                return redirect()->back()->withErrors(['error' => 'Board members can only demote board users to member or employee.']);
            }
        } else {
            // Member não pode fazer alterações a type de ninguém
            return redirect()->back()->withErrors(['error' => 'Members are not allowed to update other users.']);
        }
    } else {
        // Se não for board nem member (por exemplo, admin), pode alterar tudo
        // Hash da password, se fornecida
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
    }

    $user->update($data);

    $url = route('users.show', ['user' => $user]);
    $htmlMessage = "User <a href='$url'><strong>{$user->name}</strong></a> has been updated successfully!";

    return redirect()->route('users.index')
        ->with('alert-type', 'success')
        ->with('alert-msg', $htmlMessage);
}



    public function destroy(User $user): RedirectResponse
    {
        if (auth()->user()->id === $user->id) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }

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
        if ($user->type === 'member') {
            $user->blocked = !$user->blocked;
            $user->save();
        }

        return redirect()->back()->with('success', 'Blocked status updated.');
    }

    public function forceDestroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        $url = route('users.index', ['user' => $user]);

        $htmlMessage = "user <a href='$url'><strong>{$user->name}</strong></a> deleted successfully!";

        return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', $htmlMessage);
    }


    public function restore(User $user)
    {
        $user = User::withTrashed()->findOrFail($user->id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored successfully!');
    }




}
