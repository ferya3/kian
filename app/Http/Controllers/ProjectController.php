<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProjectCategory::query()->orderBy('position')->withCount('projects')->get();

        $projects = Project::query()
            ->with('category')
            ->when($request->string('type')->toString(), fn ($q, $slug) => $q
                ->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->orderBy('position')
            ->orderByDesc('year')
            ->get();

        $this->seo()
            ->title(__('site.seo.projects.title'))
            ->description(__('site.seo.projects.description'))
            ->breadcrumbs([[__('site.nav.home'), route('home')], [__('site.nav.items.projects_index'), null]]);

        return view('pages.projects.index', compact('categories', 'projects'));
    }

    public function show(Project $project)
    {
        $project->load(['category', 'products.category']);

        $more = Project::query()
            ->with('category')
            ->where('id', '!=', $project->id)
            ->where('project_category_id', $project->project_category_id)
            ->orderByDesc('year')
            ->take(3)
            ->get();

        $this->seo()
            ->title($project->title.' — '.$project->city)
            ->description($project->summary)
            ->image($project->cover_image)
            ->type('article')
            ->breadcrumbs([
                [__('site.nav.home'), route('home')],
                [__('site.nav.items.projects_index'), route('projects.index')],
                [$project->title, null],
            ]);

        return view('pages.projects.show', compact('project', 'more'));
    }
}
