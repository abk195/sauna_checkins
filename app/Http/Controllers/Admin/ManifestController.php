<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManifestRequest;
use App\Http\Requests\Admin\UpdateManifestRequest;
use App\Models\Manifest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ManifestController extends Controller
{
    public function index(): View
    {
        $manifests = Manifest::query()->orderBy('name')->get();

        return view('admin.manifests.index', compact('manifests'));
    }

    public function create(): View
    {
        return view('admin.manifests.create');
    }

    public function store(StoreManifestRequest $request): RedirectResponse
    {
        Manifest::create($request->validated());

        return redirect()
            ->route('admin.manifests.index')
            ->with('status', 'Manifest created.');
    }

    public function edit(Manifest $manifest): View
    {
        return view('admin.manifests.edit', compact('manifest'));
    }

    public function update(UpdateManifestRequest $request, Manifest $manifest): RedirectResponse
    {
        $manifest->update($request->validated());

        return redirect()
            ->route('admin.manifests.index')
            ->with('status', 'Manifest updated.');
    }

    public function destroy(Manifest $manifest): RedirectResponse
    {
        $manifest->delete();

        return redirect()
            ->route('admin.manifests.index')
            ->with('status', 'Manifest deleted.');
    }
}
