<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class NotificationController extends Controller
{
 
    public function index(Request $request)
    {
        $query = Notification::where('type', 'system_notification')
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read === 'true');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Notification::where('type', 'system_notification')->count(),
            'unread' => Notification::where('type', 'system_notification')
                ->where('is_read', false)
                ->count(),
            'read' => Notification::where('type', 'system_notification')
                ->where('is_read', true)
                ->count(),
        ];

        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/Notifications/Index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'users' => $users,
            'filters' => $request->only(['user_id', 'is_read', 'search']),
        ]);
    }

    public function create()
    {
        // Thống kê số lượng users theo role
        $userStats = [
            'all' => User::count(),
            'candidates' => User::role('Candidate')->count(),
            'employers' => User::role('Employer')->count(),
            'admins' => User::role('Admin')->count(),
        ];

        return Inertia::render('admin/Notifications/Create', [
            'userStats' => $userStats,
        ]);
    }

    /**
     * Gửi thông báo hệ thống
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,candidates,employers,admins,specific',
            'user_ids' => 'required_if:target_type,specific|array',
            'user_ids.*' => 'exists:users,id',
            'data' => 'nullable|array',
        ]);

        $targetType = $validated['target_type'];
        $users = collect();

        // Lấy danh sách users dựa trên target_type
        switch ($targetType) {
            case 'all':
                $users = User::all();
                break;
            case 'candidates':
                $users = User::role('Candidate')->get();
                break;
            case 'employers':
                $users = User::role('Employer')->get();
                break;
            case 'admins':
                $users = User::role('Admin')->get();
                break;
            case 'specific':
                $users = User::whereIn('id', $validated['user_ids'])->get();
                break;
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Không tìm thấy người dùng nào để gửi thông báo.');
        }

        // Tạo thông báo cho từng user
        $notifications = [];
        $count = 0;

        DB::transaction(function () use ($validated, $users, &$notifications, &$count) {
            foreach ($users as $user) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'type' => 'system_notification',
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'data' => $validated['data'] ?? null,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $count++;
            }

            // Insert hàng loạt để tối ưu hiệu suất
            if (!empty($notifications)) {
                Notification::insert($notifications);
            }
        });

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', "Đã gửi thông báo thành công cho {$count} người dùng.");
    }

    /**
     * Hiển thị chi tiết thông báo
     */
    public function show($id)
    {
        $notification = Notification::where('type', 'system_notification')
            ->with('user:id,name,email')
            ->findOrFail($id);

        return Inertia::render('admin/Notifications/Show', [
            'notification' => $notification,
        ]);
    }

    /**
     * Đánh dấu thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Đã đánh dấu là đã đọc');
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Đã đánh dấu tất cả là đã đọc');
    }

    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        $notification = Notification::where('type', 'system_notification')
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Đã xóa thông báo thành công.');
    }

    /**
     * Xóa nhiều thông báo
     */
    public function destroyMultiple(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);

        $count = Notification::where('type', 'system_notification')
            ->whereIn('id', $validated['ids'])
            ->delete();

        return back()->with('success', "Đã xóa {$count} thông báo thành công.");
    }

    /**
     * Thống kê thông báo hệ thống
     */
    public function stats()
    {
        $stats = [
            'total' => Notification::where('type', 'system_notification')->count(),
            'unread' => Notification::where('type', 'system_notification')
                ->where('is_read', false)
                ->count(),
            'read' => Notification::where('type', 'system_notification')
                ->where('is_read', true)
                ->count(),
            'today' => Notification::where('type', 'system_notification')
                ->whereDate('created_at', today())
                ->count(),
            'this_week' => Notification::where('type', 'system_notification')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Notification::where('type', 'system_notification')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Thống kê theo ngày trong 30 ngày qua
        $dailyStats = Notification::where('type', 'system_notification')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'stats' => $stats,
            'daily_stats' => $dailyStats,
        ]);
    }
}

