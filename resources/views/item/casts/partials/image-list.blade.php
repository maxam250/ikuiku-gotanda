<ul id="sortable">
@foreach($images as $image)
    <li data-id="{{ $image->id }}" class="image-item ui-state-default">
        <img src="uploads/{{ asset($image->filename) }}" alt="Image" width="100">
        <input type="checkbox" class="toggle-flag" data-id="{{ $image->id }}" {{ $image->flag ? 'checked' : '' }}> Flag
        <button class="delete-image" data-id="{{ $image->id }}">Delete</button>
    </li>
@endforeach
</ul>
