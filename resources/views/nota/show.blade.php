<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi #{{ $transaction->id }} - PB. Ani Wijaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', sans-serif;
            background: linear-gradient(135deg, #f9f9f9 0%, #eeeeee 100%);
        }
        .font-heading {
            font-family: 'EB Garamond', serif;
        }
        .nota-paper {
            background: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="min-h-screen py-8 px-4">

    {{-- Action Bar (tidak ikut ter-screenshot) --}}
    <div class="no-print max-w-2xl mx-auto mb-6 flex flex-wrap gap-3 justify-between items-center">
        <a href="{{ url('/admin/stock-transactions/' . $transaction->id . '/edit') }}"
           class="inline-flex items-center gap-2 text-gray-700 hover:text-gray-900 font-semibold">
            ← Kembali ke Admin
        </a>
        <div class="flex gap-2">
            <button onclick="downloadAsImage()"
                    class="bg-[#ba0013] text-white px-6 py-2.5 rounded-lg font-bold hover:bg-[#93000d] transition-all shadow-md active:scale-95">
                📷 Download sebagai Foto
            </button>
            <button onclick="window.print()"
                    class="bg-gray-800 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-gray-900 transition-all shadow-md active:scale-95">
                🖨️ Cetak
            </button>
        </div>
    </div>

    {{-- Nota Card (yang akan jadi foto) --}}
    <div id="nota" class="nota-paper max-w-2xl mx-auto rounded-2xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-br from-[#ba0013] to-[#93000d] text-white p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>

            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h1 class="font-heading text-3xl font-bold mb-1">PB. Ani Wijaya</h1>
                    <p class="text-white/80 text-sm">Indramayu - Jawa Barat</p>
                    <p class="text-white/60 text-xs mt-1">NIB. 1412210030165</p>
                </div>
                <div class="text-right">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        @if($transaction->type === 'in')
                            📥 Barang Masuk
                        @else
                            📤 Barang Keluar
                        @endif
                    </div>
                    <p class="font-heading text-2xl font-bold">Nota</p>
                    <p class="text-white/80 text-sm">#{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-8 space-y-6">

            {{-- Info Transaksi --}}
            <div class="grid grid-cols-2 gap-4 pb-6 border-b border-gray-200">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">Tanggal Transaksi</p>
                    <p class="font-bold text-gray-900">{{ $transaction->transaction_date->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">No. Referensi</p>
                    <p class="font-bold text-gray-900">{{ $transaction->reference_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Pihak (Supplier atau Buyer) --}}
            <div>
                @if($transaction->type === 'in' && $transaction->supplier)
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Pemasok</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-bold text-lg text-gray-900">{{ $transaction->supplier->name }}</p>
                        @if($transaction->supplier->phone)
                            <p class="text-sm text-gray-600 mt-1">📞 {{ $transaction->supplier->phone }}</p>
                        @endif
                        @if($transaction->supplier->address)
                            <p class="text-sm text-gray-600">📍 {{ $transaction->supplier->address }}</p>
                        @endif
                    </div>
                @elseif($transaction->type === 'out' && $transaction->buyer)
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Pembeli</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-bold text-lg text-gray-900">{{ $transaction->buyer->name }}</p>
                        @if($transaction->buyer->phone)
                            <p class="text-sm text-gray-600 mt-1">📞 {{ $transaction->buyer->phone }}</p>
                        @endif
                        @if($transaction->buyer->address)
                            <p class="text-sm text-gray-600">📍 {{ $transaction->buyer->address }}</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Detail Produk --}}
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-3">Detail Produk</p>
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="text-left py-2 text-xs uppercase tracking-wider text-gray-600 font-bold">Produk</th>
                            <th class="text-center py-2 text-xs uppercase tracking-wider text-gray-600 font-bold">Jumlah</th>
                            <th class="text-right py-2 text-xs uppercase tracking-wider text-gray-600 font-bold">Harga/Kg</th>
                            <th class="text-right py-2 text-xs uppercase tracking-wider text-gray-600 font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-4">
                                <p class="font-bold text-gray-900">{{ $transaction->product->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($transaction->quantity_kg, 2) }} kg total</p>
                            </td>
                            <td class="py-4 text-center">
                                <p class="font-bold text-gray-900">{{ number_format($transaction->input_quantity, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->input_unit }}</p>
                            </td>
                            <td class="py-4 text-right text-gray-900">
                                @if($transaction->price_per_kg)
                                    Rp {{ number_format($transaction->price_per_kg, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-4 text-right font-bold text-gray-900">
                                @if($transaction->total_price)
                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            @if($transaction->total_price)
                <div class="bg-gradient-to-r from-[#ba0013] to-[#93000d] text-white rounded-xl p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-white/70 text-sm uppercase tracking-wider font-bold">Total</p>
                            <p class="text-xs text-white/60 mt-1">{{ number_format($transaction->quantity_kg, 2) }} kg × Rp {{ number_format($transaction->price_per_kg, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-heading text-3xl font-bold">
                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Catatan --}}
            @if($transaction->notes)
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-2">Catatan</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-gray-700 text-sm whitespace-pre-line">{{ $transaction->notes }}</p>
                    </div>
                </div>
            @endif

            {{-- Footer --}}
            <div class="pt-6 border-t border-gray-200">
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-xs text-gray-500">Diinput oleh</p>
                        <p class="font-bold text-gray-900">{{ $transaction->user->name ?? 'Sistem' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <div style="display: inline-block; background-color: #fef3c7; border: 2px solid #facc15; border-radius: 9999px; padding: 8px 16px; line-height: 1;">
                            <span style="font-size: 12px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">✓ Mutu Terjamin</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tagline --}}
            <div class="text-center pt-4">
                <p class="text-xs text-gray-400 italic">Terima kasih atas kepercayaan Anda — PB. Ani Wijaya</p>
            </div>
        </div>
    </div>

    <script>
        function downloadAsImage() {
    const nota = document.getElementById('nota');
    const button = event.target;
    const originalText = button.innerHTML;

    button.innerHTML = '⏳ Memproses...';
    button.disabled = true;

    // Tunggu font ter-load dulu (anti race condition)
    document.fonts.ready.then(() => {
        // Delay kecil untuk memastikan semua style ter-apply
        setTimeout(() => {
            html2canvas(nota, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                allowTaint: true,
                imageTimeout: 15000,
                onclone: function(clonedDoc) {
                    // Force font apply di cloned document
                    const clonedNota = clonedDoc.getElementById('nota');
                    if (clonedNota) {
                        clonedNota.style.fontFamily = 'Manrope, sans-serif';
                    }
                }
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Nota-PB-Ani-Wijaya-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                button.innerHTML = originalText;
                button.disabled = false;
            }).catch(err => {
                console.error('Error generating image:', err);
                alert('Gagal generate foto. Silakan coba lagi.');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }, 300);
    });
}
    </script>
</body>
</html>
