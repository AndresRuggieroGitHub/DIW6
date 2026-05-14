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

        $templateItemCounts = DB::table('exercise_items')
            ->select('template_id', DB::raw('count(*) as items_total'))
            ->groupBy('template_id');

        $templateOptionCounts = DB::table('exercise_items')
            ->join('exercise_options', 'exercise_options.item_id', '=', 'exercise_items.id')
            ->select('exercise_items.template_id', DB::raw('count(exercise_options.id) as options_total'))
            ->groupBy('exercise_items.template_id');

        $templateInstanceCounts = DB::table('exercise_instances')
            ->select('template_id', DB::raw('count(*) as instances_total'))
            ->groupBy('template_id');

        $templates = DB::table('exercise_templates')
            ->leftJoin('users', 'users.id', '=', 'exercise_templates.created_by')
            ->leftJoinSub($templateItemCounts, 'template_item_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_item_counts.template_id'))
            ->leftJoinSub($templateOptionCounts, 'template_option_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_option_counts.template_id'))
            ->leftJoinSub($templateInstanceCounts, 'template_instance_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_instance_counts.template_id'))
            ->when($search !== '', fn ($query) => $query->where('exercise_templates.title', 'like', '%' . $search . '%'))
            ->select(
                'exercise_templates.id',
                'exercise_templates.title',
                'exercise_templates.type',
                'exercise_templates.source',
                'exercise_templates.schema_version',
                'users.name as author_name',
                'users.surname as author_surname',
                DB::raw('coalesce(template_item_counts.items_total, 0) as items_total'),
                DB::raw('coalesce(template_option_counts.options_total, 0) as options_total'),
                DB::raw('coalesce(template_instance_counts.instances_total, 0) as instances_total')
            )
            ->orderByDesc('instances_total')
            ->orderBy('exercise_templates.title')
            ->limit(12)
            ->get();

        $answersTotal = (int) DB::table('attempt_answers')->count();
        $correctAnswersTotal = (int) DB::table('attempt_answers')->where('is_correct', true)->count();
        $accuracyRate = $answersTotal > 0 ? (int) round(($correctAnswersTotal / $answersTotal) * 100) : null;

        return view('pages.admin-exercises', [
            'exercises' => $exercises,
            'templates' => $templates,
            'filters' => ['q' => $search],
            'stats' => [
                'published' => DB::table('exercises')->count(),
                'drafts' => 0,
                'attempts' => DB::table('exercise_attempts')->count(),
                'answers' => $answersTotal,
                'accuracy_rate' => $accuracyRate,
                'templates' => DB::table('exercise_templates')->count(),
                'template_items' => DB::table('exercise_items')->count(),
            ],
        ]);
    }
}