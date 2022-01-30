<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Category;
use App\Services\ImagesManager;

class CategoryObserver
{

    public function created(Category $category)
    {
        SearchLog::createSearchEntry($category->id, 'Category', $category->name, 'superadmin.categories.edit');

    }

    public function updating(Category $category)
    {
        SearchLog::updateSearchEntry($category->id, 'Category', $category->name, 'superadmin.categories.edit');
    }

    public function deleted(Category $category)
    {
        $image = new ImagesManager();
        $image->deleteImage($category->image, 'category');

        SearchLog::deleteSearchEntry($category->id, 'superadmin.categories.edit');
    }

}
