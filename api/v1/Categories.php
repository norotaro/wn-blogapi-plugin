<?php

namespace Norotaro\BlogApi\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Winter\Blog\Models\Category;
use Winter\Storm\Database\Collection;

class Categories extends Controller
{
    public function index(Request $request)
    {
        $displayEmpty = $request->input('displayEmpty', false);

        $categories = $this->loadCategories($displayEmpty);

        return response()->json($categories);
    }

    /**
     * Load all categories or, depending on the <displayEmpty> option, only those that have blog posts
     * @return mixed
     */
    protected function loadCategories(Bool $displayEmpty): Collection
    {
        $categories = Category::with('posts_count')->getNested();
        if (!$displayEmpty) {
            $iterator = function ($categories) use (&$iterator) {
                return $categories->reject(function ($category) use (&$iterator) {
                    if ($category->getNestedPostCount() == 0) {
                        return true;
                    }
                    if ($category->children) {
                        $category->children = $iterator($category->children);
                    }
                    return false;
                });
            };
            $categories = $iterator($categories);
        }

        return $categories->values();
    }
}
