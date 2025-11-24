<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\CandidateCv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CvController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile;

        if (!$candidate) {
            return redirect()->route('candidate.profile.create');
        }

        // Migrate existing cv_file from profile if not already in cvs table
        if ($candidate->cv_file) {
            $exists = $candidate->cvs()->where('file_path', $candidate->cv_file)->exists();
            
            if (!$exists) {
                // Get filename from path
                $filename = basename($candidate->cv_file);
                
                $candidate->cvs()->create([
                    'name' => 'CV Profile (' . date('d/m/Y') . ')',
                    'file_path' => $candidate->cv_file,
                    'is_default' => $candidate->cvs()->count() === 0,
                ]);
            }
        }

        $cvs = $candidate->cvs()->orderBy('created_at', 'desc')->get()->map(function ($cv) {
            return [
                'id' => $cv->id,
                'name' => $cv->name,
                'file_path' => $cv->file_path,
                'file_url' => asset('storage/' . $cv->file_path),
                'is_default' => $cv->is_default,
                'created_at' => $cv->created_at->format('d/m/Y H:i'),
            ];
        });

        return Inertia::render('candidate/CVs/Index', [
            'cvs' => $cvs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        $user = Auth::user();
        $candidate = $user->candidateProfile;

        if (!$candidate) {
            return redirect()->back()->with('error', 'Vui lòng tạo hồ sơ trước.');
        }

        $path = $request->file('file')->store('cvs', 'public');

        // If this is the first CV, make it default
        $isFirst = $candidate->cvs()->count() === 0;

        $candidate->cvs()->create([
            'name' => $request->name,
            'file_path' => $path,
            'is_default' => $isFirst,
        ]);

        return redirect()->back()->with('success', 'Tải lên CV thành công.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile;
        $cv = $candidate->cvs()->findOrFail($id);

        if ($cv->file_path) {
            Storage::disk('public')->delete($cv->file_path);
        }

        $cv->delete();

        return redirect()->back()->with('success', 'Xóa CV thành công.');
    }

    public function setDefault($id)
    {
        $user = Auth::user();
        $candidate = $user->candidateProfile;
        $cv = $candidate->cvs()->findOrFail($id);

        // Unset other defaults
        $candidate->cvs()->where('id', '!=', $id)->update(['is_default' => false]);

        $cv->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Đã đặt làm CV mặc định.');
    }
}
