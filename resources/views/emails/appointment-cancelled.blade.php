@extends('emails.layout')
@section('title', 'Cita cancelada — Mentea')
@section('content')
    <h1>Cita cancelada</h1>
    <p>Hola <strong>{{ $patientName }}</strong>,</p>
    <p>Tu cita ha sido cancelada. Puedes agendar una nueva desde la aplicación.</p>
    <div class="meta">
        <strong>Fecha original:</strong> {{ $date ?? '—' }} · {{ $time ?? '—' }}
    </div>
    <div class="btn-wrap">
        <a class="btn" href="{{ $url ?? config('app.url') }}">Ir a Mentea</a>
    </div>
@endsection
