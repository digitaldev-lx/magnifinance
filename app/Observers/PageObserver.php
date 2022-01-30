<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Page;

class PageObserver
{

    public function created(Page $page)
    {
        SearchLog::createSearchEntry($page->slug, 'Page', $page->title, 'superadmin.pages.edit');

    }

    public function updating(Page $page)
    {
        if ($page->isDirty('slug') || $page->isDirty('title')) {
            SearchLog::updateSearchEntry($page->getRawOriginal('slug'), 'Page', $page->getRawOriginal('title'), 'superadmin.pages.edit', ['modified' => ['searchable_id' => $page->slug, 'title' => $page->title]]);
        }
    }

    public function deleted(Page $page)
    {
        SearchLog::deleteSearchEntry($page->slug, 'superadmin.pages.edit');
    }

}
