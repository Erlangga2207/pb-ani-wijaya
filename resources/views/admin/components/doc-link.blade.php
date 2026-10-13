{{-- Nomor dokumen sumber mutasi stok, ditautkan ke halaman detailnya bila user boleh melihat. --}}
@props(['movement'])

@php
    $routes = [
        App\Models\Purchase::class => 'admin.purchases.show',
        App\Models\Production::class => 'admin.productions.show',
        App\Models\Sale::class => 'admin.sales.show',
        App\Models\StockAdjustment::class => 'admin.stock-adjustments.show',
    ];
    $source = $movement->source;
    $route = $routes[$movement->source_type] ?? null;
@endphp

@if ($source && $route && auth()->user()->can('view', $source))
    <a href="{{ route($route, $source) }}">{{ $source->number }}</a>
@else
    {{ $source?->number ?? class_basename($movement->source_type) }}
@endif
