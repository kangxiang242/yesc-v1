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
        <input type="hidden" id="{{ $htmlId }}-h" :value="weVal" value="{{ $initialContent }}">
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

                // 切换 wangEditor 语言为繁中
                if (window.wangEditor.i18nChangeLanguage) {
                    window.wangEditor.i18nChangeLanguage('zh-TW');
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
                    placeholder: '請輸入內容...',
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

                // —— HTML 预清洗：移除空 <a>、空 <span>、修复块级元素嵌套 ——
                function cleanEditorHtml(raw) {
                    if (!raw) return '';
                    var d = document.createElement('div');
                    d.innerHTML = raw;

                    // 1) 移除空 <a>（无子节点或仅空白文本）
                    d.querySelectorAll('a').forEach(function (el) {
                        var txt = el.textContent.replace(/\s+/g, '');
                        if (!txt && !el.querySelector('img, video, iframe')) {
                            el.remove();
                        }
                    });

                    // 2) 移除空 <span>（无子节点或仅空白文本/换行）
                    d.querySelectorAll('span').forEach(function (el) {
                        if (!el.children.length && !el.textContent.trim()) {
                            el.remove();
                        }
                    });

                    // 3) 将 <span> 内的块级子元素（p, div, h1-h6, ul, ol, table 等）上提一层
                    d.querySelectorAll('span').forEach(function (el) {
                        var blockChild = el.querySelector('p, div, h1, h2, h3, h4, h5, h6, ul, ol, table, blockquote, pre');
                        if (blockChild) {
                            // 将 span 的所有子节点上提到 span 父级
                            var parent = el.parentNode;
                            if (parent) {
                                while (el.firstChild) {
                                    parent.insertBefore(el.firstChild, el);
                                }
                                el.remove();
                            }
                        }
                    });

                    // 4) 清理残留的空 <p></p>（多个连续只保留一个）
                    d.querySelectorAll('p').forEach(function (el) {
                        if (!el.children.length && !el.textContent.trim()) {
                            el.remove();
                        }
                    });

                    return d.innerHTML;
                }

                html = cleanEditorHtml(html);

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
                    // 创建失败时降级：显示可编辑的 textarea
                    if (!document.getElementById(id + '-ed-fallback')) {
                        var fallback = document.createElement('textarea');
                        fallback.id = id + '-ed-fallback';
                        fallback.className = 'we-code-textarea';
                        fallback.style.position = 'relative';
                        fallback.style.top = 'auto';
                        fallback.value = html;
                        editorEl.parentNode.replaceChild(fallback, editorEl);

                        // 同步到隐藏 input
                        fallback.addEventListener('input', function () {
                            var h2 = document.getElementById(id + '-h');
                            if (h2 && h2.value !== fallback.value) {
                                h2.value = fallback.value;
                                h2.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        });
                    }
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
