<link href="/static/vendor/wangEditor5/style.css" rel="stylesheet">
<script>
    // wangEditor5 — 全局只加载一次核心库，编辑器初始化由各组件视图自行处理
    // MutationObserver 兼容 Livewire SPA 导航中新增的编辑器
    (function () {
        if (window.__weLoaded) return;
        window.__weLoaded = true;

        var s = document.createElement('script');
        s.src = '/static/vendor/wangEditor5/index.js';
        s.onload = function () {
            // —— 注册繁中 i18n 资源 ——
            if (window.wangEditor && window.wangEditor.i18nAddResources) {
                window.wangEditor.i18nAddResources('zh-TW', {
                    common: { ok: '确定', delete: '删除', enter: '回车' },
                    blockQuote: { title: '引用' },
                    codeBlock: { title: '代码块' },
                    color: { color: '文字颜色', bgColor: '背景色', default: '默认颜色', clear: '清除背景色' },
                    divider: { title: '分割线' },
                    emotion: { title: '表情' },
                    fontSize: { title: '字型大小', default: '預設字型大小' },
                    fontFamily: { title: '字型', default: '預設字型' },
                    fullScreen: { title: '全螢幕' },
                    header: { title: '标题', text: '正文' },
                    image: { netImage: '网络图片', delete: '删除图片', edit: '编辑图片', viewLink: '查看链接', src: '图片地址', desc: '图片描述', link: '图片链接' },
                    indent: { decrease: '减少缩进', increase: '增加缩进' },
                    justify: { left: '靠左', right: '靠右', center: '置中', justify: '两端对齐' },
                    lineHeight: { title: '行高', default: '預設行高' },
                    link: { insert: '插入链接', text: '链接文本', url: '链接地址', unLink: '取消链接', edit: '修改链接', view: '查看链接' },
                    textStyle: { bold: '粗體', clear: '清除格式', code: '行内代码', italic: '斜體', sub: '下标', sup: '上标', through: '刪除線', underline: '底線' },
                    undo: { undo: '復原', redo: '重做' },
                    todo: { todo: '待办' },
                    listModule: { unOrderedList: '無序列表', orderedList: '有序列表' },
                    tableModule: { deleteCol: '删除列', deleteRow: '删除行', deleteTable: '删除表格', widthAuto: '宽度自适应', insertCol: '插入列', insertRow: '插入行', insertTable: '插入表格', header: '表头' },
                    videoModule: { delete: '删除视频', uploadVideo: '上传视频', insertVideo: '插入视频', videoSrc: '视频地址', videoSrcPlaceHolder: '视频文件 url 或第三方 <iframe>', videoPoster: '视频封面', videoPosterPlaceHolder: '封面图片 url', ok: '确定', editSize: '修改尺寸', width: '宽度', height: '高度' },
                    uploadImgModule: { uploadImage: '上传图片', uploadError: '{{fileName}} 上传出错' },
                    highLightModule: { selectLang: '选择语言' },
                });
            }
        };
        document.head.appendChild(s);

        // 等 document.body 就绪后再设置 MutationObserver
        function setupObserver() {
            if (!document.body) {
                requestAnimationFrame(setupObserver);
                return;
            }

            // —— 全局 Fix: Choices.js "Remove item" → "移除" ——
            function fixRemoveItemLabel(node) {
                if (node.nodeType !== 1) return;
                if (node.matches && node.matches('.choices__item')) {
                    var btn = node.querySelector('.choices__button');
                    if (btn && (!btn.getAttribute('aria-label') || btn.getAttribute('aria-label') === 'Remove item')) {
                        btn.setAttribute('aria-label', '移除');
                    }
                    if (btn && (btn.innerText === 'Remove item' || btn.textContent.trim() === 'Remove item')) {
                        btn.innerText = '移除';
                    }
                }
                if (node.querySelectorAll) {
                    node.querySelectorAll('.choices__item').forEach(function (item) {
                        var btn = item.querySelector('.choices__button');
                        if (btn && (!btn.getAttribute('aria-label') || btn.getAttribute('aria-label') === 'Remove item')) {
                            btn.setAttribute('aria-label', '移除');
                        }
                        if (btn && (btn.innerText === 'Remove item' || btn.textContent.trim() === 'Remove item')) {
                            btn.innerText = '移除';
                        }
                    });
                }
            }

            // 初始扫描
            fixRemoveItemLabel(document.body);

            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (node) {
                        if (node.nodeType !== 1) return;
                        // 修复 Choices.js "Remove item" 文本
                        fixRemoveItemLabel(node);
                        // 查找新添加的 wang-editor-wrapper（未初始化过的）
                        var wrappers = node.matches('.wang-editor-wrapper')
                            ? [node]
                            : node.querySelectorAll('.wang-editor-wrapper:not([data-ready])');
                        wrappers.forEach(function (w) {
                            if (w.__weInitScheduled) return;
                            w.__weInitScheduled = true;
                            // 触发组件内的 Alpine JS 事件或重执行 script
                            // 直接 dispatch 自定义事件让组件内脚本感知
                            w.dispatchEvent(new CustomEvent('we-dom-added', { bubbles: true }));
                        });
                    });
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
        setupObserver();
    })();
</script>
