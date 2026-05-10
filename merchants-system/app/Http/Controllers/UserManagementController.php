<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', $this->allowedRolesForCurrentUser())
            ->orderBy('name')
            ->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->allowedRolesForCurrentUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in($allowedRoles)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_root' => false,
        ]);

        $user->syncRoles([$validated['role']]);

        ActivityLogService::log(
            action: 'user_created',
            target: $user,
            description: 'تم إنشاء مستخدم جديد',
            oldValues: null,
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $validated['role'],
                'is_root' => $user->is_root,
            ]
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        if (!$this->canManageTargetUser($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ليس لديك صلاحية تعديل هذا المستخدم');
        }

        $user->load('roles');

        $roles = Role::whereIn('name', $this->allowedRolesForCurrentUser())
            ->orderBy('name')
            ->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!$this->canManageTargetUser($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ليس لديك صلاحية تعديل هذا المستخدم');
        }

        $allowedRoles = $this->allowedRolesForCurrentUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in($allowedRoles)],
        ]);

        if (!$this->isRootUser() && $user->is_root) {
            return redirect()
                ->route('users.index')
                ->with('error', 'لا يمكن تعديل مستخدم Root');
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
            'is_root' => $user->is_root,
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);
        $user->load('roles');

        ActivityLogService::log(
            action: 'user_updated',
            target: $user,
            description: 'تم تعديل بيانات مستخدم',
            oldValues: $oldValues,
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
                'is_root' => $user->is_root,
            ]
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'تم تعديل المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if (!$this->canManageTargetUser($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ليس لديك صلاحية حذف هذا المستخدم');
        }

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'لا يمكنك حذف حسابك الحالي');
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()?->name,
            'is_root' => $user->is_root,
        ];

        ActivityLogService::log(
            action: 'user_deleted',
            target: $user,
            description: 'تم حذف مستخدم',
            oldValues: $oldValues,
            newValues: null
        );

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'تم حذف المستخدم');
    }

    public function showResetPasswordForm(User $user)
    {
        if (!$this->canManageTargetUser($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ليس لديك صلاحية إعادة تعيين كلمة مرور هذا المستخدم');
        }

        return view('users.reset-password', compact('user'));
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!$this->canManageTargetUser($user)) {
            return redirect()
                ->route('users.index')
                ->with('error', 'ليس لديك صلاحية إعادة تعيين كلمة مرور هذا المستخدم');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        ActivityLogService::log(
            action: 'user_password_reset',
            target: $user,
            description: 'تمت إعادة تعيين كلمة مرور مستخدم',
            oldValues: null,
            newValues: [
                'email' => $user->email,
                'reset_by' => auth()->user()?->email,
            ]
        );

        return redirect()
            ->route('users.index')
            ->with('success', 'تمت إعادة تعيين كلمة المرور بنجاح');
    }

    private function isRootUser(): bool
    {
        return (bool) auth()->user()?->is_root;
    }

    private function allowedRolesForCurrentUser(): array
    {
        if ($this->isRootUser()) {
            return ['super-admin', 'admin', 'user'];
        }

        return ['admin', 'user'];
    }

    private function canManageTargetUser(User $targetUser): bool
    {
        if ($this->isRootUser()) {
            return true;
        }

        if ($targetUser->is_root) {
            return false;
        }

        if ($targetUser->hasRole('super-admin')) {
            return false;
        }

        return true;
    }
}
