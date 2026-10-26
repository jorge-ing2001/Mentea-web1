<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('super-admin.dashboard', [
            'openTickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'auditToday' => AuditLog::whereDate('created_at', today())->count(),
            'settingsCount' => SystemSetting::count(),
            'usersCount' => User::count(),
            'recentTickets' => SupportTicket::with('user')->latest()->take(5)->get(),
            'recentAudit' => AuditLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
