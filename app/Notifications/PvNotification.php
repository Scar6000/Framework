<?php

namespace App\Notifications;

use App\Models\Pv;
use Illuminate\Notifications\Notification;

class PvNotification extends Notification
{
    public function __construct(
        public Pv $pv,
        public string $type,      // soumis | valide | rejete
        public string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pv_id' => $this->pv->id,
            'reference' => $this->pv->reference,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}
