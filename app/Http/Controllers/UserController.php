<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        $filterByName = $request->get('name');
        $filterByGender = $request->get('gender', '');
        $filterByType = $request->get('type', '');

        if (!empty($filterByName)) {
            $usersQuery->where(function($query) use ($filterByName) {
                $query->where('name', 'LIKE', "%{$filterByName}%")
                    ->orWhere('email', 'LIKE', "%{$filterByName}%");
            });
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
            'F' => 'Female',
            'M' => 'Male',
            'O' => 'Other',
        ];

        $listTypes = [
            'board' => 'Board',
            'member' => 'Member',
            'employee' => 'Employee',
        ];

        return view('users.index', [
            'allUsers' => $allUsers,
            'filterByName' => $filterByName,
            'filterByGender' => $filterByGender,
            'filterByType' => $filterByType,
            'listGenders' => $listGenders,
            'listTypes' => $listTypes,
        ]);
    }


    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $fields = [
            'name',
            'email',
            'type',
            'gender',
            'password',
            'default_delivery_address',
            'nif',
            'default_payment_type',
            'photo'
        ];

        $editableFields = [];
        foreach ($fields as $field) {
            if (auth()->user()->can('updateField', [$user, $field])) {
                $editableFields[] = $field;
            }
        }

        return view('users.show', [
            'user' => $user,
            'editableFields' => $editableFields
        ]);
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


    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);
        
        // Depuração - vamos registrar os dados do formulário
        \Log::info('Form data submitted:', $request->all());
        
        // Executar a validação e verificar se falhou
        $formRequest = new UserFormRequest();
        $rules = $formRequest->rules();
        $validator = \Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            \Log::info('Validation errors:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('alert-type', 'danger')
                ->with('alert-msg', 'Validation failed: ' . implode(', ', $validator->errors()->all()));
        }
        
        // Se chegou aqui, a validação foi bem-sucedida
        $data = $validator->validated();
        
        // Processando os dados validados
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
        
        \Log::info('User updated successfully', ['user_id' => $user->id]);
        
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

            // Log the exception
            Log::error('Error deleting user: ' . $message, [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'exception' => $error
            ]);

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

    public function forceDestroy($id): RedirectResponse
    {
        // Find the user with or without soft delete
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('forceDelete', $user);

        // Check if it's an employee
        if ($user->type !== 'employee') {
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "Only employee accounts can be permanently deleted.");
        }

        try {
            // Use database transaction to ensure all operations are atomic
            \DB::beginTransaction();
            
            // 1. Delete StockAdjustments related to this user
            \DB::table('stock_adjustments')
                ->where('registered_by_user_id', $user->id)
                ->delete();
            
            // 2. Delete SupplyOrders related to this user
            \DB::table('supply_orders')
                ->where('registered_by_user_id', $user->id)
                ->delete();
            
            // 3. Delete Operations related to this user through the card relation
            \DB::table('operations')
                ->where('card_id', $user->id)
                ->delete();
            
            // 4. Delete the user's card (if exists)
            \DB::table('cards')
                ->where('id', $user->id)
                ->delete();
            
            // 5. Delete any orders related to this user
            \DB::table('orders')
                ->where('member_id', $user->id)
                ->delete();
                
            // 6. Finally, delete the photo if it exists
            if ($user->photo) {
                // Use deletePhotoFile which takes a string path instead of a model
                $this->deletePhotoFile($user->photo, 'users');
            }
            
            // 7. Permanently delete the user
            $user->forceDelete();
            
            \DB::commit();
            
            return redirect()->route('users.index')
                ->with('alert-type', 'danger')
                ->with('alert-msg', "User <strong>{$user->name}</strong> was permanently deleted from the system.");
        } catch (\Exception $error) {
            \DB::rollBack();
            
            // Log the exception
            Log::error('Error permanently deleting user', [
                'user_id' => $id,
                'user_name' => $user->name,
                'error_message' => $error->getMessage(),
                'error_trace' => $error->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('alert-type', 'danger')
                ->with('alert-msg', "It was not possible to permanently delete the user <strong>{$user->name}</strong> due to an unexpected error: {$error->getMessage()}");
        }
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $user);
        
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored successfully!');
    }
}
