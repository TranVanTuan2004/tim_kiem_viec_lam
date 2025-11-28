<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Notification;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? (function() use ($request) {
                    $user = $request->user();
                    $avatar = $user->avatar;
                    
                    // Nếu là Employer, ưu tiên lấy logo công ty
                    if ($user->hasRole('Employer')) {
                        $company = $user->company;
                        if ($company && $company->logo) {
                            $avatar = $company->logo;
                        }
                    }

                    // Xử lý URL avatar (nếu không phải full URL thì thêm storage path)
                    if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL)) {
                        $avatar = asset('storage/' . $avatar);
                    }

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $avatar,
                        'roles' => $user->getRoleNames(),
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                    ];
                })() : null,
            ],
            'candidateProfile' => function () use ($request) {
                if (!$request->user()) {
                    return null;
                }
                
                $candidateProfile = $request->user()->candidateProfile;
                if (!$candidateProfile) {
                    return null;
                }
                
                return [
                    'avatar_url' => $candidateProfile->avatar 
                        ? asset('storage/' . $candidateProfile->avatar) 
                        : null,
                ];
            },
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        
            'flash' => [
		    'success' => fn () => $request->session()->get('success'),
		    'error'   => fn () => $request->session()->get('error'),
		    'info'    => fn () => $request->session()->get('info'),
		],
            'notifications' => function () use ($request) {
                if (!$request->user() || !$request->user()->hasRole('Admin')) {
                    return [
                        'unread_count' => 0,
                        'recent' => [],
                    ];
                }

                $user = $request->user();
                $unreadCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();

                $recentNotifications = Notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => $notification->type,
                            'title' => $notification->title,
                            'message' => $notification->message,
                            'data' => $notification->data,
                            'is_read' => $notification->is_read,
                            'created_at' => $notification->created_at->diffForHumans(),
                            'created_at_full' => $notification->created_at->toDateTimeString(),
                        ];
                    });

                return [
                    'unread_count' => $unreadCount,
                    'recent' => $recentNotifications,
                ];
            },
            // 'ziggy' => function () use ($request) {
            //     return array_merge((new \Tighten\Ziggy\Ziggy)->toArray(), [
            //         'location' => $request->url(),
            //     ]);
            // },
        ];
    }
}
