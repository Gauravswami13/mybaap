<x-filament-panels::page>
    <div class="ft-page">
        <div class="rpt-head">
            <div>
                <h1>Notifications</h1>
                <p class="muted" style="margin:4px 0 0">Inbox me save hota hai. Phone push tab jayega jab Settings → Messaging me Firebase lagao.</p>
            </div>
        </div>

        <section class="card">
            <h3>Push / in-app notification</h3>
            {{ $this->content }}
        </section>

        <section class="card mt">
            <h3>Recent notifications</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    <div class="muted">{{ $item->body }}</div>
                                </td>
                                <td>{{ $item->audience === 'one' ? ($item->user?->mobile ?? 'One user') : 'All users' }}</td>
                                <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="muted" colspan="3">No notifications yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament-panels::page>
