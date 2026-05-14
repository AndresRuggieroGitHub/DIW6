<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    public function storeAttempt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['reading', 'listening', 'speaking', 'writing', 'mix'])],
            'source_type' => ['nullable', Rule::in(['catalog', 'saved'])],
            'source_name' => ['nullable', 'string', 'max:150'],
            'result_status' => ['nullable', Rule::in(['completed', 'passed', 'failed'])],
            'score' => ['nullable', 'numeric', 'between:0,100'],
            'time_spent_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'item_count' => ['nullable', 'integer', 'min:0', 'max:200'],
            'correct_count' => ['nullable', 'integer', 'min:0', 'max:200'],
            'answers' => ['nullable', 'array', 'max:200'],
            'answers.*.item_id' => ['nullable', 'integer', 'exists:exercise_items,id'],
            'answers.*.item_type' => ['nullable', 'string', 'max:40'],
            'answers.*.prompt' => ['nullable', 'string', 'max:2000'],
            'answers.*.expected_answer' => ['nullable', 'string', 'max:4000'],
            'answers.*.answer_text' => ['nullable', 'string', 'max:4000'],
            'answers.*.answer_payload' => ['nullable', 'array'],
            'answers.*.is_correct' => ['nullable', 'boolean'],
            'answers.*.points_obtained' => ['nullable', 'numeric', 'between:0,100'],
            'answers.*.feedback' => ['nullable', 'string', 'max:4000'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $exerciseId = $this->resolveExerciseId($request, $validated);

            $timeSpent = $validated['time_spent_seconds'] ?? null;
            $startedAt = $timeSpent !== null ? now()->copy()->subSeconds($timeSpent) : now();
            $now = now();

            $attemptId = DB::table('exercise_attempts')->insertGetId([
                'user_id' => $request->user()->id,
                'exercise_id' => $exerciseId,
                'started_at' => $startedAt,
                'completed_at' => $now,
                'score' => $validated['score'] ?? null,
                'result_status' => $validated['result_status'] ?? 'completed',
                'time_spent_seconds' => $timeSpent,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $answerRows = collect($validated['answers'] ?? [])
                ->map(function (array $answer) use ($attemptId, $now) {
                    $payload = array_filter([
                        'item_type' => $answer['item_type'] ?? null,
                        'prompt' => $answer['prompt'] ?? null,
                        'expected_answer' => $answer['expected_answer'] ?? null,
                        'client_payload' => $answer['answer_payload'] ?? null,
                    ], fn ($value) => $value !== null && $value !== '');

                    return [
                        'attempt_id' => $attemptId,
                        'item_id' => $answer['item_id'] ?? null,
                        'answer_text' => $answer['answer_text'] ?? null,
                        'answer_payload' => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                        'is_correct' => $answer['is_correct'] ?? null,
                        'points_obtained' => $answer['points_obtained'] ?? null,
                        'feedback' => $answer['feedback'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->all();

            if ($answerRows) {
                DB::table('attempt_answers')->insert($answerRows);
            }
        });

        return response()->json([
            'ok' => true,
        ]);
    }

    private function titleForMode(string $mode): string
    {
        return match ($mode) {
            'reading' => 'Reading',
            'listening' => 'Listening',
            'speaking' => 'Speaking',
            'writing' => 'Writing',
            default => 'Combinado',
        };
    }

    private function resolveExerciseId(Request $request, array $validated): int
    {
        $title = $this->titleForMode($validated['mode']);

        $existingId = DB::table('exercises')
            ->where('type', $validated['mode'])
            ->where('title', $title)
            ->value('id');

        if ($existingId) {
            return (int) $existingId;
        }

        try {
            return (int) DB::table('exercises')->insertGetId([
                'type' => $validated['mode'],
                'title' => $title,
                'payload' => json_encode([
                    'source_type' => $validated['source_type'] ?? null,
                    'source_name' => $validated['source_name'] ?? null,
                    'item_count' => $validated['item_count'] ?? null,
                    'correct_count' => $validated['correct_count'] ?? null,
                ], JSON_UNESCAPED_UNICODE),
                'source' => $validated['source_type'] ?? 'manual',
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            $duplicateEntryDetected = str_contains(strtolower($exception->getMessage()), 'duplicate')
                || str_contains((string) $exception->getCode(), '23000');

            if (! $duplicateEntryDetected) {
                throw $exception;
            }

            return (int) DB::table('exercises')
                ->where('type', $validated['mode'])
                ->where('title', $title)
                ->value('id');
        }
    }
}