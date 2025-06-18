<?php

use App\Jobs\CancelExpiredReservations;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CancelExpiredReservations)->everyHour();
