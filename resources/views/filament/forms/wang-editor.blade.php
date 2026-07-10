@php
    $id = $getId();
    $statePath = $getStatePath();
    $mode = $getMode();
    $uploadUrl = $getUploadUrl();
    $toolbarKeys = $getToolbarButtons();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        id="{{ $id }}-wrapper"
        class="wang-editor-wrapper"
        style="border: 1px solid #dbe3e6; border-radius: 4px;"
        data-mode="{{ $mode }}"
        data-upload-url="{{ $uploadUrl }}"
        data-toolbar-keys='@json($toolbarKeys)'
    >
        <div id="{{ $id }}-tb" class="w-e-tb" style="border-bottom: 1px solid #dbe3e6;"></div>
        <div id="{{ $id }}-ed" class="w-e-ed" style="height: 500px;"></div>
    </div>

    <input x-data id="{{ $id }}-h" type="hidden"
        :value="$wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }}">

    <style>
        .wang-editor-wrapper { border: 1px solid #dbe3e6; border-radius: 4px; z-index: 100; }
        .wang-editor-wrapper .w-e-bar-item button { padding: 0 6px !important; }
        .w-e-text-container { z-index: 10; }
    </style>
</x-dynamic-component>
