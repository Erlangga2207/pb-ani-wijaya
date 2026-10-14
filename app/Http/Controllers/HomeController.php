<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
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
        // Satu route POST untuk semua bahasa: pesan validasi & redirect mengikuti bahasa halaman asal.
        $locale = array_key_exists((string) $request->input('locale'), config('locales'))
            ? $request->input('locale')
            : array_key_first(config('locales'));
        app()->setLocale($locale);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [], [
            'name' => __('Nama Lengkap'),
            'email' => __('Email'),
            'subject' => __('Subjek'),
            'message' => __('Pesan'),
        ]);

        // Honeypot: field "website" tersembunyi, hanya bot yang mengisinya.
        // Pura-pura sukses, simpan sebagai spam supaya tidak masuk daftar lead baru.
        $isSpam = filled($request->input('website'));

        $contactMessage = ContactMessage::create($validated + [
            'ip_address' => $request->ip(),
            'status' => $isSpam ? LeadStatus::Spam : LeadStatus::New,
        ]);

        // Dikirim langsung (bukan queue) karena hosting tidak menjalankan queue worker.
        // Gagal kirim tidak boleh menggagalkan form: pesan sudah tersimpan di panel.
        if (! $isSpam) {
            try {
                Mail::to(config('mail.contact_to'))->send(new NewContactMessage($contactMessage));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect(localized_route('contact'))
            ->with('success', __('Pesan Anda berhasil dikirim! Tim kami akan menghubungi Anda segera.'));
    }

    /**
     * Sitemap: semua halaman publik × semua bahasa, dengan tautan alternatif hreflang.
     */
    public function sitemap(): Response
    {
        return response()
            ->view('sitemap', ['pages' => ['home', 'about', 'products', 'gallery', 'contact']])
            ->header('Content-Type', 'application/xml');
    }
}
