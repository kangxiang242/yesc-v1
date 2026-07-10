<link href="/static/vendor/wangEditor5/style.css" rel="stylesheet">
<script>
    // wangEditor5 — 全局只加载一次，通过 MutationObserver 自动初始化编辑器
    (function () {
        if (window.__weLoaded) return;
        window.__weLoaded = true;

        var wangEditorReady = false;

        var s = document.createElement('script');
        s.src = '/static/vendor/wangEditor5/index.js';
        s.onload = function () {
            wangEditorReady = true;
            initAllWangEditors();
        };
        document.head.appendChild(s);

        function initAllWangEditors(target) {
            if (typeof window.wangEditor === 'undefined') return;
            var wrappers = target
                ? target.querySelectorAll('.wang-editor-wrapper')
                : document.querySelectorAll('.wang-editor-wrapper');
            wrappers.forEach(function (w) {
                if (w.__weDone) return;
                w.__weDone = true;
                var id = w.id.replace('-wrapper', '');
                var h = document.getElementById(id + '-h');
                var html = h ? (h.value || h.getAttribute('value') || '') : '';
                var toolbarRaw = w.dataset.toolbarKeys || '[]';
                var mode = w.dataset.mode || 'default';
                var uploadUrl = w.dataset.uploadUrl || '';
                var toolbarKeys = [];
                try { toolbarKeys = JSON.parse(toolbarRaw); } catch (e) {}

                // 直接用 DOM 元素，避免 CSS 选择器中 id 含 . 被解析为 class
                var editorEl = document.getElementById(id + '-ed');
                var toolbarEl = document.getElementById(id + '-tb');
                if (!editorEl) return;

                var ec = {
                    placeholder: '请输入内容...',
                    MENU_CONF: {
                        uploadImage: {
                            server: uploadUrl,
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
                        selector: editorEl,
                        html: html || '',
                        config: ec,
                        mode: mode,
                    });
                    window.wangEditor.createToolbar({
                        editor: ed,
                        selector: toolbarEl,
                        config: { toolbarKeys: toolbarKeys },
                        mode: mode,
                    });
                } catch (e) {
                    console.error('WE:', e);
                }
            });
        }

        // 等 body 就绪后再设置 MutationObserver
        function setupObserver() {
            if (!document.body) {
                requestAnimationFrame(setupObserver);
                return;
            }
            var observer = new MutationObserver(function (mutations) {
                if (!wangEditorReady) return;
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1 && (node.matches('.wang-editor-wrapper') || node.querySelector('.wang-editor-wrapper'))) {
                            initAllWangEditors(node.nodeType === 1 && node.matches('.wang-editor-wrapper') ? node : null);
                        }
                    });
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
        setupObserver();

        // DOMContentLoaded 时再扫一遍，以防 MutationObserver 漏掉
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { if (wangEditorReady) initAllWangEditors(); });
        } else {
            if (wangEditorReady) initAllWangEditors();
        }
    })();
</script>
