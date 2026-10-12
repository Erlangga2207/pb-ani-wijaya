{{-- Form filter GET: kotak cari + filter tambahan di slot. Nilai ikut query string (paginate()->withQueryString()). --}}
@props(['placeholder' => 'Cari...'])

<form method="GET" class="d-flex flex-wrap gap-2 mb-3" role="search">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" aria-label="Cari"
           class="form-control" style="max-width:260px">
    {{ $slot }}
    <button type="submit" class="btn btn-garis">Cari</button>
    @if (array_filter(request()->except('page')))
        <a href="{{ url()->current() }}" class="btn btn-polos">Reset</a>
    @endif
</form>
