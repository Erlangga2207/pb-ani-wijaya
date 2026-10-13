<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Services\WebsiteContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Website › Katalog Produk. Owner ke atas (ProductPolicy).
// Catatan: nama & deskripsi produk saat ini hanya tampil di "Produk Unggulan" beranda (keputusan Fase 0 no. 2).
class ProductController extends Controller
{
    public function __construct(private WebsiteContentService $content) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $products = Product::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%")))
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        Gate::authorize('create', Product::class);

        return view('admin.products.form', ['product' => new Product(['weight_per_karung' => 25, 'is_active' => true])]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        $this->content->saveProduct(new Product, $request->productData());

        return redirect()->route('admin.products.index')->with('sukses', 'Produk ditambahkan.');
    }

    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('admin.products.form', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        Gate::authorize('update', $product);

        $this->content->saveProduct($product, $request->productData());

        return redirect()->route('admin.products.index')->with('sukses', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('admin.products.index')->with('sukses', 'Produk dihapus.');
    }
}
