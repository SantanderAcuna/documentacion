<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    /**
     * Display a listing of published contents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Content::with(['author', 'category', 'tags'])
            ->published();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', (bool) $request->featured);
        }

        // Search
        if ($request->has('search')) {
            $query->whereFullText(['title', 'content'], $request->search);
        }

        $contents = $query->orderBy('published_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($contents);
    }

    /**
     * Store a newly created content.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|array',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $content = new Content($validated);
        $content->slug = Str::slug($validated['title']);
        $content->author_id = $request->user()->id;
        $content->save();

        // Attach tags if provided
        if (isset($validated['tags'])) {
            $content->tags()->sync($validated['tags']);
        }

        return response()->json($content->load(['author', 'category', 'tags']), 201);
    }

    /**
     * Display the specified content.
     */
    public function show(string $slug): JsonResponse
    {
        $content = Content::with(['author', 'category', 'tags', 'media'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views for published content
        if ($content->status === 'published') {
            $content->incrementViews();
        }

        return response()->json($content);
    }

    /**
     * Update the specified content.
     */
    public function update(Request $request, Content $content): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|string',
            'category_id' => 'nullable|exists:categories,id',
            'featured_image' => 'nullable|string',
            'status' => 'sometimes|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|array',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $content->update($validated);

        // Update tags if provided
        if (isset($validated['tags'])) {
            $content->tags()->sync($validated['tags']);
        }

        return response()->json($content->load(['author', 'category', 'tags']));
    }

    /**
     * Remove the specified content.
     */
    public function destroy(Content $content): JsonResponse
    {
        $content->delete();

        return response()->json([
            'message' => 'Contenido eliminado exitosamente',
        ]);
    }
}
