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

    public function toggle(Request $request, $id)
    {
        try {
            $job = JobPosting::findOrFail($id);
            $user = Auth::user();

            if ($user->favorites()->where('job_posting_id', $id)->exists()) {
                // Xóa favorite
                $user->favorites()->detach($job->id);
                $isFavorited = false;
                $message = 'Đã xóa công việc khỏi danh sách yêu thích.';
            } else {
                // Thêm favorite
                $user->favorites()->attach($job->id);
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
        $favorites = $user->favorites()->latest()->get();
        return view('favorites.index', compact('favorites'));
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
