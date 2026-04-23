@extends('layouts.admin')

@section('title', 'Manifests')

@section('heading', 'Manifest IDs')

@section('content')
    <p style="color: var(--muted); margin: 0 0 1rem; font-size: 0.95rem;">
        Sync uses these manifests from the database. Edit the list here; the scheduled <code>bookings:sync</code> command picks up changes automatically.
    </p>

    <p style="margin-bottom: 1rem;">
        <a href="{{ route('admin.manifests.create') }}" class="btn">Add manifest</a>
    </p>

    <div class="card" style="overflow-x: auto;">
        @if ($manifests->isEmpty())
            <p style="margin:0;color:var(--muted);">No manifests yet. Add one to enable booking sync.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Manifest ID</th>
                        <th>Kiosk URL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($manifests as $m)
                        <tr>
                            <td>{{ $m->name }}</td>
                            <td><code style="font-size:0.85em;">{{ $m->manifest_id }}</code></td>
                            <td>
                                <code style="font-size:0.75em;word-break:break-all;line-height:1.4;display:block;max-width:28rem;">{{ route('checkin.manifest', ['manifest_id' => $m->manifest_id]) }}</code>
                            </td>
                            <td class="actions">
                                <a href="{{ route('admin.manifests.edit', $m) }}" class="btn" style="font-size:0.85rem;padding:0.35rem 0.75rem;">Edit</a>
                                <form action="{{ route('admin.manifests.destroy', $m) }}" method="post" onsubmit="return confirm('Delete this manifest?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="font-size:0.85rem;padding:0.35rem 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
