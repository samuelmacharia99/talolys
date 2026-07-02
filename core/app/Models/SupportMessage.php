<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model {
    use BelongsToTenant;

    public function ticket() {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id', 'id');
    }

    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function attachments() {
        return $this->hasMany(SupportAttachment::class, 'support_message_id', 'id');
    }
}
