<x-filament-panels::page>
    <div class="ft-page">
        <div class="rpt-head">
            <div>
                <h1>Account deletions</h1>
                <p class="muted" style="margin:4px 0 0">App se anonymize hue accounts. Remove dabane par subscription rows bhi hat jati hain.</p>
            </div>
        </div>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row->name ?: 'Deleted user' }}</strong>
                                    <div class="muted">{{ $row->mobile }}</div>
                                </td>
                                <td><span class="badge danger">{{ ucfirst($row->status) }}</span></td>
                                <td>{{ $row->deleted_at?->format('d M Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" type="button" wire:click="purge({{ $row->id }})" wire:confirm="Permanently delete this account? This cannot be undone.">Delete permanently</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="muted">No deleted accounts.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="rpt-table-foot muted">Showing {{ $rows->count() ? '1' : '0' }} to {{ $rows->count() }} of {{ $rows->count() }} entries</p>
        </section>
    </div>
</x-filament-panels::page>
