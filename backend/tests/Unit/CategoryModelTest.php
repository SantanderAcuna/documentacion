<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_have_parent(): void
    {
        $parent = Category::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);

        $child = Category::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $this->assertInstanceOf(Category::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_category_can_have_children(): void
    {
        $parent = Category::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);

        $child1 = Category::create([
            'name' => 'Child 1',
            'slug' => 'child-1',
            'parent_id' => $parent->id,
        ]);

        $child2 = Category::create([
            'name' => 'Child 2',
            'slug' => 'child-2',
            'parent_id' => $parent->id,
        ]);

        $this->assertCount(2, $parent->children);
    }

    public function test_active_scope_only_returns_active_categories(): void
    {
        Category::create([
            'name' => 'Active',
            'slug' => 'active',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Inactive',
            'slug' => 'inactive',
            'is_active' => false,
        ]);

        $active = Category::active()->get();
        
        $this->assertCount(1, $active);
        $this->assertTrue($active->first()->is_active);
    }

    public function test_root_scope_only_returns_root_categories(): void
    {
        $parent = Category::create([
            'name' => 'Parent',
            'slug' => 'parent',
        ]);

        Category::create([
            'name' => 'Child',
            'slug' => 'child',
            'parent_id' => $parent->id,
        ]);

        $root = Category::root()->get();
        
        $this->assertCount(1, $root);
        $this->assertNull($root->first()->parent_id);
    }

    public function test_category_has_contents_relationship(): void
    {
        $user = User::factory()->create();
        
        $category = Category::create([
            'name' => 'News',
            'slug' => 'news',
        ]);

        Content::create([
            'title' => 'Article 1',
            'slug' => 'article-1',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        Content::create([
            'title' => 'Article 2',
            'slug' => 'article-2',
            'content' => 'Content',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(2, $category->contents);
    }

    public function test_category_is_soft_deleted(): void
    {
        $category = Category::create([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        $category->delete();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);

        $this->assertCount(0, Category::all());
        $this->assertCount(1, Category::withTrashed()->get());
    }
}
