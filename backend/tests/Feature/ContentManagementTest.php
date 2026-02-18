<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Content;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $editor;
    protected User $citizen;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
        
        $this->editor = User::factory()->create();
        $this->editor->assignRole('editor');
        
        $this->citizen = User::factory()->create();
        $this->citizen->assignRole('ciudadano');
    }

    public function test_anyone_can_view_published_contents(): void
    {
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'content' => 'This is test content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
        ]);

        $response = $this->getJson('/api/v1/contents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'content', 'status'],
                ],
            ]);
    }

    public function test_can_view_content_by_slug(): void
    {
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'content' => 'This is test content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
        ]);

        $response = $this->getJson('/api/v1/contents/test-content');

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Test Content',
                'slug' => 'test-content',
            ]);
    }

    public function test_viewing_content_increments_views(): void
    {
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'content' => 'This is test content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
            'views' => 0,
        ]);

        $this->getJson('/api/v1/contents/test-content');

        $this->assertEquals(1, $content->fresh()->views);
    }

    public function test_editor_can_create_content(): void
    {
        $category = Category::create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        $response = $this->actingAs($this->editor, 'sanctum')
            ->postJson('/api/v1/contents', [
                'title' => 'New Content',
                'content' => 'Content body',
                'category_id' => $category->id,
                'status' => 'draft',
                'is_featured' => false,
                'allow_comments' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'title', 'slug', 'content', 'author',
            ]);

        $this->assertDatabaseHas('contents', [
            'title' => 'New Content',
            'slug' => 'new-content',
            'author_id' => $this->editor->id,
        ]);
    }

    public function test_citizen_cannot_create_content(): void
    {
        $response = $this->actingAs($this->citizen, 'sanctum')
            ->postJson('/api/v1/contents', [
                'title' => 'New Content',
                'content' => 'Content body',
                'status' => 'draft',
            ]);

        $response->assertStatus(403);
    }

    public function test_editor_can_update_content(): void
    {
        $content = Content::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'status' => 'draft',
            'author_id' => $this->editor->id,
        ]);

        $response = $this->actingAs($this->editor, 'sanctum')
            ->putJson("/api/v1/contents/{$content->id}", [
                'title' => 'Updated Title',
                'status' => 'published',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'slug' => 'updated-title',
            ]);
    }

    public function test_editor_can_delete_content(): void
    {
        $content = Content::create([
            'title' => 'To Delete',
            'slug' => 'to-delete',
            'content' => 'Will be deleted',
            'status' => 'draft',
            'author_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/contents/{$content->id}");

        $response->assertStatus(200);
        
        $this->assertSoftDeleted('contents', [
            'id' => $content->id,
        ]);
    }

    public function test_can_filter_contents_by_category(): void
    {
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        
        Content::create([
            'title' => 'Tech Content',
            'slug' => 'tech-content',
            'content' => 'Tech stuff',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
            'category_id' => $category->id,
        ]);

        Content::create([
            'title' => 'Other Content',
            'slug' => 'other-content',
            'content' => 'Other stuff',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
        ]);

        $response = $this->getJson("/api/v1/contents?category_id={$category->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Tech Content', $data[0]['title']);
    }

    public function test_can_filter_featured_contents(): void
    {
        Content::create([
            'title' => 'Featured',
            'slug' => 'featured',
            'content' => 'Featured content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
            'is_featured' => true,
        ]);

        Content::create([
            'title' => 'Not Featured',
            'slug' => 'not-featured',
            'content' => 'Regular content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->admin->id,
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/v1/contents?featured=1');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Featured', $data[0]['title']);
    }

    public function test_content_can_have_tags(): void
    {
        $tag1 = Tag::create(['name' => 'Important', 'slug' => 'important']);
        $tag2 = Tag::create(['name' => 'News', 'slug' => 'news']);

        $response = $this->actingAs($this->editor, 'sanctum')
            ->postJson('/api/v1/contents', [
                'title' => 'Tagged Content',
                'content' => 'Content with tags',
                'status' => 'draft',
                'tags' => [$tag1->id, $tag2->id],
            ]);

        $response->assertStatus(201);
        
        $content = Content::where('slug', 'tagged-content')->first();
        $this->assertCount(2, $content->tags);
    }
}
