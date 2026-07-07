@push('rice-scroll')
<script>
    (function() {
        // ==================== 速度配置 ====================
        // 滚动速度（单位：秒，数值越小速度越快）
        // 例如：20 表示20秒完成一轮滚动
        var RICE_SCROLL_DURATION = 30;
        // ==================== 配置结束 ====================
        
        // 稻穗无缝循环滚动
        $(document).ready(function() {
            var $riceScroll = $('.rice-scroll');
            var $riceItems = $('.rice-item');
            
            if ($riceItems.length === 0) return;
            
            // 保存所有6个不同的内容项
            var itemsHtml = [];
            $riceItems.each(function() {
                itemsHtml.push($(this)[0].outerHTML);
            });
            
            var totalItems = itemsHtml.length;
            
            // 清空并重新填充
            $riceScroll.empty();
            
            // 复制整个集合以实现无缝循环（复制2次，总共会有 6 * 2 = 12 个项目）
            // 这样移动到-50%时正好是一组内容，实现无缝循环
            var copies = 2;
            for (var i = 0; i < copies; i++) {
                for (var j = 0; j < totalItems; j++) {
                    $riceScroll.append(itemsHtml[j]);
                }
            }
            
            // 设置动画速度
            $riceScroll.css('animation-duration', RICE_SCROLL_DURATION + 's');
        });
    })();
</script>
@endpush


<ul class="rice-scroll">
    <li class="rice-item"><p class="rice-text"><span class="big-text">上市28年</span>臨床使用時間最長用藥</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">20億+人次使用</span>安全數據最完整</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">銷量No.1</span>全球最暢銷壯陽藥</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">FDA核准</span>ED治療用藥</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">臨床首選方案</span>短效型代表藥物</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">廣泛應用</span>橫跨多項臨床需求</p></li>
    <li class="rice-item"><p class="rice-text"><span class="big-text">30分鐘起效</span>使用時間明確可控</p></li>
</ul>