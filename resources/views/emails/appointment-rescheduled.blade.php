@extends('emails.layout')
@section('title', 'Cita reprogramada — Mentea')
@section('content')
    <h1>Cita reprogramada</h1>
    <p>Hola <strong>{{ $patientName }}</strong>,</p>
    <p>Tu cita cambió de horario. Revisa los nuevos datos en Mentea.</p>
    <div class="meta">
        <strong>Nueva fecha:</strong> {{ $date }} · {{ $time }}<br>
        <strong>Profesional:</strong> {{ $professionalName }}
    </div>
    <div class="btn-wrap">
        <a class="btn" href="{{ $url }}">Ver cita</a>
    </div>
@endsection
