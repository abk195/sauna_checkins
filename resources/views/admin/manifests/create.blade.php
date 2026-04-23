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
            <button type="submit" class="btn">Save</button>
            <a href="{{ route('admin.manifests.index') }}" class="btn btn-ghost" style="margin-left:0.5rem;">Cancel</a>
        </form>
    </div>
@endsection
