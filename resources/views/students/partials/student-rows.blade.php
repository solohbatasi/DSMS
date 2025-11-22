@foreach($students as $ef)
<tr>
    <td>{{ $ef->ef_no }}</td>
    <td>{{ $ef->student->name }}</td>
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
