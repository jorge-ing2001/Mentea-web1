<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'assignee'])->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        return view('super-admin.tickets.index', [
            'tickets' => $query->paginate(20)->withQueryString(),
            'status' => $status ?? '',
            'type' => $type ?? '',
        ]);
    }

    public function create(): View
    {
        return view('super-admin.tickets.create', [
            'categories' => SupportTicket::categories(),
        ]);
    }

    /**
     * Diligenciamiento: el admin/super registra un evento sobre la plataforma.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'event_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:open,in_progress,resolved,closed'],
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'type' => SupportTicket::TYPE_PLATFORM_EVENT,
            'category' => $data['category'],
            'event_date' => $data['event_date'] ?? now(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'],
            'status' => $data['status'] ?? 'resolved',
            'assigned_to' => Auth::id(),
            'resolved_at' => in_array($data['status'] ?? 'resolved', ['resolved', 'closed'], true) ? now() : null,
            'admin_notes' => 'Registrado como diligenciamiento de plataforma por ' . Auth::user()->name,
        ]);

        return redirect()
            ->route('super.tickets.index', ['type' => 'platform_event'])
            ->with('success', 'Evento / diligenciamiento registrado en la plataforma.');
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['user', 'assignee']);

        return view('super-admin.tickets.show', compact('ticket'));
    }

    public function update(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (in_array($data['status'], ['resolved', 'closed'], true) && ! $ticket->resolved_at) {
            $data['resolved_at'] = now();
        }
        if ($data['status'] === 'open') {
            $data['resolved_at'] = null;
        }

        $data['assigned_to'] = $request->user()->id;
        $ticket->update($data);

        return back()->with('success', 'Ticket actualizado.');
    }
}
