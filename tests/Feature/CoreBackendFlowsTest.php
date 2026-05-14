<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoreBackendFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $appResponse = $this->get('/app.html');
        $adminResponse = $this->get('/admin.html');

        $appResponse->assertStatus(302);
        $adminResponse->assertStatus(302);

        $this->assertStringContainsString('/login.html', (string) $appResponse->headers->get('Location'));
        $this->assertStringContainsString('/login.html', (string) $adminResponse->headers->get('Location'));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin.html');

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $this->attachAdminRole($user);

        $response = $this->actingAs($user)->get('/admin.html');

        $response->assertOk();
        $response->assertSee('Admin');
        $response->assertSee('Analytics');
    }

    public function test_admin_billing_page_shows_real_plan_and_subscription_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'surname' => 'Lexi',
            'email' => 'admin-billing@test.local',
        ]);
        $this->attachAdminRole($user);

        $planId = DB::table('plans')->insertGetId([
            'code' => 'pro',
            'name' => 'Pro',
            'price_cents' => 990,
            'currency' => 'EUR',
            'billing_interval' => 'monthly',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('subscriptions')->insert([
            'user_id' => $user->id,
            'plan_id' => $planId,
            'status' => 'active',
            'provider' => 'manual',
            'quantity' => 1,
            'starts_at' => now(),
            'renews_at' => now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin-billing.html');

        $response->assertOk();
        $response->assertSee('Pro');
        $response->assertSee('admin-billing@test.local');
    }

    public function test_admin_ai_page_shows_real_generation_data(): void
    {
        $user = User::factory()->create([
            'email' => 'admin-ai@test.local',
        ]);
        $this->attachAdminRole($user);

        DB::table('ai_generations')->insert([
            'user_id' => $user->id,
            'feature' => 'exercise_builder',
            'model' => 'gpt-5.4-mini',
            'prompt' => 'Genera vocabulario sobre viajes.',
            'response' => 'Salida de ejemplo.',
            'source_language_code' => 'es',
            'target_language_code' => 'pt',
            'status' => 'approved',
            'estimated_cost_cents' => 15,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => 'Correcto.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin-ai.html');

        $response->assertOk();
        $response->assertSee('exercise_builder');
        $response->assertSee('gpt-5.4-mini');
    }

    public function test_exercise_attempt_endpoint_creates_attempt_and_reuses_mode_record(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $user = User::factory()->create();

        $payload = [
            'mode' => 'reading',
            'source_type' => 'catalog',
            'source_name' => 'Portugues A1',
            'result_status' => 'passed',
            'score' => 75,
            'time_spent_seconds' => 42,
            'item_count' => 4,
            'correct_count' => 3,
        ];

        $this->actingAs($user)->postJson('/api/exercise-attempts', $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->actingAs($user)->postJson('/api/exercise-attempts', $payload)
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('exercises', [
            'type' => 'reading',
            'title' => 'Reading',
            'source' => 'catalog',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('exercise_attempts', [
            'user_id' => $user->id,
            'result_status' => 'passed',
            'score' => 75,
            'time_spent_seconds' => 42,
        ]);

        $this->assertSame(1, DB::table('exercises')->count());
        $this->assertSame(2, DB::table('exercise_attempts')->count());
    }

    public function test_progress_state_returns_real_summary_for_active_language(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'es',
        ]);

        DB::table('user_languages')->insert([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'level_cefr' => 'A1',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Viajes',
            'language_code' => 'pt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $translatedWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-es',
            'text' => 'hola',
            'language_code' => 'es',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $firstWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-pt',
            'text' => 'ola',
            'language_code' => 'pt',
            'category_id' => $categoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $secondWordId = DB::table('words')->insertGetId([
            'client_key' => 'adeus-pt',
            'text' => 'adeus',
            'language_code' => 'pt',
            'category_id' => $categoryId,
            'cefr_level' => 'A2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('translations')->insert([
            'source_word_id' => $firstWordId,
            'target_word_id' => $translatedWordId,
            'context_note' => 'saludo',
            'created_at' => now(),
        ]);

        DB::table('user_words')->insert([
            [
                'user_id' => $user->id,
                'word_id' => $firstWordId,
                'status' => 'learned',
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ],
            [
                'user_id' => $user->id,
                'word_id' => $secondWordId,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('collections')->insert([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'name' => 'Basicos',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $exerciseId = DB::table('exercises')->insertGetId([
            'type' => 'reading',
            'title' => 'Reading',
            'source' => 'catalog',
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('exercise_attempts')->insert([
            'user_id' => $user->id,
            'exercise_id' => $exerciseId,
            'started_at' => now()->subMinutes(5),
            'completed_at' => now(),
            'result_status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/progress/state');

        $response->assertOk();
        $response->assertJsonPath('active_language.code', 'pt');
        $response->assertJsonPath('summary.saved_words_total', 2);
        $response->assertJsonPath('summary.saved_words_active', 2);
        $response->assertJsonPath('summary.collections_active', 1);
        $response->assertJsonPath('exercises.total_completed', 1);
        $response->assertJsonPath('exercises.modes.reading', 1);
        $response->assertJsonPath('summary.recent_words.0.label', 'adeus');
    }

    public function test_library_state_keeps_all_user_collections_even_when_items_are_filtered_by_language(): void
    {
        $this->seedLanguages();

        $user = User::factory()->create([
            'mother_tongue_code' => 'es',
        ]);

        $portugueseCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Viajes',
            'language_code' => 'pt',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $englishCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Travel',
            'language_code' => 'en',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $portugueseWordId = DB::table('words')->insertGetId([
            'client_key' => 'ola-pt',
            'text' => 'ola',
            'language_code' => 'pt',
            'category_id' => $portugueseCategoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $englishWordId = DB::table('words')->insertGetId([
            'client_key' => 'hello-en',
            'text' => 'hello',
            'language_code' => 'en',
            'category_id' => $englishCategoryId,
            'cefr_level' => 'A1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_words')->insert([
            [
                'user_id' => $user->id,
                'word_id' => $portugueseWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'word_id' => $englishWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $ptCollectionId = DB::table('collections')->insertGetId([
            'user_id' => $user->id,
            'language_code' => 'pt',
            'name' => 'Portugues',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $enCollectionId = DB::table('collections')->insertGetId([
            'user_id' => $user->id,
            'language_code' => 'en',
            'name' => 'English',
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('collection_words')->insert([
            [
                'collection_id' => $ptCollectionId,
                'word_id' => $portugueseWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'collection_id' => $enCollectionId,
                'word_id' => $englishWordId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->actingAs($user)->getJson('/api/library/state?language=pt');

        $response->assertOk();
        $response->assertJsonCount(1, 'items');
        $response->assertJsonCount(2, 'collections');
        $response->assertJsonPath('items.0.language', 'pt');
    }

    public function test_database_seeder_creates_admin_and_core_seed_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = DB::table('users')->where('email', 'admin@lexi.app')->first();

        $this->assertNotNull($admin);
        $this->assertTrue(
            DB::table('user_roles')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $admin->id)
                ->where('roles.name', 'admin')
                ->exists()
        );
        $this->assertSame(3, DB::table('plans')->count());
        $this->assertGreaterThanOrEqual(1, DB::table('subscriptions')->count());
        $this->assertGreaterThanOrEqual(2, DB::table('ai_generations')->count());
    }

    private function attachAdminRole(User $user): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'description' => 'Acceso total al panel.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedLanguages(): void
    {
        DB::table('languages')->insert([
            ['code' => 'es', 'name' => 'Español', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pt', 'name' => 'Portugués', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'en', 'name' => 'Inglés', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}