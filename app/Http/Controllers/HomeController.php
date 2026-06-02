<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Halaman Beranda (Homepage)
     */
    public function beranda(): View
    {
        $featuredGalleries = Gallery::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        $featuredProduct = Product::where('is_active', true)->first();

        return view('public.beranda', compact('featuredGalleries', 'featuredProduct'));
    }

    /**
     * Halaman Tentang Kami
     */
    public function tentangKami(): View
    {
        return view('public.tentang-kami');
    }

    /**
     * Halaman Produk
     */
    public function produk(): View
    {
        $products = Product::where('is_active', true)->get();
        return view('public.produk', compact('products'));
    }

    /**
     * Halaman Galeri
     */
    public function galeri(): View
{
    $galleries = Gallery::where('is_active', true)
        ->orderBy('order')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('public.galeri', compact('galleries'));
}

    /**
     * Halaman Kontak
     */
    public function kontak(): View
    {
        return view('public.kontak');
    }

    /**
     * Submit form kontak
     */
    public function submitKontak(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('contact')
            ->with('success', 'Pesan Anda berhasil dikirim! Tim kami akan menghubungi Anda segera.');
    }
}
