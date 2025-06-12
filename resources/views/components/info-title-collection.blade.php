<div class="card bg-light p-4 m-2 flex-grow-1">
    <h5 class="text-secondary">{{ $itemTitle }}</h5>
    
    <table class="w-100">
        @forelse ($collection as $item)
            <tr>
                <td>{{ $item['department'] ?? 'N/A' }}</td>
                <td class="text-end"><strong>{{ $item['total'] ?? 0 }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center">Nenhum dado disponível</td>
            </tr>
        @endforelse
    </table>
</div>