<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSaunaRequest;
use App\Http\Requests\Admin\UpdateSaunaRequest;
use App\Models\Sauna;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaunaController extends Controller
{
    public function index(): View
    {
        $saunas = Sauna::query()
            ->withCount('manifests')
            ->orderBy('name')
            ->get();

        return view('admin.saunas.index', compact('saunas'));
    }

    public function create(): View
    {
        return view('admin.saunas.create');
    }

    public function store(StoreSaunaRequest $request): RedirectResponse
    {
        Sauna::create($request->validated());

        return redirect()
            ->route('admin.saunas.index')
            ->with('status', 'Sauna created.');
    }

    public function edit(Sauna $sauna): View
    {
        return view('admin.saunas.edit', compact('sauna'));
    }

    public function update(UpdateSaunaRequest $request, Sauna $sauna): RedirectResponse
    {
        $sauna->update($request->validated());

        return redirect()
            ->route('admin.saunas.index')
            ->with('status', 'Sauna updated.');
    }

    public function destroy(Sauna $sauna): RedirectResponse
    {
        $sauna->delete();

        return redirect()
            ->route('admin.saunas.index')
            ->with('status', 'Sauna deleted.');
    }
}
