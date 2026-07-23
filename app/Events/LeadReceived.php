<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Core chỉ bắn event. Gửi mail / bắn webhook / đẩy CRM là việc của dự án —
 * mỗi site một kiểu, không nhét vào core.
 */
class LeadReceived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Model $lead) {}
}
