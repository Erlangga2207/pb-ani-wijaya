<?php

// Satu-satunya sumber menu panel /panel: sidebar desktop dan menu HP merender dari sini.
// role: null = semua role, 'owner' = owner & super_admin. Item yang route-nya belum ada otomatis disembunyikan.
// ikon: path SVG 24x24 (stroke). bawah: grup dipisah garis di bagian bawah sidebar.

return [
    [
        'judul' => null,
        'item' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'ikon' => 'M3 3h7v9H3zM14 3h7v5h-7zM14 12h7v9h-7zM3 16h7v5H3z'],
        ],
    ],
    [
        'judul' => 'Transaksi',
        'item' => [
            ['label' => 'Pembelian Gabah', 'route' => 'admin.purchases.index', 'ikon' => 'M4 7h16l-1.5 12h-13zM9 7V5a3 3 0 0 1 6 0v2'],
            ['label' => 'Produksi / Giling', 'route' => 'admin.productions.index', 'ikon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1'],
            ['label' => 'Penjualan', 'route' => 'admin.sales.index', 'ikon' => 'M3 4h2l2 12h11l2-8H6M9 20h.01M17 20h.01'],
        ],
    ],
    [
        'judul' => 'Stok',
        'item' => [
            ['label' => 'Kartu Stok', 'route' => 'admin.stock-movements.index', 'ikon' => 'M7 4v16M3 16l4 4 4-4M17 20V4M13 8l4-4 4 4'],
            ['label' => 'Stock Opname', 'route' => 'admin.stock-adjustments.index', 'ikon' => 'M9 3h6v4H9zM9 5H5v16h14V5h-4M9 13l2 2 4-4', 'role' => 'owner'],
        ],
    ],
    [
        'judul' => 'Keuangan',
        'item' => [
            ['label' => 'Piutang Pembeli', 'route' => 'admin.receivables.index', 'ikon' => 'M12 3v12M7 10l5 5 5-5M4 21h16', 'role' => 'owner'],
            ['label' => 'Utang ke Petani', 'route' => 'admin.payables.index', 'ikon' => 'M12 15V3M7 8l5-5 5 5M4 21h16', 'role' => 'owner'],
            ['label' => 'Biaya Operasional', 'route' => 'admin.expenses.index', 'ikon' => 'M3 6h18v12H3zM3 10h18M7 15h3', 'role' => 'owner'],
            ['label' => 'Laporan', 'route' => 'admin.reports.index', 'ikon' => 'M4 20V10M10 20V4M16 20v-7M22 20H2'],
        ],
    ],
    [
        'judul' => 'Master Data',
        'item' => [
            ['label' => 'Barang', 'route' => 'admin.items.index', 'ikon' => 'M4 7l8-4 8 4-8 4zM4 12l8 4 8-4M4 17l8 4 8-4'],
            ['label' => 'Petani / Pemasok', 'route' => 'admin.suppliers.index', 'ikon' => 'M12 21V11M12 11c0-4 3-7 7-7 0 4-3 7-7 7zM12 14c0-3-2.5-5.5-6-5.5 0 3 2.5 5.5 6 5.5z'],
            ['label' => 'Pembeli', 'route' => 'admin.buyers.index', 'ikon' => 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-4-5.7'],
        ],
    ],
    [
        'judul' => 'Website',
        'item' => [
            ['label' => 'Galeri', 'route' => 'admin.galleries.index', 'ikon' => 'M3 5h18v14H3zM3 16l5-5 4 4 3-3 6 6M15.5 9.5h.01', 'role' => 'owner'],
            ['label' => 'Katalog Produk', 'route' => 'admin.products.index', 'ikon' => 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z', 'role' => 'owner'],
            ['label' => 'Pengaturan Website', 'route' => 'admin.site-settings.edit', 'ikon' => 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18', 'role' => 'owner'],
            ['label' => 'Pesan & Calon Pembeli', 'route' => 'admin.contact-messages.index', 'ikon' => 'M4 4h16v12H8l-4 4z', 'role' => 'owner'],
        ],
    ],
    [
        'judul' => null,
        'bawah' => true,
        'item' => [
            ['label' => 'Pengguna', 'route' => 'admin.users.index', 'ikon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0', 'role' => 'owner'],
        ],
    ],
];
