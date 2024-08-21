<?php

namespace App\Observers;

use App\Models\TodoItem;

class TodoItemObserver
{

    public function creating(TodoItem $todoItem)
    {
        if (company()) {
            $todoItem->company_id = company()->id;
        }
    }

}
