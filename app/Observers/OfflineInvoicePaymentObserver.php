<?php

namespace App\Observers;

use App\Notifications\NewClientTask;
use App\Notifications\NewTask;
use App\Notifications\OfflineInvoicePaymentAccept;
use App\Notifications\OfflineInvoicePaymentReject;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\Notifications\TaskUpdatedClient;
use App\OfflineInvoicePayment;
use App\Scopes\CompanyScope;
use App\Task;
use App\TaskboardColumn;
use App\UniversalSearch;
use App\User;
use Illuminate\Support\Facades\Notification;
use Pusher\Pusher;

class OfflineInvoicePaymentObserver
{



}
