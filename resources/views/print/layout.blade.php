<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ $setting('company_name', 'PB. Ani Wijaya') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1a1c1c; background: #eee; margin: 0; padding: 24px 12px; font-size: 13px; }
        .paper { position: relative; background: #fff; max-width: @yield('width', '148mm'); margin: 0 auto; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,.15); overflow: hidden; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; border-bottom: 3px solid #ba0013; padding-bottom: 12px; margin-bottom: 16px; }
        .head img { height: 48px; }
        .brand { display: flex; gap: 12px; align-items: center; }
        .brand h1 { margin: 0; font-size: 18px; color: #ba0013; }
        .brand p, .doc p { margin: 2px 0; font-size: 11px; color: #555; white-space: pre-line; }
        .doc { text-align: right; }
        .doc h2 { margin: 0 0 4px; font-size: 16px; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 4px; text-align: left; vertical-align: top; }
        table.lines th { border-bottom: 1px solid #1a1c1c; font-size: 11px; text-transform: uppercase; }
        table.lines td { border-bottom: 1px solid #ddd; }
        .r { text-align: right; }
        .meta td { padding: 2px 4px; }
        .meta td:first-child { color: #555; width: 35%; }
        .totals { margin-top: 12px; margin-left: auto; width: 60%; }
        .totals td { padding: 3px 4px; }
        .totals tr.grand td { font-weight: bold; font-size: 15px; border-top: 2px solid #1a1c1c; }
        .muted { color: #666; font-size: 11px; }
        .sign { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; font-size: 12px; }
        .sign div { width: 40%; }
        .sign .line { margin-top: 56px; border-top: 1px solid #1a1c1c; }
        .void-mark { position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-25deg); font-size: 64px; font-weight: bold; color: rgba(186, 0, 19, .18); border: 6px solid rgba(186, 0, 19, .18); padding: 4px 24px; pointer-events: none; }
        .toolbar { max-width: @yield('width', '148mm'); margin: 0 auto 12px; display: flex; gap: 8px; justify-content: flex-end; }
        .btn { background: #ba0013; color: #fff; border: 0; padding: 8px 14px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 13px; }
        .btn.gray { background: #444; }
        .btn.green { background: #1e9e4a; }
        @media print {
            @page { size: @yield('page', 'A5'); margin: 8mm; }
            body { background: #fff; padding: 0; }
            .paper { box-shadow: none; max-width: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        @yield('toolbar')
        <button class="btn gray" onclick="window.print()">Cetak</button>
    </div>

    <div class="paper">
        @yield('watermark')
        <div class="head">
            <div class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="">
                <div>
                    <h1>{{ $setting('company_name', 'PB. Ani Wijaya') }}</h1>
                    <p>{{ $setting('contact_address') }}</p>
                    <p>Telp. {{ $setting('contact_phone') }} &middot; NIB {{ $setting('company_nib') }}</p>
                </div>
            </div>
            <div class="doc">
                @yield('doc')
            </div>
        </div>

        @yield('content')
    </div>
</body>
</html>
