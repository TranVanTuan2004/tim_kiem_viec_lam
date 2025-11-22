<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\JobPosting;
use App\Models\Notification;
use Illuminate\Support\Collection;

class JobMatchingService
{
    /**
     * Find matching candidates for a job posting
     *
     * @param JobPosting $jobPosting
     * @param int $minMatchScore Minimum match score (0-100)
     * @return Collection
     */
    public function findMatchingCandidates(JobPosting $jobPosting, int $minMatchScore = 50): Collection
    {
        $candidates = CandidateProfile::with(['skills', 'user'])
            ->where('is_available', true)
            ->where('job_alert_enabled', true)
            ->get();

        $matches = [];

        foreach ($candidates as $candidate) {
            $score = $this->calculateMatchScore($candidate, $jobPosting);
            
            if ($score >= $minMatchScore) {
                $matches[] = [
                    'candidate' => $candidate,
                    'score' => $score,
                ];
            }
        }

        // Sort by score descending
        usort($matches, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return collect($matches);
    }

    /**
     * Calculate match score between candidate and job
     *
     * @param CandidateProfile $candidate
     * @param JobPosting $jobPosting
     * @return int Score from 0-100
     */
    public function calculateMatchScore(CandidateProfile $candidate, JobPosting $jobPosting): int
    {
        $scores = [];

        // 1. Skills Match (40%)
        $scores['skills'] = $this->calculateSkillsMatch($candidate, $jobPosting) * 0.4;

        // 2. Experience Level Match (20%)
        $scores['experience'] = $this->calculateExperienceMatch($candidate, $jobPosting) * 0.2;

        // 3. Salary Range Match (20%)
        $scores['salary'] = $this->calculateSalaryMatch($candidate, $jobPosting) * 0.2;

        // 4. Location Match (20%)
        $scores['location'] = $this->calculateLocationMatch($candidate, $jobPosting) * 0.2;

        $totalScore = array_sum($scores);

        return (int) round($totalScore);
    }

    /**
     * Calculate skills match percentage
     */
    private function calculateSkillsMatch(CandidateProfile $candidate, JobPosting $jobPosting): int
    {
        $candidateSkills = $candidate->skills->pluck('name')->map(function ($skill) {
            return strtolower(trim($skill));
        })->toArray();

        $jobSkills = $jobPosting->skills->pluck('name')->map(function ($skill) {
            return strtolower(trim($skill));
        })->toArray();

        if (empty($jobSkills)) {
            return 100; // If job has no specific skill requirements, consider it a match
        }

        $matchingSkills = array_intersect($candidateSkills, $jobSkills);
        $matchPercentage = (count($matchingSkills) / count($jobSkills)) * 100;

        return (int) round($matchPercentage);
    }

    /**
     * Calculate experience level match
     */
    private function calculateExperienceMatch(CandidateProfile $candidate, JobPosting $jobPosting): int
    {
        $experienceLevels = ['fresher', 'junior', 'middle', 'senior', 'lead'];
        
        $candidateLevel = array_search($candidate->experience_level, $experienceLevels);
        $jobLevel = array_search($jobPosting->experience_level, $experienceLevels);

        if ($candidateLevel === false || $jobLevel === false) {
            return 50; // Neutral score if levels not defined
        }

        // Exact match
        if ($candidateLevel === $jobLevel) {
            return 100;
        }

        // One level difference
        if (abs($candidateLevel - $jobLevel) === 1) {
            return 70;
        }

        // Two levels difference
        if (abs($candidateLevel - $jobLevel) === 2) {
            return 40;
        }

        return 0;
    }

    /**
     * Calculate salary match
     */
    private function calculateSalaryMatch(CandidateProfile $candidate, JobPosting $jobPosting): int
    {
        if (!$candidate->expected_salary || !$jobPosting->salary_min) {
            return 50; // Neutral score if salary not specified
        }

        $expectedSalary = $candidate->expected_salary;
        $salaryMin = $jobPosting->salary_min;
        $salaryMax = $jobPosting->salary_max ?? $salaryMin * 1.5;

        // Candidate's expected salary is within job's range
        if ($expectedSalary >= $salaryMin && $expectedSalary <= $salaryMax) {
            return 100;
        }

        // Candidate expects slightly more (up to 20% above max)
        if ($expectedSalary > $salaryMax && $expectedSalary <= $salaryMax * 1.2) {
            return 70;
        }

        // Candidate expects less (good for employer, still notify)
        if ($expectedSalary < $salaryMin) {
            return 80;
        }

        return 0;
    }

    /**
     * Calculate location match
     */
    private function calculateLocationMatch(CandidateProfile $candidate, JobPosting $jobPosting): int
    {
        // If candidate has no location preferences, match any location
        if (empty($candidate->preferred_locations)) {
            return 100;
        }

        $preferredLocations = is_array($candidate->preferred_locations) 
            ? $candidate->preferred_locations 
            : json_decode($candidate->preferred_locations, true) ?? [];

        // Check if job location matches any preferred location
        foreach ($preferredLocations as $location) {
            if (stripos($jobPosting->location, $location) !== false || 
                stripos($location, $jobPosting->location) !== false) {
                return 100;
            }
        }

        // Check city match
        if ($candidate->city && stripos($jobPosting->location, $candidate->city) !== false) {
            return 80;
        }

        // Check province match
        if ($candidate->province && stripos($jobPosting->location, $candidate->province) !== false) {
            return 60;
        }

        return 0;
    }

    /**
     * Send job match notifications to candidates
     *
     * @param JobPosting $jobPosting
     * @param Collection $matches
     * @return int Number of notifications sent
     */
    public function sendJobMatchNotifications(JobPosting $jobPosting, Collection $matches): int
    {
        $count = 0;

        foreach ($matches as $match) {
            $candidate = $match['candidate'];
            $score = $match['score'];

            Notification::create([
                'user_id' => $candidate->user_id,
                'type' => 'job_match',
                'title' => 'Công việc phù hợp với bạn!',
                'message' => "Có công việc mới \"{$jobPosting->title}\" phù hợp {$score}% với hồ sơ của bạn.",
                'data' => [
                    'job_posting_id' => $jobPosting->id,
                    'job_title' => $jobPosting->title,
                    'company_name' => $jobPosting->company->company_name ?? 'N/A',
                    'match_score' => $score,
                    'location' => $jobPosting->location,
                    'salary_range' => $jobPosting->salary_min && $jobPosting->salary_max 
                        ? number_format($jobPosting->salary_min) . ' - ' . number_format($jobPosting->salary_max) . ' VNĐ'
                        : 'Thỏa thuận',
                ],
                'is_read' => false,
            ]);

            $count++;
        }

        return $count;
    }
}
