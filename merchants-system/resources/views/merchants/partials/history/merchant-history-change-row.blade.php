<tr>
    <td>{{ $change['label'] }}</td>

    @if($valueType === 'old')
        <td class="old-value">{{ $change['old'] }}</td>
    @elseif($valueType === 'new')
        <td class="new-value">{{ $change['new'] }}</td>
    @else
        <td class="old-value">{{ $change['old'] }}</td>
        <td class="new-value">{{ $change['new'] }}</td>
    @endif
</tr>
