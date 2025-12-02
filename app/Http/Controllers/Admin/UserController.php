<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Test Case 10: Validate URL parameters
     */
    public function index(Request $request): Response|RedirectResponse
    {
        // Test Case 10: Validate page parameter
        $page = $request->get('page', 1);
        if (!is_numeric($page) || $page < 1) {
            return redirect()->route('admin.users.index', ['page' => 1])
                ->with('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
        }

        $query = User::with('roles');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Test Case 10: Check if requested page exists
        if ($page > $users->lastPage() && $users->total() > 0) {
            return redirect()->route('admin.users.index', array_merge(
                $request->except('page'),
                ['page' => 1]
            ))->with('error', 'Trang không tồn tại. Đã chuyển về trang đầu tiên.');
        }

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $roles = Role::all();

        return Inertia::render('admin/users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'is_active' => 1, // Default active for new users
        ]);

        // Assign roles
        $user->roles()->attach($validated['roles']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo người dùng thành công!');
    }

    /**
     * Display the specified resource.
     * Test Case 3: Handle invalid IDs
     */
    public function show($id): Response|RedirectResponse
    {
        // Test Case 3: Check if ID is numeric
        if (!is_numeric($id)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không tìm thấy người dùng này.');
        }

        try {
            $user = User::with('roles')->findOrFail($id);

            return Inertia::render('admin/users/Show', [
                'user' => $user,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không tìm thấy người dùng này. Có thể đã bị xóa.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Test Case 3: Handle invalid IDs
     */
    public function edit($id): Response|RedirectResponse
    {
        // Test Case 3: Check if ID is numeric
        if (!is_numeric($id)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không tìm thấy người dùng này.');
        }

        try {
            $user = User::with('roles')->findOrFail($id);
            $roles = Role::all();

            return Inertia::render('admin/users/Edit', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'bio' => $user->bio,
                    'roles' => $user->roles,
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'), // For optimistic locking
                ],
                'roles' => $roles,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không tìm thấy người dùng này. Có thể đã bị xóa.');
        }
    }

    /**
     * Update the specified resource in storage.
     * Test Case 2: Optimistic locking (concurrent updates)
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            // Test Case 2: Optimistic Locking - Check if data was updated by another user
            if ($request->has('updated_at')) {
                $requestUpdatedAt = $request->input('updated_at');
                $dbUpdatedAt = $user->updated_at->format('Y-m-d H:i:s');

                if ($requestUpdatedAt !== $dbUpdatedAt) {
                    return back()->withErrors([
                        'concurrent_update' => 'Dữ liệu đã được cập nhật bởi người dùng khác. Vui lòng tải lại trang trước khi cập nhật.'
                    ])->withInput();
                }
            }

            $validated = $request->validated();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]);

            // Update password if provided
            if (!empty($validated['password'])) {
                $user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            // Sync roles
            $user->roles()->sync($validated['roles']);

            return redirect()->route('admin.users.index')
                ->with('success', 'Cập nhật người dùng thành công!');

        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra khi cập nhật người dùng. Vui lòng thử lại.'
            ])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * Test Case 1: Handle deletion of non-existent users
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->forceDelete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Xóa người dùng thành công!');

        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không tìm thấy người dùng này. Có thể đã bị xóa bởi người dùng khác.');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Có lỗi xảy ra khi xóa người dùng. Vui lòng thử lại.');
        }
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['is_active' => $user->is_active])
            ->log('Thay đổi trạng thái tài khoản');

        return back()->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function resetPassword(User $user)
    {
        $user->password = Hash::make('12345678');
        $user->save();

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Admin reset mật khẩu user.');

        return back()->with('success', 'Mật khẩu đã được reset thành 12345678');
    }

    public function sendResetLink(User $user)
    {
        Password::sendResetLink(['email' => $user->email]);

        activity()->causedBy(auth()->user())
            ->performedOn($user)
            ->log('Admin gửi email đặt lại mật khẩu.');

        return back()->with('success', 'Đã gửi email đặt lại mật khẩu.');
    }

    public function activityLogs(User $user)
    {
        $logs = Activity::where('causer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return Inertia::render('admin/users/ActivityLogs', [
            'user' => $user,
            'logs' => $logs
        ]);
    }
}
