@php
    $id = $getId();
    // 去掉点号，避免 CSS 选择器将 . 解析为 class
    $htmlId = str_replace('.', '_', $id);
    $statePath = $getStatePath();
    $mode = $getMode();
    $uploadUrl = $getUploadUrl();
    $toolbarKeys = $getToolbarButtons();
    $initialContent = $getState();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore
         id="{{ $htmlId }}-wrapper"
         class="wang-editor-wrapper"
         data-mode="{{ $mode }}"
         data-upload-url="{{ $uploadUrl }}"
         data-toolbar-keys='@json($toolbarKeys)'
         data-has-content="{{ $initialContent ? 'true' : 'false' }}"
         x-data="{ weVal: $wire.$entangle('{{ $statePath }}') }"
         style="border: 1px solid #dbe3e6; border-radius: 4px; position: relative;"
    >
        <div id="{{ $htmlId }}-tb" style="border-bottom: 1px solid #dbe3e6;"></div>
        <div id="{{ $htmlId }}-ed" style="height: 500px;"></div>
        <input type="hidden" id="{{ $htmlId }}-h" :value="weVal">
    </div>

    <style>
        .wang-editor-wrapper { border: 1px solid #dbe3e6; border-radius: 4px; z-index: 100; }
        .wang-editor-wrapper .w-e-bar-item button { padding: 0 6px !important; }
        .w-e-text-container { z-index: 10; }

        /* 原始码按钮样式 */
        .we-code-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            color: #595959;
            height: 40px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 21px;
            min-width: 30px;
        }
        .we-code-btn:hover { background-color: #f0f0f0; }

        /* 原始码编辑区样式（overlay 覆盖） */
        .we-code-textarea {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 15;
            width: 100%;
            min-height: 500px;
            padding: 12px;
            font-family: Menlo, Consolas, monospace;
            font-size: 13px;
            border: none;
            outline: none;
            resize: vertical;
            tab-size: 2;
            background: #fafafa;
            box-sizing: border-box;
        }
    </style>

    <script>
        (function () {
            if (window.__weEditorInit) return;
            window.__weEditorInit = true;

            var id = '{{ $htmlId }}';
            var el = document.getElementById(id + '-wrapper');

            function initEditor(target) {
                if (typeof window.wangEditor === 'undefined') {
                    setTimeout(function () { initEditor(target); }, 100);
                    return;
                }

                var w = target || document.querySelector('.wang-editor-wrapper[data-ready="1"]');
                if (!w) return;
                if (w.__weDone) return;
                w.__weDone = true;

                var h = document.getElementById(id + '-h');
                var html = h ? (h.value || h.getAttribute('value') || '') : '';
                var hasContent = w.getAttribute('data-has-content') === 'true';
                var toolbarRaw = w.dataset.toolbarKeys || '[]';
                var mode = w.dataset.mode || 'default';
                var uploadUrl = w.dataset.uploadUrl || '';
                var toolbarKeys = [];
                try { toolbarKeys = JSON.parse(toolbarRaw); } catch (e) {}

                // 内容未就绪时重试（仅编辑页面）
                if (!html && hasContent) {
                    // 尝试从 Alpine 数据读取
                    var alpineData = w.__x && w.__x.getUnobservedData ?
                        w.__x.getUnobservedData() : null;
                    if (alpineData && alpineData.weVal) {
                        html = alpineData.weVal;
                    }
                    if (!html && !target) {
                        w.__weDone = false;
                        setTimeout(function () { initEditor(w); }, 200);
                        return;
                    }
                }

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
                    w.__weDone = false;
                    return;
                }

                // —— 原始码切换 ——
                var codeMode = false;
                var ta = null;
                var tbContainer = document.getElementById(id + '-tb');

                // 等待工具栏渲染后追加 </> 按钮
                setTimeout(function () {
                    var bar = tbContainer ? tbContainer.querySelector('.w-e-bar') : null;
                    if (!bar) return;

                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'we-code-btn';
                    btn.innerHTML = '&lt;/&gt;';
                    btn.title = '切换原始码';

                    btn.addEventListener('click', function () {
                        codeMode = !codeMode;
                        if (codeMode) {
                            // 切换到原始码模式
                            var rawHtml = ed.getHtml();
                            if (!ta) {
                                ta = document.createElement('textarea');
                                ta.className = 'we-code-textarea';
                                ta.spellcheck = false;
                                // 计算工具栏高度
                                var tb = tbContainer ? tbContainer.querySelector('.w-e-bar') : null;
                                var tbHeight = tb ? tb.offsetHeight : 40;
                                ta.style.top = tbHeight + 'px';

                                ta.addEventListener('input', function () {
                                    var h2 = document.getElementById(id + '-h');
                                    if (h2 && h2.value !== ta.value) {
                                        h2.value = ta.value;
                                        h2.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                });
                            }
                            ta.value = rawHtml;
                            w.appendChild(ta);
                            btn.textContent = '視覺';
                        } else {
                            // 切换回可视化模式
                            if (ta) {
                                try {
                                    ed.setHtml(ta.value);
                                } catch (e) {
                                    // 编辑器实例可能已销毁，重新初始化
                                    var h2 = document.getElementById(id + '-h');
                                    if (h2) {
                                        h2.value = ta.value;
                                        h2.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                    w.__weDone = false;
                                    initEditor(w);
                                }
                                ta.remove();
                                ta = null;
                            }
                            btn.textContent = '&lt;/&gt;';
                        }
                    });

                    bar.appendChild(btn);

                    // 监听工具栏重渲染，保持 </> 按钮位置
                    var tbObserver = new MutationObserver(function () {
                        var freshBar = tbContainer.querySelector('.w-e-bar');
                        if (freshBar && btn.parentNode !== freshBar) {
                            freshBar.appendChild(btn);
                        }
                    });
                    tbObserver.observe(tbContainer, { childList: true, subtree: true });
                }, 200);

                w.dataset.ready = '1';
            }

            // 立即初始化（DOM 已就绪）
            if (document.readyState !== 'loading') {
                initEditor(el);
            } else {
                document.addEventListener('DOMContentLoaded', function () {
                    initEditor(el);
                });
            }
        })();
    </script>
</x-dynamic-component>
