<?php

namespace Modules\Blog\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Modules\Auth\Traits\UserPermissions;
use Modules\Blog\Models\Category;
use Modules\Clean\Traits\InteractsWithBanner;

class CategoryController extends Controller
{
    use InteractsWithBanner, UserPermissions;


    /**
     * Manage categories page
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(): Application|Factory|View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::whereNull('category_id')
            ->with(['categories'])
            ->orderBy('name', 'ASC')
            ->get();

        // the default is the first category
        $selectedCategory = $categories->first();

        $parentCategories = [];
        $parentCategoryId = $selectedCategory->category_id ?? null;

        while ($parentCategoryId !== null) {
            $currentCategory = Category::where('id', $parentCategoryId)->firstOrFail();
            $parentCategories[$currentCategory->id] = $currentCategory->name;
            $parentCategoryId = $currentCategory->category_id ?? null;
        }

        return view('blog::admin.category.manage')->with([
            'categories' => $categories,
            'parentCategories' => array_reverse($parentCategories, true),
            'userPermissions' => $this->getUserPermissions()
        ]);
    }

}
