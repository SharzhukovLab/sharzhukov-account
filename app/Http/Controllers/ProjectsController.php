<?php

namespace App\Http\Controllers;

use App\Models\UserProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->orderByDesc('created_at')
            ->get();

        return view('account.projects.index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        return view('account.projects.form', [
            'project' => new UserProject(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);

        $data['user_id'] = $request->user()->id;
        $data['status']  = 'pending';
        $data['description'] = $this->sanitizeHtml($data['description'] ?? null);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('user-projects/covers', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('user-projects/gallery', 'public');
            }
            $data['gallery'] = $gallery;
        }

        unset($data['cover_image'], $data['gallery']);

        UserProject::create($data);

        return redirect()
            ->route('projects.index')
            ->with('status', '✓ Project submitted for moderation');
    }

    public function edit(UserProject $project)
    {
        if (!$project->isOwnedBy(auth()->user())) {
            abort(403);
        }

        return view('account.projects.form', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, UserProject $project)
    {
        if (!$project->isOwnedBy($request->user())) {
            abort(403);
        }

        $data = $this->validateProject($request);
        $data['description'] = $this->sanitizeHtml($data['description'] ?? null);

        // Правка approved → обратно в pending
        if ($project->status === 'approved') {
            $data['status'] = 'pending';
        }

        if ($request->hasFile('cover_image')) {
            if ($project->cover_image_path) {
                Storage::disk('public')->delete($project->cover_image_path);
            }
            $data['cover_image_path'] = $request->file('cover_image')->store('user-projects/covers', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = $project->gallery ?? [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('user-projects/gallery', 'public');
            }
            $data['gallery'] = $gallery;
        }

        unset($data['cover_image'], $data['gallery']);

        $project->update($data);

        return redirect()
            ->route('projects.index')
            ->with('status', '✓ Project updated');
    }

    public function destroy(Request $request, UserProject $project)
    {
        if (!$project->isOwnedBy($request->user())) {
            abort(403);
        }

        $project->delete();

        return back()->with('status', '✓ Project deleted');
    }

    protected function sanitizeHtml(?string $html): ?string
    {
        if (!$html) return $html;

        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><h4><blockquote>';
        $clean = strip_tags($html, $allowed);

        $clean = preg_replace('/\s+on[a-z]+\s*=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace("/\s+on[a-z]+\s*=\s*'[^']*'/i", '', $clean);
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*[^\s>]+/i', '', $clean);
        $clean = preg_replace('/href\s*=\s*"javascript:[^"]*"/i', 'href="#"', $clean);
        $clean = preg_replace("/href\s*=\s*'javascript:[^']*'/i", 'href="#"', $clean);

        return $clean;
    }

    protected function validateProject(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:200'],
            'tagline'      => ['nullable', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:30000'],
            'github_url'   => ['nullable', 'url', 'max:500'],
            'demo_url'     => ['nullable', 'url', 'max:500'],
            'download_url' => ['nullable', 'url', 'max:500'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'tech_stack'   => ['nullable', 'array', 'max:15'],
            'tech_stack.*' => ['string', 'max:40'],
            'license'      => ['nullable', 'string', 'max:80'],
            'status_label' => ['nullable', 'string', 'in:Stable,Beta,Alpha,WIP,Deprecated'],
            'cover_image'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery'      => ['nullable', 'array', 'max:8'],
            'gallery.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
    }
}
