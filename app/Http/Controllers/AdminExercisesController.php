<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminExercisesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));

        $attemptCounts = DB::table('exercise_attempts')
            ->select('exercise_id', DB::raw('count(*) as total'))
            ->groupBy('exercise_id');

        $exercises = DB::table('exercises')
            ->leftJoinSub($attemptCounts, 'attempt_counts', fn ($join) => $join->on('exercises.id', '=', 'attempt_counts.exercise_id'))
            ->when($search !== '', fn ($query) => $query->where('exercises.title', 'like', '%' . $search . '%'))
            ->select('exercises.id', 'exercises.title', 'exercises.type', 'exercises.source', 'exercises.created_at', DB::raw('coalesce(attempt_counts.total, 0) as attempts_total'))
            ->orderByDesc('attempts_total')
            ->orderBy('exercises.title')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin-exercises', [
            'exercises' => $exercises,
            'filters' => ['q' => $search],
            'stats' => [
                'published' => DB::table('exercises')->count(),
                'drafts' => 0,
                'attempts' => DB::table('exercise_attempts')->count(),
            ],
        ]);
    }
}