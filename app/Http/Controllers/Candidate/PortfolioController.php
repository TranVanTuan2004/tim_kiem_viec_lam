<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portfolio\StorePortfolioRequest;
use App\Http\Requests\Portfolio\UpdatePortfolioRequest;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    protected PortfolioService $portfolioService;

    public function __construct(PortfolioService $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * Display a listing of the portfolios.
     */
    public function index()
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create')
                ->with('error', 'Please complete your candidate profile first.');
        }

        $portfolios = $this->portfolioService->getCandidatePortfolios($candidateProfile->id, 12);

        return Inertia::render('candidate/Portfolio/Index', [
            'portfolios' => $portfolios->through(fn($portfolio) => 
                $this->portfolioService->transformForResponse($portfolio)
            ),
        ]);
    }

    /**
     * Show the form for creating a new portfolio.
     */
    public function create()
    {
        return Inertia::render('candidate/Portfolio/Create');
    }

    /**
     * Store a newly created portfolio in storage.
     */
    public function store(StorePortfolioRequest $request)
    {
        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()->route('candidate.profile.create')
                ->with('error', 'Please complete your candidate profile first.');
        }

        $portfolio = $this->portfolioService->createPortfolio(
            $candidateProfile->id,
            $request->validated()
        );

        return redirect()->route('candidate.portfolios.index')
            ->with('success', 'Portfolio project created successfully!');
    }

    /**
     * Display the specified portfolio.
     */
    public function show(int $id)
    {
        $portfolio = $this->portfolioService->getPortfolio($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Check ownership
        if ($portfolio->candidate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('candidate/Portfolio/Show', [
            'portfolio' => $this->portfolioService->transformForResponse($portfolio),
        ]);
    }

    /**
     * Show the form for editing the specified portfolio.
     */
    public function edit(int $id)
    {
        $portfolio = $this->portfolioService->getPortfolio($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Check ownership
        if ($portfolio->candidate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return Inertia::render('candidate/Portfolio/Edit', [
            'portfolio' => $this->portfolioService->transformForResponse($portfolio),
        ]);
    }

    /**
     * Update the specified portfolio in storage.
     */
    public function update(UpdatePortfolioRequest $request, int $id)
    {
        $portfolio = $this->portfolioService->updatePortfolio($id, $request->validated());

        return redirect()->route('candidate.portfolios.index')
            ->with('success', 'Portfolio project updated successfully!');
    }

    /**
     * Remove the specified portfolio from storage.
     */
    public function destroy(int $id)
    {
        $portfolio = $this->portfolioService->getPortfolio($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Check ownership
        if ($portfolio->candidate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $this->portfolioService->deletePortfolio($id);

        return redirect()->route('candidate.portfolios.index')
            ->with('success', 'Portfolio project deleted successfully!');
    }

    /**
     * Reorder portfolios.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'portfolio_ids' => ['required', 'array'],
            'portfolio_ids.*' => ['integer', 'exists:portfolios,id'],
        ]);

        $user = Auth::user();
        $candidateProfile = $user->candidateProfile;

        $this->portfolioService->reorderPortfolios(
            $candidateProfile->id,
            $request->portfolio_ids
        );

        return response()->json([
            'success' => true,
            'message' => 'Portfolios reordered successfully!',
        ]);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(int $id)
    {
        $portfolio = $this->portfolioService->getPortfolio($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Check ownership
        if ($portfolio->candidate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $portfolio = $this->portfolioService->toggleFeatured($id);

        return response()->json([
            'success' => true,
            'is_featured' => $portfolio->is_featured,
            'message' => 'Featured status updated successfully!',
        ]);
    }

    /**
     * Toggle public status.
     */
    public function togglePublic(int $id)
    {
        $portfolio = $this->portfolioService->getPortfolio($id);

        if (!$portfolio) {
            abort(404, 'Portfolio not found');
        }

        // Check ownership
        if ($portfolio->candidate->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $portfolio = $this->portfolioService->togglePublic($id);

        return response()->json([
            'success' => true,
            'is_public' => $portfolio->is_public,
            'message' => 'Visibility status updated successfully!',
        ]);
    }
}

