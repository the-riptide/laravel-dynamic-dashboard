@props(['model'])

<div
    x-data="{ value: @entangle($model->model) }"
    x-init="
        tinymce.init({
            target: $refs.tinymce,
            themes: 'modern',
            height: 200,
            menubar: false,
            paste_as_text: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            setup: function(editor) {
                editor.on('blur', function(e) {
                    value = editor.getContent()
                })
                editor.on('init', function (e) {
                    if (value != null) {
                        editor.setContent(value)
                    }
                })
                function putCursorToEnd() {
                    editor.selection.select(editor.getBody(), true);
                    editor.selection.collapse(false);
                }
                $watch('value', function (newValue) {
                    if (newValue !== editor.getContent()) {
                        editor.resetContent(newValue || '');
                        putCursorToEnd();
                    }
                });
            }
        })
    "
    wire:ignore
>
    <div>
        <label class="block text-sm font-medium text-gray-700"> {{$model->title}} </label>

        <input
            x-ref="tinymce"
            type="textarea"
            placeholder= "{{ $model->placeholder }}"
        >
    </div>
</div>

@once
    @push('head')
        <script src="https://cdn.tiny.cloud/1/1t34fhcvbr8km3mjjq4m5iuchg080id0uh64alg9ouv1cnpi/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush
@endonce