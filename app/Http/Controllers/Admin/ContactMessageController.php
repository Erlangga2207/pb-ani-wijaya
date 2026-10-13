<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Website › Pesan & Calon Pembeli (lead dari form kontak). Owner ke atas (ContactMessagePolicy).
class ContactMessageController extends Controller
{
    public function __construct(private LeadService $leads) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ContactMessage::class);

        $messages = ContactMessage::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")->orWhere('subject', 'like', "%{$term}%")))
            ->when(LeadStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        Gate::authorize('view', $contactMessage);

        $contactMessage->markAsRead();
        $contactMessage->load(['buyer:id,name', 'handledBy:id,name']);

        return view('admin.contact-messages.show', ['message' => $contactMessage]);
    }

    public function update(ContactMessageRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        Gate::authorize('update', $contactMessage);

        $this->leads->updateFollowUp($contactMessage, $request->validated(), $request->user());

        return back()->with('sukses', 'Tindak lanjut disimpan.');
    }

    public function convert(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        Gate::authorize('update', $contactMessage);
        abort_if($contactMessage->status === LeadStatus::Spam, 422, 'Pesan spam tidak bisa dijadikan pembeli.');

        $buyer = $this->leads->convertToBuyer($contactMessage, $request->user());

        return back()->with('sukses', "Pembeli \"{$buyer->name}\" dibuat dan ditautkan ke pesan ini.");
    }

    public function spam(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        Gate::authorize('update', $contactMessage);

        $this->leads->markAsSpam($contactMessage, $request->user());

        return back()->with('sukses', 'Pesan ditandai spam.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        Gate::authorize('delete', $contactMessage);

        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('sukses', 'Pesan dihapus.');
    }
}
