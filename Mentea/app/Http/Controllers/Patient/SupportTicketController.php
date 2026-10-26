<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(): View
    {
        $tickets = SupportTicket::where('user_id', Auth::id())->latest()->paginate(10);

        return view('patient.tickets.index', compact('tickets'));
    }

    public function create(): View
    {
        return view('patient.tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'type' => SupportTicket::TYPE_SUPPORT,
            'category' => 'consulta',
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
        ]);

        return redirect()->route('patient.tickets.index')
            ->with('success', 'Tu solicitud de soporte fue enviada. Te responderemos pronto.');
    }
}
