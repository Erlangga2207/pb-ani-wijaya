<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserService $users) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('roles')
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")))
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($q) => $q->where('is_active', $request->query('status') === 'active'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roleLabels' => UserRequest::roleLabels()]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', User::class);

        return $this->form($request, new User(['is_active' => true]));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $this->users->create($request->validated(), $request->user());

        return redirect()->route('admin.users.index')->with('sukses', 'Pengguna ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        Gate::authorize('update', $user);

        return $this->form($request, $user);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $this->users->update($user, $request->validated(), $request->user());

        return redirect()->route('admin.users.index')->with('sukses', 'Data pengguna diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        if (! Gate::allows('delete', $user)) {
            return back()->with('gagal', 'Pengguna ini tidak bisa dihapus (akun sendiri atau sudah pernah mencatat transaksi). Nonaktifkan saja supaya tidak bisa login.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('sukses', 'Pengguna dihapus.');
    }

    private function form(Request $request, User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => array_intersect_key(UserRequest::roleLabels(), array_flip($request->user()->assignableRoles())),
            'isSelf' => $user->is($request->user()),
        ]);
    }
}
