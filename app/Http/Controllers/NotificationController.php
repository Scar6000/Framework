<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use RendersView;

    public function index(Request $request)
    {
        $user = $request->user();

        return $this->render('notifications.index', [
            'notifications' => $user->notifications()->paginate(20),
            'non_lues' => $user->unreadNotifications()->count(),
        ]);
    }

    public function lire(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->markAsRead();

        return $this->done('notifications.index', [], 'Notification lue.');
    }

    public function toutLire(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->done('notifications.index', [], 'Toutes les notifications sont lues.');
    }
}
