<style>
    #icon-rocket-i {
        transform: rotate(40deg);
        display: inline-block;
    }

    #billing-i {
        font-size: 25px;
    }
</style>


<!-- Sidebar Menu -->
<nav class="mt-4">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
        id="sidebarnav">

        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->

        <li class="nav-item">
            @if ($user->is_superadmin_employee)
                <a href="{{ route('superadmin.dashboard') }}"
                   class="nav-link {{ request()->is('super-admin/dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon icon-dashboard"></i>
                    <p>@lang('menu.dashboard')</p>
                </a>
            @else
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->is('account/dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon icon-dashboard"></i>
                    <p>@lang('menu.dashboard')</p>
                </a>
            @endif
        </li>

        @if ($user->is_superadmin_employee)
            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_company','create_company', 'update_company', 'delete_company']))
                <li class="nav-item">
                    <a href="{{ route('superadmin.companies.index') }}"
                       class="nav-link {{ request()->is('super-admin/companies*') ? 'active' : '' }}">
                        <i class="nav-icon icon-home"></i>
                        <p>
                            @lang('menu.companies')
                        </p>
                    </a>
                </li>
            @endif
            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_package','create_package', 'update_package', 'delete_package']))
                <li class="nav-item">
                    <a href="{{ route('superadmin.packages.index') }}"
                       class="nav-link {{ request()->is('super-admin/package*') ? 'active' : '' }}">
                        <i class="fa fa-dropbox fa-2x"></i>

                        <p>
                            @lang('menu.packages')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_company'))
                <li class="nav-item">
                    <a href="{{ route('superadmin.invoices.index') }}"
                       class="nav-link {{ request()->is('super-admin/invoices*') ? 'active' : '' }}">
                        <i class="nav-icon icon-printer"></i>
                        <p>@lang('menu.invoices')</p>
                    </a>
                </li>
            @endif
            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_company'))
                <li class="nav-item">
                    <a href="{{ route('superadmin.offline-plan.index') }}"
                       class="nav-link {{ request()->is('super-admin/offline-plan*') ? 'active' : '' }}">
                        <i class="fa fa-money fa-2x"></i>

                        <p>
                            @lang('app.offlineRequest')
                        </p>
                    </a>
                </li>
            @endif
            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_location','create_location', 'update_location', 'delete_location']))
                <li class="nav-item">
                    <a href="{{ route('superadmin.locations.index') }}"
                       class="nav-link {{ request()->is('super-admin/locations*') ? 'active' : '' }}">
                        <i class="nav-icon icon-map-alt"></i>
                        <p>
                            @lang('menu.locations')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_category','create_category', 'update_category', 'delete_category']))
                <li class="nav-item">
                    <a href="{{ route('superadmin.categories.index') }}"
                       class="nav-link {{ request()->is('super-admin/categories*') ? 'active' : '' }}">
                        <i class="nav-icon icon-list"></i>
                        <p>
                            @lang('menu.categories')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_coupon','create_coupon', 'update_coupon', 'delete_coupon']))
                <li class="nav-item">
                    <a href="{{ route('superadmin.coupons.index') }}"
                       class="nav-link {{ request()->is('super-admin/coupons*') ? 'active' : '' }}">
                        <i class="nav-icon icon-gift"></i>
                        <p>
                            @lang('menu.coupons')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->hasRole('superadmin'))
                <li class="nav-item">
                    <a href="{{ route('superadmin.spotlight-deal.index') }}"
                       class="nav-link {{ request()->is('super-admin/spotlight-deal*') ? 'active' : '' }}">
                        <i class="fa fa-flash fa-lg mb-1"></i>
                        <p>
                            @lang('menu.spotlight')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->hasRole('superadmin'))
                <li class="nav-item">
                    <a href="{{ route('superadmin.ratings.index') }}"
                       class="nav-link {{ request()->is('super-admin/ratings*') ? 'active' : '' }}">
                        <i class="fa fa-star fa-lg mb-1"></i>
                        <p>
                            @lang('menu.ratings')
                        </p>
                    </a>
                </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('superadmin.todo-items.index') }}"
                   class="nav-link {{ request()->is('super-admin/todo-items*') ? 'active' : '' }}">
                    <i class="nav-icon icon-notepad"></i>
                    <p>
                        @lang('menu.todoList')
                    </p>
                </a>
            </li>

            @if ($user->hasRole('superadmin'))
                <li class="nav-item">
                    <a href="{{ route('superadmin.reports.index') }}"
                       class="nav-link {{ request()->is('super-admin/reports*') ? 'active' : '' }}">
                        <i class="nav-icon icon-pie-chart"></i>
                        <p>
                            @lang('menu.reports')
                        </p>
                    </a>
                </li>
            @endif
        @else

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_business_service'))
                <li class="nav-item">
                    <a href="{{ route('admin.business-services.index') }}"
                       class="nav-link {{ request()->is('account/business-services*') ? 'active' : '' }}">
                        <i class="nav-icon icon-list"></i>
                        <p>
                            @lang('menu.services')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_business_service'))
                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}"
                       class="nav-link {{ request()->is('account/products*') ? 'active' : '' }}">
                        <i class="nav-icon icon-shopping-cart-full"></i>
                        <p>
                            @lang('menu.products')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'))
                <li class="nav-item">
                    <a href="{{ route('admin.customers.index') }}"
                       class="nav-link {{ request()->is('account/customers*') ? 'active' : '' }}">
                        <i class="nav-icon fa fa-user-o"></i>
                        <p>
                            @lang('menu.customers')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee'))
                <li class="nav-item">
                    <a href="{{ route('admin.employee.index') }}"
                       class="nav-link {{ request()->is('account/employee*') ? 'active' : '' }}">
                        <i class="nav-icon icon-user"></i>
                        <p>
                            @lang('menu.employee')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_deal'))
                <li class="nav-item">
                    <a href="{{ route('admin.deals.index') }}"
                       class="nav-link {{ request()->is('account/deals*') ? 'active' : '' }}">
                        <i class="nav-icon icon-tag"></i>
                        <p>
                            @lang('menu.deals')
                        </p>
                    </a>
                </li>
            @endif


            @if(in_array('POS',$user->modules))
                @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'))
                    <li class="nav-item">
                        <a href="{{ route('admin.pos.create') }}"
                           class="nav-link {{ request()->is('account/pos*') ? 'active' : '' }}">
                            <i class="nav-icon icon-shopping-cart"></i>
                            <p>
                                @lang('menu.pos')
                            </p>
                        </a>
                    </li>
                @endif
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') || $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'))
                <li class="nav-item">
                    <a href="{{ route('admin.bookings.index') }}"
                       class="nav-link {{ request()->is('account/bookings*') ? 'active' : '' }}">
                        <i class="nav-icon icon-bookmark-alt"></i>
                        <p>
                            @lang('menu.bookings')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') || $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'))
                <li class="nav-item">
                    <a href="{{ route('admin.calendar') }}"
                       class="nav-link {{ request()->is('account/calendar*') ? 'active' : '' }}">
                        <i class="nav-icon icon-calendar"></i>
                        <p>
                            @lang('menu.bookings')<br/>
                            @lang('menu.calendar')
                        </p>
                    </a>
                </li>
            @endif

            @if ($user->is_admin || $user->is_employee)
                <li class="nav-item">
                    <a href="{{ route('admin.todo-items.index') }}"
                       class="nav-link {{ request()->is('account/todo-items*') ? 'active' : '' }}">
                        <i class="nav-icon icon-notepad"></i>
                        <p>
                            @lang('menu.todoList')
                        </p>
                    </a>
                </li>
            @endif

            @if(in_array('Employee Leave',$user->modules) && $user->is_employee)
                @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee_leave'))
                    <li class="nav-item">
                        <a href="{{ route('admin.employeeLeaves') }}"
                           class="nav-link {{ request()->is('account/employeeLeaves*') ? 'active' : '' }}">
                            <i class="nav-icon icon-rocket" id="icon-rocket-i"></i>
                            <p>@lang('menu.leaves')</p>
                        </a>
                    </li>
                @endif
            @endif

            @if(in_array('Reports',$user->modules))
                @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'))
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.index') }}"
                           class="nav-link {{ request()->is('account/reports*') ? 'active' : '' }}">
                            <i class="nav-icon icon-pie-chart"></i>
                            <p>
                                @lang('menu.reports')
                            </p>
                        </a>
                    </li>
                @endif
            @endif

        @endif

        @if ($user->is_admin)
            <li class="nav-item">
                <a href="{{ route('admin.billing.index') }}"
                   class="nav-link {{ request()->is('account/billing*') ? 'active' : '' }}">
                    <i class="nav-icon icon-credit-card" id="billing-i" aria-hidden="true"></i>
                    <p> @lang('menu.billing') </p>
                </a>
            </li>
        @endif

        @if ($user->hasPermission(['manage_article','read_article','create_article', 'update_article', 'delete_article']))
            <li class="nav-item">
                @if($user->is_admin)
                    <a href="{{ route('admin.articles.index') }}"
                       class="nav-link {{ request()->is('account/articles/*') ? 'active' : '' }}">
                        @elseif($user->is_superadmin)
                            <a href="{{ route('superadmin.articles.index') }}"
                               class="nav-link {{ request()->is('super-admin/articles/*') ? 'active' : '' }}">
                                @endif
                                <i class="fa fa-newspaper-o fa-2x"></i>

                                <p>
                                    @lang('menu.articles')
                                </p>
                            </a>
            </li>
        @endif
        @if(auth()->user()->hasPermission(["manage_tout", "read_tout", "create_tout", "update_tout"]))
        <li class="nav-item">
            @if($user->is_admin)
                <a href="{{ route('admin.toutes.index') }}"
                   class="nav-link {{ request()->is('account/toutes/*') ? 'active' : '' }}">
            @elseif($user->is_superadmin)
                <a href="{{ route('superadmin.toutes.index') }}"
                   class="nav-link {{ request()->is('super-admin/toutes/*') ? 'active' : '' }}">
            @endif
                    <i class="fa fa-bullhorn fa-2x"></i>

                    <p>
                        {{__('menu.toutes')}}
                    </p>
                </a>
        </li>
        @endif
        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission(['read_ticket','create_ticket', 'update_ticket', 'delete_ticket']))
            <li class="nav-item">
                <a href="{{ $user->is_superadmin_employee ? route('superadmin.tickets.index') : route('admin.tickets.index') }}"
                   class="nav-link {{ (request()->is('super-admin/ticket*') || request()->is('account/ticket*')) ? 'active' : '' }}">
                    <i class="nav-icon fa fa-life-ring"></i>
                    <p>
                        @lang('app.support')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'))
            <li class="nav-item">
                @if ($user->is_superadmin_employee)
                    <a href="{{ route('superadmin.settings.index') }}"
                       class="nav-link {{ request()->is('super-admin/settings*') ? 'active' : '' }} {{ request()->is('super-admin/front-settings*') ? 'active' : '' }}">
                        <i class="nav-icon icon-settings"></i>
                        <p>
                            @lang('menu.settings')
                        </p>
                    </a>
                @else
                    <a href="{{ route('admin.settings.index') }}"
                       class="nav-link {{ request()->is('account/settings*') ? 'active' : '' }}">
                        <i class="nav-icon icon-settings"></i>
                        <p>
                            @lang('menu.settings')
                        </p>
                    </a>
                @endif
            </li>
        @endif

    </ul>
</nav>
<!-- /.sidebar-menu -->
