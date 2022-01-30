<?php

namespace App\Observers;

use App\TodoItem;

class TodoItemObserver
{

    public function creating(TodoItem $todoItem)
    {
        if (company()) {
            $todoItem->company_id = company()->id;
        }
    }

}
