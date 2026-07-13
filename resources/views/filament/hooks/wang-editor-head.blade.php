<link href="/static/vendor/wangEditor5/style.css" rel="stylesheet">
<script>
    // wangEditor5 — 全局只加载一次核心库，编辑器初始化由各组件视图自行处理
    // MutationObserver 兼容 Livewire SPA 导航中新增的编辑器
    (function () {
        if (window.__weLoaded) return;
        window.__weLoaded = true;

        var s = document.createElement('script');
        s.src = '/static/vendor/wangEditor5/index.js';
        document.head.appendChild(s);

        // 等 document.body 就绪后再设置 MutationObserver
        function setupObserver() {
            if (!document.body) {
                requestAnimationFrame(setupObserver);
                return;
            }
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (m) {
                    m.addedNodes.forEach(function (node) {
                        if (node.nodeType !== 1) return;
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
