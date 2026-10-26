@extends('emails.layout')
@section('title', 'Disponibilidad en lista de espera — Mentea')
@section('content')
    <h1>Hay un hueco para ti</h1>
    <p>Hola <strong>{{ $patientName }}</strong>,</p>
    <p>Encontramos una disponibilidad según tu lista de espera. Tienes un tiempo limitado para confirmar.</p>
    <div class="meta">
        <strong>Fecha ofrecida:</strong> {{ $date ?? '—' }} · {{ $time ?? '—' }}<br>
        @if(!empty($professionalName))
            <strong>Profesional:</strong> {{ $professionalName }}
        @endif
    </div>
    <div class="btn-wrap">
        <a class="btn" href="{{ $url ?? config('app.url') }}">Confirmar en Mentea</a>
    </div>
@endsection
