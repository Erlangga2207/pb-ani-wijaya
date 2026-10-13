<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\Gallery;
use App\Services\WebsiteContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Website › Galeri. Owner ke atas (GalleryPolicy).
class GalleryController extends Controller
{
    public function __construct(private WebsiteContentService $content) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Gallery::class);

        $galleries = Gallery::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->when(array_key_exists((string) $request->query('category'), Gallery::CATEGORIES), fn ($q) => $q->where('category', $request->query('category')))
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($q) => $q->where('is_active', $request->query('status') === 'active'))
            ->when($request->boolean('featured'), fn ($q) => $q->where('is_featured', true))
            ->orderBy('order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        Gate::authorize('create', Gallery::class);

        return view('admin.galleries.form', ['gallery' => new Gallery(['category' => 'other', 'order' => 0, 'is_active' => true])]);
    }

    public function store(GalleryRequest $request): RedirectResponse
    {
        Gate::authorize('create', Gallery::class);

        $this->content->createGallery($request->galleryData(), $request->file('file'));

        return redirect()->route('admin.galleries.index')->with('sukses', 'Foto/video ditambahkan ke galeri.');
    }

    public function edit(Gallery $gallery): View
    {
        Gate::authorize('update', $gallery);

        return view('admin.galleries.form', compact('gallery'));
    }

    public function update(GalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        Gate::authorize('update', $gallery);

        $this->content->updateGallery($gallery, $request->galleryData(), $request->file('file'));

        return redirect()->route('admin.galleries.index')->with('sukses', 'Galeri diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        Gate::authorize('delete', $gallery);

        $this->content->deleteGallery($gallery);

        return redirect()->route('admin.galleries.index')->with('sukses', 'Item galeri dihapus.');
    }
}
