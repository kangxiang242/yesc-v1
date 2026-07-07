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

    <link href="/static/vendor/wangEditor5/style.css" rel="stylesheet">
    <script>
        // Load wangEditor5 script once
        if (!window.__weLoaded) {
            window.__weLoaded = true;
            var s = document.createElement('script');
            s.src = '/static/vendor/wangEditor5/index.js';
            s.onload = function () { window.__weReady = true; initWE(); };
            document.head.appendChild(s);
        }

        function initWE(target) {
            if (typeof window.wangEditor === 'undefined') { return; }
            var wrappers = target
                ? target.querySelectorAll('.wang-editor-wrapper')
                : document.querySelectorAll('.wang-editor-wrapper');
            wrappers.forEach(function (w) {
                if (w.__weDone) return;
                w.__weDone = true;
                var id = w.id.replace('-wrapper', '');
                var h = document.getElementById(id + '-h');
                var html = h ? (h.value || h.getAttribute('value') || '') : '';
                var ec = {
                    placeholder: '请输入内容...',
                    MENU_CONF: {
                        uploadImage: {
                            server: '{{ $uploadUrl }}',
                            fieldName: 'file',
                            meta: {
                                _token: document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') || '',
                            },
                        }
                    },
                    onChange: function (editor) {
                        var h2 = document.getElementById(id + '-h');
                        var h2v = editor.getHtml();
                        if (h2 && h2.value !== h2v) {
                            h2.value = h2v;
                            h2.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    },
                };
                try {
                    var ed = window.wangEditor.createEditor({
                        selector: '#' + id + '-ed',
                        html: html || '',
                        config: ec,
                        mode: '{{ $mode }}',
                    });
                    window.wangEditor.createToolbar({
                        editor: ed,
                        selector: '#' + id + '-tb',
                        config: { toolbarKeys: @json($toolbarKeys) },
                        mode: '{{ $mode }}',
                    });
                } catch (e) {
                    console.error('WE:', e);
                }
            });
        }

        // 页面加载后初始化
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { if (window.__weReady) initWE(); });
        } else {
            if (window.__weReady) initWE();
        }

        // 监听 Livewire DOM 更新，自动初始化新添加的编辑器
        var observer = new MutationObserver(function (mutations) {
            if (!window.__weReady) return;
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1 && (node.matches('.wang-editor-wrapper') || node.querySelector('.wang-editor-wrapper'))) {
                        initWE(node.nodeType === 1 && node.matches('.wang-editor-wrapper') ? node : null);
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    </script>
</x-dynamic-component>
