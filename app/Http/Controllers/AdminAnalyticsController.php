<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $userWords = DB::table('user_words');
        $attempts = DB::table('exercise_attempts');

        $userWordsCount = (clone $userWords)->count();
        $attemptsCount = (clone $attempts)->count();
        $completedAttempts = (clone $attempts)->whereNotNull('completed_at')->count();
        $reviewDue = (clone $userWords)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', now())
            ->count();
        $wordsLearned = (clone $userWords)->where('status', 'learned')->count();

        $completionRate = $attemptsCount > 0
            ? (int) round(($completedAttempts / $attemptsCount) * 100)
            : 0;

        $recentActivity = DB::table('exercise_attempts')
            ->leftJoin('users', 'exercise_attempts.user_id', '=', 'users.id')
            ->leftJoin('exercises', 'exercise_attempts.exercise_id', '=', 'exercises.id')
            ->select(
                'exercise_attempts.id',
                'users.name as user_name',
                'exercises.title as exercise_title',
                'exercise_attempts.result_status',
                'exercise_attempts.completed_at',
                'exercise_attempts.started_at',
                'exercise_attempts.score'
            )
            ->orderByDesc('exercise_attempts.created_at')
            ->limit(10)
            ->get();

        $overview = [
            [
                'metric' => 'Words learned',
                'value' => number_format($wordsLearned),
                'window' => 'Acumulado',
                'status' => $wordsLearned > 0 ? 'En objetivo' : 'Sin señal',
                'statusClass' => $wordsLearned > 0 ? 'admin-status--active' : 'admin-status--review',
            ],
            [
                'metric' => 'Exercise attempts',
                'value' => number_format($attemptsCount),
                'window' => 'Acumulado',
                'status' => $attemptsCount > 0 ? 'Estable' : 'Sin actividad',
                'statusClass' => $attemptsCount > 0 ? 'admin-status--active' : 'admin-status--review',
            ],
            [
                'metric' => 'Due reviews',
                'value' => number_format($reviewDue),
                'window' => 'Ahora',
                'status' => $reviewDue > 0 ? 'Pendiente' : 'Al día',
                'statusClass' => $reviewDue > 0 ? 'admin-status--review' : 'admin-status--active',
            ],
        ];

        return view('pages.admin-analytics', [
            'stats' => [
                'records' => $userWordsCount + $attemptsCount,
                'completion_rate' => $completionRate,
                'review_due' => $reviewDue,
            ],
            'overview' => $overview,
            'recentActivity' => $recentActivity,
        ]);
    }
}