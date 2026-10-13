{{-- Filter rentang tanggal untuk form GET (?from=&until=). --}}
<input type="date" name="from" value="{{ request('from') }}" class="form-control" style="max-width:170px" aria-label="Dari tanggal" title="Dari tanggal">
<input type="date" name="until" value="{{ request('until') }}" class="form-control" style="max-width:170px" aria-label="Sampai tanggal" title="Sampai tanggal">
