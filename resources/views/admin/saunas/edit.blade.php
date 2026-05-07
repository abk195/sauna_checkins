@extends('layouts.admin')

@section('title', 'Edit sauna')

@section('heading', 'Edit sauna')

@section('content')
    <div class="card" style="max-width: 520px;">
        <form method="post" action="{{ route('admin.saunas.update', $sauna) }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="name">Display name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $sauna->name) }}" required maxlength="255">
            </div>
            <div class="field">
                <label for="slug">URL slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $sauna->slug) }}" required maxlength="255" autocomplete="off">
                <small style="color:var(--muted);font-size:0.8rem;">Changing the slug also changes the iPad URL.</small>
            </div>
            <button type="submit" class="btn">Update</button>
            <a href="{{ route('admin.saunas.index') }}" class="btn btn-ghost" style="margin-left:0.5rem;width:auto;">Cancel</a>
        </form>
    </div>
@endsection
