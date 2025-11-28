<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use Exception;

class FavoriteController extends Controller
{
    // Thêm hoặc xóa tin yêu thích
    // public function toggle(Request $request, $id)
    // {
    //     try {
    //         $job = JobPosting::findOrFail($id);
    //         $user = Auth::user();

    //         if ($user->favorites()->where('job_posting_id', $id)->exists()) {
    //             $user->favorites()->detach($job->id);
    //             return back()->with('success', 'Đã xóa công việc khỏi danh sách yêu thích.');
    //         }

    //         $user->favorites()->attach($job->id);
    //         return back()->with('success', 'Đã lưu tin tuyển dụng vào danh sách yêu thích.');
    //     } catch (Exception $e) {
    //         return back()->with('error', 'Thao tác thất bại, vui lòng thử lại.');
    //     }
    // }

    public function toggle(Request $request, int $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'is_favorited' => null,
                'message' => 'Bạn cần đăng nhập để thực hiện thao tác này.',
            ], 200);
        }

        try {
            $job = JobPosting::findOrFail($id);

            $favorite = $user->favorites()->where('job_posting_id', $id)->first();

            if ($favorite) {
                $favorite->pivot->is_favorited = !$favorite->pivot->is_favorited;
                $favorite->pivot->save();

                $isFavorited = $favorite->pivot->is_favorited;
                $message = $isFavorited
                    ? 'Đã lưu tin tuyển dụng vào danh sách yêu thích.'
                    : 'Đã xóa tin tuyển dụng khỏi danh sách yêu thích.';
            } else {
                $user->favorites()->attach($job->id, ['is_favorited' => true]);
                $isFavorited = true;
                $message = 'Đã lưu tin tuyển dụng vào danh sách yêu thích.';
            }

            return response()->json([
                'success' => true,
                'is_favorited' => $isFavorited,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'is_favorited' => null,
                'message' => 'Thao tác thất bại, vui lòng thử lại.',
            ], 500);
        }
    }



    // Hiển thị danh sách yêu thích
    public function index()
    {
        $user = Auth::user();

        // $favorites = $user->favorites()->with('company')->latest()->get();

        // return \Inertia\Inertia::render('client/FavoriteJobs', [
        //     'favorites' => $favorites->map(function ($job) use ($user) {
        //         return [
        //             'id' => $job->id,
        //             'title' => $job->title,
        //             'slug' => $job->slug,
        //             'location' => $job->location,
        //             'salary' => $job->getSalaryRange(), // dùng helper trong JobPosting.php
        //             'posted' => $job->created_at ? $job->created_at->diffForHumans() : 'Mới đăng',
        //             'company' => $job->company->company_name ?? null,
        //             'company_logo' => $job->company->logo ? asset('storage/' . $job->company->logo) : null,
        //             'skills' => $job->skills->pluck('name')->toArray() ?? [],
        //             'job_type' => $job->job_type,
        //             'is_featured' => $job->is_featured,
        //             'is_favorited' => $user->favorites()->where('job_posting_id', $job->id)->exists(),
        //         ];
        //     }),
        // ]);

        // $favorites = $user->favorites()
        //     ->with([
        //         'company:id,company_name,logo',
        //         'skills:id,name',
        //     ])
        //     ->latest()
        //     ->get();

        $favorites = $user->favorites()
            ->wherePivot('is_favorited', 1)
            ->with([
                'company:id,company_name,logo',
                'skills:id,name',
            ])
            ->latest()
            ->get();

        return \Inertia\Inertia::render('client/FavoriteJobs', [
            'favorites' => $favorites->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'location' => $job->location,
                    'salary' => $job->getSalaryRange(),
                    'posted' => $job->created_at->diffForHumans(),

                    // Company
                    'company' => $job->company->company_name ?? null,
                    'company_logo' => $job->company->logo
                        ? (str_starts_with($job->company->logo, 'http')
                            ? $job->company->logo
                            : asset('storage/' . $job->company->logo))
                        : null,

                    // Job type
                    'type' => $job->job_type,

                    // Skills
                    'skills' => $job->skills->pluck('name')->toArray(),

                    'is_featured' => $job->is_featured,
                    'is_favorited' => true,
                ];
            })
        ]);

    }

    // Xóa tất cả
    public function clear()
    {
        try {
            $user = Auth::user();
            $user->favorites()->detach();
            return back()->with('success', 'Đã xóa toàn bộ danh sách yêu thích.');
        } catch (Exception $e) {
            return back()->with('error', 'Thao tác thất bại, vui lòng thử lại.');
        }
    }
}
