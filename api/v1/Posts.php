<?php

namespace Norotaro\BlogApi\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Norotaro\BlogApi\Classes\ApiException;
use Winter\Blog\Models\Category;
use Winter\Blog\Models\Post;

class Posts extends Controller
{
    public function index(Request $request)
    {
        $params = $scopeParams = array_filter([
            'page'             => (int) $request->input('page'),
            'perPage'          => (int) $request->input('perPage'),
            'sort'             => $request->input('sort'),
            'exceptCategories' => $request->input('exceptCategories'),
            'category'         => $request->input('category'),
            'search'           => $request->input('search'),
            'exceptPost'       => $request->input('exceptPost'),
        ]);

        if (!isset($params['sort'])) {
            $scopeParams['sort'] = 'published_at desc';
        }

        if (isset($params['category']) && $category = $this->loadCategory($params['category'])) {
            $scopeParams['category'] = $category->id;
        }

        if (isset($params['exceptCategories'])) {
            $scopeParams['exceptCategories'] = preg_split('/,\s*/', $params['exceptCategories'], -1, PREG_SPLIT_NO_EMPTY);
        }

        if (isset($params['exceptPost'])) {
            $scopeParams['exceptPost'] = preg_split('/,\s*/', $params['exceptPost'], -1, PREG_SPLIT_NO_EMPTY);
        }

        $posts = Post::with([
            'categories',
            'featured_images',
            'content_images',
        ])
            ->listFrontEnd($scopeParams)
            ->appends($params)
            ->toArray();

        $posts['category'] = $category ?? null;

        return response()->json($posts);
    }

    public function show(String $slug)
    {
        $post = $this->loadPost($slug);

        if (!$post) throw new ApiException(404, 'Not found');

        return response()->json($post);
    }

    protected function loadCategory($slug): Category|null
    {
        if (!$slug) {
            return null;
        }

        $category = new Category;

        $category = $category->isClassExtendedWith('Winter.Translate.Behaviors.TranslatableModel')
            ? $category->transWhere('slug', $slug)
            : $category->where('slug', $slug);

        $category = $category->first();

        return $category ?: null;
    }

    protected function loadPost(String $slug): Post|null
    {
        $post = new Post;
        $query = $post->query();

        if ($post->isClassExtendedWith('Winter.Translate.Behaviors.TranslatableModel')) {
            $query->transWhere('slug', $slug);
        } else {
            $query->where('slug', $slug);
        }

        return $query->with([
            'categories',
            'featured_images',
            'content_images',
        ])
            ->isPublished()
            ->first();
    }
}
