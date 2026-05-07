@extends('layouts.admin')

@section('title', 'Add sauna')

@section('heading', 'Add sauna')

@section('content')
    <div class="card" style="max-width: 520px;">
        <form method="post" action="{{ route('admin.saunas.store') }}">
            @csrf
            <div class="field">
                <label for="name">Display name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
            </div>
            <div class="field">
                <label for="slug">URL slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug') }}" required maxlength="255" autocomplete="off" placeholder="sauna-a">
                <small style="color:var(--muted);font-size:0.8rem;">Lowercase letters, numbers, and dashes (used in iPad URL).</small>
            </div>
            <button type="submit" class="btn">Save</button>
            <a href="{{ route('admin.saunas.index') }}" class="btn btn-ghost" style="margin-left:0.5rem;width:auto;">Cancel</a>
        </form>
    </div>
@endsection
