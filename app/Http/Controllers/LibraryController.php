<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Translation;
use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LibraryController extends Controller
{
    public function state(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['nullable', Rule::exists('languages', 'code')],
        ]);

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language'] ?? null),
        ]);
    }

    public function storeWord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_key' => ['required', 'string', 'max:120'],
            'label' => ['required', 'string', 'max:255'],
            'language' => ['required', Rule::exists('languages', 'code')],
            'translation' => ['nullable', 'string', 'max:255'],
            'cefr' => ['nullable', 'string', 'max:2'],
            'topic' => ['nullable', 'string', 'max:80'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $word = $this->upsertWord(
                $validated['client_key'],
                $validated['label'],
                $validated['language'],
                $validated['cefr'] ?? null,
                $validated['topic'] ?? null
            );

            $this->upsertTranslation($request->user(), $word, $validated['translation'] ?? null);

            UserWord::query()->updateOrCreate(
                ['user_id' => $request->user()->id, 'word_id' => $word->id],
                ['last_seen_at' => now()]
            );
        });

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language']),
        ]);
    }

    public function destroyWord(Request $request, string $clientKey): JsonResponse
    {
        $wordId = Word::query()->where('client_key', $clientKey)->value('id');

        if ($wordId) {
            UserWord::query()
                ->where('user_id', $request->user()->id)
                ->where('word_id', $wordId)
                ->delete();
        }

        return response()->json([
            'items' => $this->libraryItems($request->user(), $request->query('language')),
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', Rule::exists('languages', 'code')],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.client_key' => ['required', 'string', 'max:120'],
            'entries.*.label' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            foreach ($validated['entries'] as $entry) {
                $word = $this->upsertWord($entry['client_key'], $entry['label'], $validated['language']);

                UserWord::query()->updateOrCreate(
                    ['user_id' => $request->user()->id, 'word_id' => $word->id],
                    ['last_seen_at' => now()]
                );
            }
        });

        return response()->json([
            'items' => $this->libraryItems($request->user(), $validated['language']),
        ]);
    }

    private function libraryItems(User $user, ?string $language = null): array
    {
        $query = UserWord::query()
            ->with(['word.category', 'word.translations.targetWord'])
            ->where('user_id', $user->id)
            ->latest();

        if ($language) {
            $query->whereHas('word', function ($wordQuery) use ($language) {
                $wordQuery->where('language_code', $language);
            });
        }

        return $query->get()->map(function (UserWord $userWord) {
            $word = $userWord->word;

            return [
                'id' => $word->client_key,
                'label' => $word->text,
                'language' => $word->language_code,
                'translation' => $word->translations->first()?->targetWord?->text,
                'cefr' => $word->cefr_level,
                'topic' => $word->category?->name,
            ];
        })->values()->all();
    }

    private function upsertWord(
        string $clientKey,
        string $label,
        string $language,
        ?string $cefr = null,
        ?string $topic = null
    ): Word {
        $word = Word::query()
            ->where('client_key', $clientKey)
            ->orWhere(function ($query) use ($label, $language) {
                $query->where('text', $label)->where('language_code', $language);
            })
            ->first();

        $categoryId = null;
        if ($topic) {
            $categoryId = Category::query()->updateOrCreate(
                ['name' => $topic, 'language_code' => $language],
                ['description' => null]
            )->id;
        }

        if ($word) {
            $word->fill(array_filter([
                'client_key' => $word->client_key ?: $clientKey,
                'cefr_level' => $word->cefr_level ?: $cefr,
                'category_id' => $word->category_id ?: $categoryId,
            ], fn ($value) => $value !== null));
            $word->save();

            return $word;
        }

        return Word::query()->create([
            'client_key' => $clientKey,
            'text' => $label,
            'language_code' => $language,
            'category_id' => $categoryId,
            'cefr_level' => $cefr,
        ]);
    }

    private function upsertTranslation(User $user, Word $sourceWord, ?string $translation): void
    {
        $translation = trim((string) $translation);
        if ($translation === '') {
            return;
        }

        $targetLanguage = $user->mother_tongue_code ?: 'es';
        $targetWord = Word::query()->firstOrCreate(
            ['text' => $translation, 'language_code' => $targetLanguage],
            ['client_key' => 'translation-' . substr(md5($targetLanguage . ':' . $translation), 0, 20)]
        );

        Translation::query()->updateOrCreate(
            ['source_word_id' => $sourceWord->id, 'target_word_id' => $targetWord->id],
            ['context_note' => null, 'created_at' => now()]
        );
    }
}