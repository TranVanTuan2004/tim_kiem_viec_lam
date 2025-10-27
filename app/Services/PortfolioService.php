<?php

namespace App\Services;

use App\Models\Portfolio;
use App\Models\CandidateProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class PortfolioService
{
    /**
     * Get all portfolios for a candidate with pagination
     */
    public function getCandidatePortfolios(int $candidateId, int $perPage = 10): LengthAwarePaginator
    {
        return Portfolio::byCandidate($candidateId)
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get public portfolios for a candidate
     */
    public function getPublicPortfolios(int $candidateId, int $perPage = 10)
    {
        return Portfolio::byCandidate($candidateId)
            ->public()
            ->ordered()
            ->paginate($perPage);
    }

    /**
     * Get featured portfolios for a candidate
     */
    public function getFeaturedPortfolios(int $candidateId, int $limit = 3)
    {
        return Portfolio::byCandidate($candidateId)
            ->public()
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Get a single portfolio by ID
     */
    public function getPortfolio(int $portfolioId): ?Portfolio
    {
        return Portfolio::with('candidate.user')->find($portfolioId);
    }

    /**
     * Create a new portfolio
     */
    public function createPortfolio(int $candidateId, array $data): Portfolio
    {
        // Verify candidate exists
        $candidate = CandidateProfile::findOrFail($candidateId);

        // Handle image uploads
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = $this->uploadImages($data['images']);
        }

        // Set display order if not provided
        if (!isset($data['display_order'])) {
            $data['display_order'] = $this->getNextDisplayOrder($candidateId);
        }

        $portfolio = new Portfolio($data);
        $portfolio->candidate_id = $candidateId;
        $portfolio->save();

        return $portfolio->fresh();
    }

    /**
     * Update an existing portfolio
     */
    public function updatePortfolio(int $portfolioId, array $data): Portfolio
    {
        $portfolio = Portfolio::findOrFail($portfolioId);

        // Handle image uploads if new images are provided
        if (isset($data['images']) && is_array($data['images'])) {
            $newImages = [];
            
            foreach ($data['images'] as $image) {
                if ($image instanceof UploadedFile) {
                    // New uploaded file
                    $newImages[] = $this->uploadImage($image);
                } elseif (is_string($image)) {
                    // Existing image path
                    $newImages[] = $image;
                }
            }
            
            // Delete old images that are not in the new list
            $this->deleteRemovedImages($portfolio->images ?? [], $newImages);
            $data['images'] = $newImages;
        }

        $portfolio->update($data);
        
        return $portfolio->fresh();
    }

    /**
     * Delete a portfolio
     */
    public function deletePortfolio(int $portfolioId): bool
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        
        // Delete associated images
        if ($portfolio->images) {
            foreach ($portfolio->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        return $portfolio->delete();
    }

    /**
     * Reorder portfolios
     */
    public function reorderPortfolios(int $candidateId, array $portfolioIds): void
    {
        foreach ($portfolioIds as $index => $portfolioId) {
            Portfolio::where('id', $portfolioId)
                ->where('candidate_id', $candidateId)
                ->update(['display_order' => $index + 1]);
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(int $portfolioId): Portfolio
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->is_featured = !$portfolio->is_featured;
        $portfolio->save();
        
        return $portfolio;
    }

    /**
     * Toggle public status
     */
    public function togglePublic(int $portfolioId): Portfolio
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->is_public = !$portfolio->is_public;
        $portfolio->save();
        
        return $portfolio;
    }

    /**
     * Upload multiple images
     */
    private function uploadImages(array $files): array
    {
        $paths = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->uploadImage($file);
            }
        }
        
        return $paths;
    }

    /**
     * Upload a single image
     */
    private function uploadImage(UploadedFile $file): string
    {
        $path = $file->store('portfolios', 'public');
        return $path;
    }

    /**
     * Delete removed images
     */
    private function deleteRemovedImages(array $oldImages, array $newImages): void
    {
        $imagesToDelete = array_diff($oldImages, $newImages);
        
        foreach ($imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Get next display order for a candidate
     */
    private function getNextDisplayOrder(int $candidateId): int
    {
        $maxOrder = Portfolio::where('candidate_id', $candidateId)
            ->max('display_order');
        
        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Transform portfolio for API response
     */
    public function transformForResponse(Portfolio $portfolio): array
    {
        return [
            'id' => $portfolio->id,
            'title' => $portfolio->title,
            'description' => $portfolio->description,
            'project_url' => $portfolio->project_url,
            'github_url' => $portfolio->github_url,
            'demo_url' => $portfolio->demo_url,
            'images' => $portfolio->images ? array_map(function($path) {
                return Storage::url($path);
            }, $portfolio->images) : [],
            'main_image' => $portfolio->getMainImage() ? Storage::url($portfolio->getMainImage()) : null,
            'technologies' => $portfolio->technologies ?? [],
            'start_date' => $portfolio->start_date?->format('Y-m-d'),
            'end_date' => $portfolio->end_date?->format('Y-m-d'),
            'duration' => $portfolio->getDuration(),
            'is_ongoing' => $portfolio->is_ongoing,
            'is_featured' => $portfolio->is_featured,
            'is_public' => $portfolio->is_public,
            'display_order' => $portfolio->display_order,
            'created_at' => $portfolio->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $portfolio->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Check if user owns the portfolio
     */
    public function userOwnsPortfolio(int $userId, int $portfolioId): bool
    {
        $portfolio = Portfolio::find($portfolioId);
        
        if (!$portfolio) {
            return false;
        }
        
        return $portfolio->candidate->user_id === $userId;
    }
}

