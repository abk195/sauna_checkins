@extends('layouts.admin')

@section('title', 'Add manifest')

@section('heading', 'Add manifest')

@section('content')
    <div class="card" style="max-width: 520px;">
        <form method="post" action="{{ route('admin.manifests.store') }}">
            @csrf
            <div class="field">
                <label for="name">Display name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
            </div>
            <div class="field">
                <label for="manifest_id">Manifest ID (Periode)</label>
                <input id="manifest_id" type="text" name="manifest_id" value="{{ old('manifest_id') }}" required maxlength="255" autocomplete="off">
            </div>
            <div class="field">
                <label for="sauna_id">Sauna</label>
                <select id="sauna_id" name="sauna_id">
                    <option value="">— Unassigned —</option>
                    @foreach ($saunas as $s)
                        <option value="{{ $s->id }}" @selected((string) old('sauna_id') === (string) $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @if ($saunas->isEmpty())
                    <small style="color:var(--muted);font-size:0.8rem;">No saunas yet — <a href="{{ route('admin.saunas.create') }}">add one</a> first.</small>
                @endif
            </div>
            <button type="submit" class="btn">Save</button>
            <a href="{{ route('admin.manifests.index') }}" class="btn btn-ghost" style="margin-left:0.5rem;">Cancel</a>
        </form>
    </div>
@endsection
