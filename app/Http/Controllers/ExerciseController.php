<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        ]);

        $exerciseId = DB::table('exercises')
            ->where('type', $validated['mode'])
            ->where('title', $this->titleForMode($validated['mode']))
            ->value('id');

        if (! $exerciseId) {
            $exerciseId = DB::table('exercises')->insertGetId([
                'type' => $validated['mode'],
                'title' => $this->titleForMode($validated['mode']),
                'payload' => json_encode([
                    'source_type' => $validated['source_type'] ?? null,
                    'source_name' => $validated['source_name'] ?? null,
                ], JSON_UNESCAPED_UNICODE),
                'source' => $validated['source_type'] ?? 'manual',
                'created_by' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('exercise_attempts')->insert([
            'user_id' => $request->user()->id,
            'exercise_id' => $exerciseId,
            'started_at' => now(),
            'completed_at' => now(),
            'result_status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
}