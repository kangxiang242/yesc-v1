<div x-data="{}" x-init="initCodeEditor()"></div>

<script>
function initCodeEditor() {
    document.querySelectorAll('trix-editor').forEach(function (editor) {
        var toolbar = editor.previousElementSibling;
        if (!toolbar || toolbar.tagName !== 'TRIX-TOOLBAR') return;
        if (toolbar.querySelector('.trix-source-btn')) return;

        // 获取关联的隐藏 input
        var inputId = editor.getAttribute('input');
        var editorInput = document.getElementById(inputId);
        if (!editorInput) {
            // 尝试通过 data-trix-input 属性查找
            editorInput = document.querySelector('input[value][data-trix-input]');
        }
        if (!editorInput) return;

        // 添加源码切换按钮
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'trix-source-btn trix-button';
        btn.textContent = '源碼';
        btn.style.cssText = 'margin-left:4px;position:relative;';

        var codeMode = false;
        var codeTextarea = null;

        btn.addEventListener('click', function () {
            codeMode = !codeMode;

            if (codeMode) {
                // 切换到源码模式：隐藏 trix-editor，显示 textarea
                editor.style.display = 'none';
                codeTextarea = document.createElement('div');
                codeTextarea.className = 'code-editor-wrapper';
                codeTextarea.style.cssText = 'border:1px solid #d1d5db;border-radius:6px;padding:12px;font-family:Menlo,Consolas,monospace;font-size:14px;line-height:1.6;min-height:300px;background:#1f2937;color:#e5e7eb;white-space:pre;overflow:auto;';
                codeTextarea.contentEditable = 'true';

                // 格式化 HTML
                var raw = editorInput.value || '';
                var formatted = formatHtml(raw);
                codeTextarea.textContent = formatted;

                editor.parentNode.insertBefore(codeTextarea, editor.nextSibling);
                btn.textContent = '視覺';
            } else {
                // 切换回视觉模式
                editor.style.display = '';
                if (codeTextarea) {
                    editorInput.value = codeTextarea.textContent;
                    editor.value = codeTextarea.textContent;
                    codeTextarea.remove();
                    codeTextarea = null;
                }
                btn.textContent = '源碼';
            }
        });

        // 添加到 toolbar 的最后一个 button group
        var buttonGroup = toolbar.querySelector('.trix-button-group--text-tools');
        if (buttonGroup) {
            var spacer = document.createElement('span');
            spacer.className = 'trix-button-group-spacer';
            buttonGroup.parentNode.insertBefore(spacer, buttonGroup.nextSibling);

            var newGroup = document.createElement('div');
            newGroup.className = 'trix-button-group';
            newGroup.appendChild(btn);
            buttonGroup.parentNode.insertBefore(newGroup, buttonGroup.nextSibling);
        } else {
            toolbar.appendChild(btn);
        }
    });
}

function formatHtml(html) {
    if (!html) return '';
    var tab = '  ';
    var result = '';
    var indent = '';

    html.split(/>\s*</).forEach(function (element) {
        if (element.match(/^\/\w/)) {
            indent = indent.substring(tab.length);
        }
        result += indent + '<' + element + '>\n';
        if (element.match(/^<?\w[^>]*[^/]$/) && !element.match(/^(br|hr|input|img|meta|link)/i)) {
            indent += tab;
        }
    });

    return result.substring(1, result.length - 2);
}
</script>