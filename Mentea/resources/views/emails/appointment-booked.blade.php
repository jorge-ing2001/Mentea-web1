@extends('emails.layout')
@section('title', 'Cita confirmada — Mentea')
@section('content')
    <h1>Cita agendada</h1>
    <p>Hola <strong>{{ $patientName }}</strong>,</p>
    <p>Tu cita ha sido registrada. Entra a Mentea para ver el detalle.</p>
    <div class="meta">
        <strong>Profesional:</strong> {{ $professionalName }}<br>
        <strong>Fecha:</strong> {{ $date }} · {{ $time }}<br>
        <strong>Modalidad:</strong> {{ $modality }}
        @if(!empty($meetLink))
            <br><strong>Enlace:</strong> disponible en tu panel
        @endif
    </div>
    <div class="btn-wrap">
        <a class="btn" href="{{ $url }}">Ver mi cita en Mentea</a>
    </div>
    <p style="font-size:13px;">Si necesitas cancelar, hazlo desde la aplicación con la anticipación indicada.</p>
@endsection
