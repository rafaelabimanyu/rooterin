<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    protected $testimonialRepo;

    public function __construct(TestimonialRepositoryInterface $testimonialRepo)
    {
        $this->testimonialRepo = $testimonialRepo;
    }

    public function index()
    {
        $testimonials = $this->testimonialRepo->paginate(10);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'photo' => 'nullable|image|max:2048',
                'rating' => 'required|integer|min:1|max:5',
                'content' => 'required',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active');

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('testimonials', 'public');
                $validated['photo'] = '/storage/' . $path;
            }

            $this->testimonialRepo->create($validated);

            return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created successfully.');
        } catch (\Exception $e) {
            Log::error('Testimonial Store Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat testimoni. Silakan coba lagi.')->withInput();
        }
    }

    public function edit($id)
    {
        $testimonial = $this->testimonialRepo->find($id);
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id)
    {
        try {
            $testimonial = $this->testimonialRepo->find($id);

            $validated = $request->validate([
                'name' => 'required|max:255',
                'photo' => 'nullable|image|max:2048',
                'rating' => 'required|integer|min:1|max:5',
                'content' => 'required',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->has('is_active');

            if ($request->hasFile('photo')) {
                // Delete old photo if it is dynamic storage path
                if ($testimonial->photo && str_starts_with($testimonial->photo, '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $testimonial->photo);
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('photo')->store('testimonials', 'public');
                $validated['photo'] = '/storage/' . $path;
            }

            $this->testimonialRepo->update($id, $validated);

            return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated successfully.');
        } catch (\Exception $e) {
            Log::error('Testimonial Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui testimoni. Silakan coba lagi.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $testimonial = $this->testimonialRepo->find($id);
            if ($testimonial->photo && str_starts_with($testimonial->photo, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $testimonial->photo);
                Storage::disk('public')->delete($oldPath);
            }
            $this->testimonialRepo->delete($id);
            return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Testimonial Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus testimoni.');
        }
    }

    public function toggleActive($id)
    {
        try {
            $testimonial = $this->testimonialRepo->find($id);
            $this->testimonialRepo->update($id, ['is_active' => !$testimonial->is_active]);
            return redirect()->back()->with('success', 'Status testimoni berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('Testimonial Toggle Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status.');
        }
    }
}
