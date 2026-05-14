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

        $answerStats = DB::table('exercise_attempts')
            ->leftJoin('attempt_answers', 'attempt_answers.attempt_id', '=', 'exercise_attempts.id')
            ->select(
                'exercise_attempts.exercise_id',
                DB::raw('count(attempt_answers.id) as answers_total'),
                DB::raw('coalesce(sum(case when attempt_answers.is_correct = 1 then 1 else 0 end), 0) as correct_answers_total')
            )
            ->groupBy('exercise_attempts.exercise_id');

        $exercises = DB::table('exercises')
            ->leftJoinSub($attemptCounts, 'attempt_counts', fn ($join) => $join->on('exercises.id', '=', 'attempt_counts.exercise_id'))
            ->leftJoinSub($answerStats, 'answer_stats', fn ($join) => $join->on('exercises.id', '=', 'answer_stats.exercise_id'))
            ->when($search !== '', fn ($query) => $query->where('exercises.title', 'like', '%' . $search . '%'))
            ->select(
                'exercises.id',
                'exercises.title',
                'exercises.type',
                'exercises.source',
                'exercises.created_at',
                DB::raw('coalesce(attempt_counts.total, 0) as attempts_total'),
                DB::raw('coalesce(answer_stats.answers_total, 0) as answers_total'),
                DB::raw('coalesce(answer_stats.correct_answers_total, 0) as correct_answers_total')
            )
            ->orderByDesc('attempts_total')
            ->orderBy('exercises.title')
            ->paginate(20)
            ->withQueryString();

        $answersTotal = (int) DB::table('attempt_answers')->count();
        $correctAnswersTotal = (int) DB::table('attempt_answers')->where('is_correct', true)->count();
        $accuracyRate = $answersTotal > 0 ? (int) round(($correctAnswersTotal / $answersTotal) * 100) : null;

        return view('pages.admin-exercises', [
            'exercises' => $exercises,
            'filters' => ['q' => $search],
            'stats' => [
                'published' => DB::table('exercises')->count(),
                'drafts' => 0,
                'attempts' => DB::table('exercise_attempts')->count(),
                'answers' => $answersTotal,
                'accuracy_rate' => $accuracyRate,
            ],
        ]);
    }
}