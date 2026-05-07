@extends('layouts.admin')

@section('title', 'Saunas')

@section('heading', 'Saunas')

@section('content')
    <p style="color: var(--muted); margin: 0 0 1rem; font-size: 0.95rem;">
        Each sauna has its own static iPad URL. Assign manifests to saunas under <a href="{{ route('admin.manifests.index') }}">Manifests</a>; the iPad URL automatically rotates to the active manifest's bookings (switching 20&nbsp;minutes before the next session).
    </p>

    <p style="margin-bottom: 1rem;">
        <a href="{{ route('admin.saunas.create') }}" class="btn">Add sauna</a>
    </p>

    <div class="card" style="overflow-x: auto;">
        @if ($saunas->isEmpty())
            <p style="margin:0;color:var(--muted);">No saunas yet. Add one to start mapping manifests.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Manifests</th>
                        <th>iPad URL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saunas as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td><code style="font-size:0.85em;">{{ $s->slug }}</code></td>
                            <td>{{ $s->manifests_count }}</td>
                            <td>
                                <code style="font-size:0.75em;word-break:break-all;line-height:1.4;display:block;max-width:28rem;">{{ route('checkin.sauna', ['sauna' => $s->slug]) }}</code>
                            </td>
                            <td class="actions">
                                <a href="{{ route('admin.saunas.edit', $s) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('admin.saunas.destroy', $s) }}" method="post" onsubmit="return confirm('Delete this sauna? Manifests assigned to it will become unassigned.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
