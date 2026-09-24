@extends('print.layout')

@section('title', 'Laporan '.$title)
@section('width', '210mm')
@section('page', 'A4')

@section('doc')
    <h2>LAPORAN {{ strtoupper($title) }}</h2>
    <p>{{ $from->translatedFormat('d F Y') }} &ndash; {{ $to->translatedFormat('d F Y') }}</p>
    <p class="muted">Dicetak {{ now()->translatedFormat('d F Y H:i') }}</p>
@endsection

@section('content')
    @include('reports.sections', ['sections' => $sections])
@endsection
