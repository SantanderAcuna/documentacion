<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_content_has_author_relationship(): void
    {
        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test',
            'content' => 'Content',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(User::class, $content->author);
        $this->assertEquals($this->user->id, $content->author->id);
    }

    public function test_content_has_category_relationship(): void
    {
        $category = Category::create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test',
            'content' => 'Content',
            'status' => 'draft',
            'author_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $content->category);
        $this->assertEquals($category->id, $content->category->id);
    }

    public function test_published_scope_only_returns_published_content(): void
    {
        Content::create([
            'title' => 'Published',
            'slug' => 'published',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_id' => $this->user->id,
        ]);

        Content::create([
            'title' => 'Draft',
            'slug' => 'draft',
            'content' => 'Content',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $published = Content::published()->get();
        
        $this->assertCount(1, $published);
        $this->assertEquals('published', $published->first()->status);
    }

    public function test_featured_scope_only_returns_featured_content(): void
    {
        Content::create([
            'title' => 'Featured',
            'slug' => 'featured',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->user->id,
            'is_featured' => true,
        ]);

        Content::create([
            'title' => 'Not Featured',
            'slug' => 'not-featured',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->user->id,
            'is_featured' => false,
        ]);

        $featured = Content::featured()->get();
        
        $this->assertCount(1, $featured);
        $this->assertTrue($featured->first()->is_featured);
    }

    public function test_increment_views_increases_view_count(): void
    {
        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $this->user->id,
            'views' => 0,
        ]);

        $this->assertEquals(0, $content->views);

        $content->incrementViews();

        $this->assertEquals(1, $content->fresh()->views);
    }

    public function test_content_is_soft_deleted(): void
    {
        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test',
            'content' => 'Content',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $content->delete();

        $this->assertSoftDeleted('contents', [
            'id' => $content->id,
        ]);

        $this->assertCount(0, Content::all());
        $this->assertCount(1, Content::withTrashed()->get());
    }

    public function test_meta_keywords_are_cast_to_array(): void
    {
        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test',
            'content' => 'Content',
            'status' => 'draft',
            'author_id' => $this->user->id,
            'meta_keywords' => ['seo', 'test', 'keywords'],
        ]);

        $this->assertIsArray($content->meta_keywords);
        $this->assertCount(3, $content->meta_keywords);
    }
}
