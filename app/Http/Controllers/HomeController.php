<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->take(3)->get()->map(function ($s, $index) {
            $colors = ['primary', 'accent', 'secondary'];
            $gallery = is_array($s->gallery) ? $s->gallery : json_decode($s->gallery, true) ?? [];
            $img = !empty($gallery) ? $gallery[0] : '/images/placeholder.jpg';

            return [
                'title' => $s->name,
                'tagline' => 'TEKNOLOGI MODERN',
                'desc' => $s->description_short,
                'img' => $img, 
                'color' => $colors[$index % 3],
            ];
        });

        $projects = Project::latest()->take(10)->get()->map(function ($project) {
            $images = $project->images;
            return [
                'img' => $images[0] ?? '/images/placeholder.jpg',
                'title' => $project->title,
                'category' => $project->category,
            ];
        });
        
        $testimonials = Testimonial::where('is_active', true)->get();

        $faqs = \App\Models\Faq::landing()->orderBy('order')->get()->map(function($f) {
            return [
                'q' => $f->question,
                'a' => $f->answer
            ];
        });

        return view('welcome', compact('services', 'projects', 'testimonials', 'faqs'));
    }
}
