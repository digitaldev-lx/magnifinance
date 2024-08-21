<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Notifications\NewCompany;
use App\User;
use Illuminate\Support\Facades\File;

class UserObserver
{

    public function created(User $user)
    {
        // Send verification email to first admin of company
        if (!is_null($user->company_id)) {
            if ($user->company->verified == 'no') {
                $user->notify(new NewCompany());
            }
        }

        if ($user->company_id) {
            $type = 'Employee';
            $route = 'admin.employee.edit';
            SearchLog::createSearchEntry($user->id, $type, $user->name, $route, $user->company_id);
            SearchLog::createSearchEntry($user->id, $type, $user->email, $route, $user->company_id);
        }

        !(auth()->check() && auth()->user()->is_superadmin) ? $this->universalCustomer($user) : '';

    }

    public function universalCustomer($customer)
    {
        if (!$customer->company_id){
            $type = 'Customer';
            $route = 'admin.customers.show';

            SearchLog::createSearchEntry($customer->id, $type, $customer->name, $route);
            SearchLog::createSearchEntry($customer->id, $type, $customer->email, $route);
        }
    }

    public function updating(User $user)
    {
        if ($user->company_id) {
            $type = 'Employee';
            $route = 'admin.employee.edit';

            if ($user->isDirty('name')) {
                $original = $user->getRawOriginal('name');
                SearchLog::updateSearchEntry($user->id, $type, $user->name, $route, ['name' => $original]);
            }

            if ($user->isDirty('email')) {
                $original = $user->getRawOriginal('email');
                SearchLog::updateSearchEntry($user->id, $type, $user->email, $route, ['email' => $original]);
            }
        }
    }

    public function deleted(User $user)
    {

        if (!is_null($user->getRawOriginal('image'))) {
            $path = public_path('user-uploads/avatar/' . $user->getRawOriginal('image'));

            if ($path) {
                File::delete($path);
            }
        }

        if ($user->company_id) {
            SearchLog::deleteSearchEntry($user->id, 'admin.employee.edit');
        }
    }

}
