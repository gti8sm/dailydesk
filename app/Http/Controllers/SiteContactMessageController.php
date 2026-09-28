<?php

namespace App\Http\Controllers;

use App\Models\PublicSiteContactMessage;
use Illuminate\Http\Request;

class SiteContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = PublicSiteContactMessage::latestFirst();

        if ($request->get('filter') === 'unread') {
            $query->unread();
        }

        $messages = $query->paginate(15)->withQueryString();
        $unreadCount = PublicSiteContactMessage::unread()->count();

        return view('site.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(PublicSiteContactMessage $message)
    {
        $this->authorizeAccess();

        $message->markAsRead();

        return view('site.messages.show', compact('message'));
    }

    public function destroy(PublicSiteContactMessage $message)
    {
        $this->authorizeAccess();

        $message->delete();

        return redirect()->route('site.messages.index')
            ->with('success', 'Message supprimé.');
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('manage_public_site')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
