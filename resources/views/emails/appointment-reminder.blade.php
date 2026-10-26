@extends('emails.layout')
@section('title', 'Recordatorio de cita — Mentea')
@section('content')
    <h1>Recordatorio de cita</h1>
    <p>Hola <strong>{{ $patientName }}</strong>,</p>
    <p>Te recordamos que tienes una cita próxima.</p>
    <div class="meta">
        <strong>Profesional:</strong> {{ $professionalName }}<br>
        <strong>Fecha:</strong> {{ $date }} · {{ $time }}<br>
        <strong>Modalidad:</strong> {{ $modality }}
    </div>
    <div class="btn-wrap">
        <a class="btn" href="{{ $url }}">Abrir en Mentea</a>
    </div>
@endsection
