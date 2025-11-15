<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyReview;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = CompanyReview::with(['company', 'candidate.user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('review', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->paginate(15);

        // Statistics
        $statistics = [
            'total' => CompanyReview::count(),
            'pending' => CompanyReview::where('status', 'pending')->count(),
            'approved' => CompanyReview::where('status', 'approved')->count(),
            'rejected' => CompanyReview::where('status', 'rejected')->count(),
            'average_rating' => round(CompanyReview::where('status', 'approved')->avg('rating') ?? 0, 1),
        ];

        return Inertia::render('admin/company-reviews/Index', [
            'reviews' => $reviews,
            'statistics' => $statistics,
            'filters' => [
                'status' => $request->status,
                'rating' => $request->rating,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Approve a review
     */
    public function approve(CompanyReview $review)
    {
        $review->update(['status' => 'approved']);

        // Update company rating
        $this->updateCompanyRating($review->company_id);

        return back()->with('success', 'Đã duyệt đánh giá thành công.');
    }

    /**
     * Reject a review
     */
    public function reject(CompanyReview $review)
    {
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Đã từ chối đánh giá.');
    }

    /**
     * Delete a review
     */
    public function destroy(CompanyReview $review)
    {
        $companyId = $review->company_id;
        $review->delete();

        // Update company rating after deletion
        $this->updateCompanyRating($companyId);

        return back()->with('success', 'Đã xóa đánh giá.');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:company_reviews,id',
        ]);

        $reviews = CompanyReview::whereIn('id', $request->ids)->get();
        
        foreach ($reviews as $review) {
            $review->update(['status' => 'approved']);
        }

        // Update company ratings
        $companyIds = $reviews->pluck('company_id')->unique();
        foreach ($companyIds as $companyId) {
            $this->updateCompanyRating($companyId);
        }

        return back()->with('success', "Đã duyệt {$reviews->count()} đánh giá.");
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:company_reviews,id',
        ]);

        CompanyReview::whereIn('id', $request->ids)->update(['status' => 'rejected']);

        return back()->with('success', "Đã từ chối " . count($request->ids) . " đánh giá.");
    }

    /**
     * Update company rating and total reviews
     */
    private function updateCompanyRating($companyId)
    {
        $company = \App\Models\Company::find($companyId);
        
        if ($company) {
            $approvedReviews = CompanyReview::where('company_id', $companyId)
                ->where('status', 'approved')
                ->get();

            $company->rating = $approvedReviews->avg('rating') ?? 0;
            $company->total_reviews = $approvedReviews->count();
            $company->save();
        }
    }
}



