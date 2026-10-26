<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(Request $request): View
    {
        $surveys = Survey::with(['patient', 'appointment.professional'])
            ->latest()
            ->paginate(20);

        $avg = Survey::avg('rating');
        $count = Survey::count();
        $pending = Appointment::query()
            ->where('status', AppointmentStatus::Completed->value)
            ->whereDoesntHave('survey')
            ->whereDate('date', '>=', now()->subDays(30))
            ->count();

        return view('admin.surveys.index', compact('surveys', 'avg', 'count', 'pending'));
    }
}
