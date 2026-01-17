@foreach($students as $ef)
<tr>
    <td>{{ $ef->ef_no }}</td>
    <td>{{ $ef->student->name }}</td>
    @if(auth()->user()->isAdmin())
    <td class="px-4 py-2">
        {{ $ef->student->branch->name ?? '-' }}
    </td>
    @endif
    <td>{{ $ef->student->id_no }}</td>
    <td>{{ $ef->course->course }}</td>
    <td>{{ $ef->total_fee }}</td>
    <td>{{ $ef->total_class }}</td>
    <td>{{ ucfirst($ef->status) }}</td>

    <td>
        <button onclick="openEditModal({{ $ef->id }})"
                class="text-blue-600 hover:underline">Edit</button>
        <button onclick="openDeleteModal({{ $ef->id }})"
                class="text-red-600 hover:underline">Delete</button>
    </td>
</tr>
@endforeach
