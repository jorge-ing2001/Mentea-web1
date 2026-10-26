<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Diligenciamiento de eventos de la plataforma por el administrador de clínica.
 */
class PlatformEventController extends Controller
{
    public function index(): View
    {
        $events = SupportTicket::with('user')
            ->where('type', SupportTicket::TYPE_PLATFORM_EVENT)
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create', [
            'categories' => SupportTicket::categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'event_date' => ['nullable', 'date'],
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'type' => SupportTicket::TYPE_PLATFORM_EVENT,
            'category' => $data['category'],
            'event_date' => $data['event_date'] ?? now(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'],
            'status' => 'resolved',
            'assigned_to' => Auth::id(),
            'resolved_at' => now(),
            'admin_notes' => 'Diligenciamiento admin: ' . Auth::user()->name,
        ]);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Evento diligenciado y registrado en la plataforma.');
    }
}
