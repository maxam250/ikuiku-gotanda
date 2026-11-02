<?php


@foreach($images as $image)
    <div class="image-item">
        <img src="{{ asset($image->filename) }}" alt="Image" width="100">
        <input type="checkbox" class="toggle-flag" data-id="{{ $image->id }}" {{ $image->flag ? 'checked' : '' }}> Flag
        <button class="delete-image" data-id="{{ $image->id }}">Delete</button>
    </div>
@endforeach
