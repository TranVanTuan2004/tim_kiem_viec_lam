<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyReviewRequest;
use App\Models\Company;
use App\Models\CompanyReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(StoreCompanyReviewRequest $request, Company $company)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để viết đánh giá.');
        }

        $user = Auth::user();

        // Check if user has a candidate profile
        if (!$user->candidateProfile) {
            return back()->with('error', 'Bạn cần có hồ sơ ứng viên để viết đánh giá.');
        }

        // Check if user has already reviewed this company
        $existingReview = CompanyReview::where('company_id', $company->id)
            ->where('candidate_id', $user->candidateProfile->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá công ty này rồi.');
        }

        // Create review
        $review = CompanyReview::create([
            'company_id' => $company->id,
            'candidate_id' => $user->candidateProfile->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'status' => 'pending', // Default to pending for moderation
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ xét duyệt.');
    }

    /**
     * Update an existing review
     */
    public function update(StoreCompanyReviewRequest $request, Company $company, CompanyReview $review)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để cập nhật đánh giá.');
        }

        $user = Auth::user();

        // Check if user owns this review
        if (!$user->candidateProfile || $review->candidate_id !== $user->candidateProfile->id) {
            return back()->with('error', 'Bạn không có quyền cập nhật đánh giá này.');
        }

        // Update review
        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'status' => 'pending', // Reset to pending for re-moderation
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được cập nhật và đang chờ xét duyệt.');
    }

    /**
     * Delete a review
     */
    public function destroy(Company $company, CompanyReview $review)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xóa đánh giá.');
        }

        $user = Auth::user();

        // Check if user owns this review
        if (!$user->candidateProfile || $review->candidate_id !== $user->candidateProfile->id) {
            return back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá của bạn.');
    }

    /**
     * Get user's review for a company
     */
    public function getUserReview(Company $company)
    {
        if (!Auth::check() || !Auth::user()->candidateProfile) {
            return response()->json(['review' => null]);
        }

        $review = CompanyReview::where('company_id', $company->id)
            ->where('candidate_id', Auth::user()->candidateProfile->id)
            ->first();

        return response()->json(['review' => $review]);
    }
}

