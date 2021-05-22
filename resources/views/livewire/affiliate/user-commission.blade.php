<div>
    <table>
        <thead>
            <tr>
                <th>Referrer</th>
                <th>Commission</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $keyId => $userRefferer)
                <tr wire:key="$userRefferer->id">
                    @if ($userRefferer->reffered_by == $user_id)
                        
                        <td>{{ $userRefferer->name }}</td>
                        <td>{{ $userRefferer->name }}</td>
                        
                    @endif
                </tr>
            @endforeach
            
        </tbody>
    </table>
</div>
