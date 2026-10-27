<h1>Notifikasi</h1>
@forelse ($notifications as $n)
    <p>{{ $n->data['message'] ?? '' }} {{ $n->read_at ? '' : '(baru)' }}</p>
@empty
    <p>Belum ada notifikasi.</p>
@endforelse
{{ $notifications->links() }}