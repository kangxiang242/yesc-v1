<style>
    .broadside-order-message .broadside-content .broadside-news-item:last-child{
        margin-top: 10px;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #E6E6E6;
    }
</style>
<div class="broadside-row" data-track-block="web_component_order_message">

    <div class="broadside broadside-order-message">
        <div class="broadside-header">
            <p class="broadside-title">訂購消息</p>
        </div>

        <div class="broadside-content running" style="height: 285px;overflow: hidden;position: relative">
            <div class="runList" style="position: absolute">
                @php
                    $hs = [2,3,4,5,6,8,10,12,16,20];
                @endphp
                @for($i=0;$i<20;$i++)
                <div class="broadside-news-item">{{ date('Y-m-d', strtotime('-'.rand(0,3).' days')) }} 顧客手機末三碼{{ str_pad(rand(0,999),3,'0',STR_PAD_LEFT) }}訂購威而鋼【{{ $hs[array_rand($hs)] }}盒】經過隱密包裝已發出，請留意手機簡訊查收！<font color="#FF0000">√</font></div>
                @endfor
            </div>
        </div>

    </div>

</div>
<script>
function initOrderScroll() {
    if (typeof jQuery === 'undefined') { setTimeout(initOrderScroll, 100); return; }
    $(function(){
        var interval;
        var inter_time = 25;
        var height = $('.runList').height();

        $('.running').append($('.runList').clone());
        $('.runList').eq(1).css('top',height);
        shopinfo(inter_time);
        function shopinfo(time){

            interval = setInterval(function(){

                var top1 = $('.runList').eq(0).position().top;

                var top2 = $('.runList').eq(1).position().top;
                if(top1 <= -height){
                    $('.runList').eq(0).css('top',height);
                    top1 = height;
                }
                if(top2 <= -height){
                    $('.runList').eq(1).css('top',height);
                    top2 = height;
                }
                top1 -= 1;
                top2 -= 1;

                $('.runList').eq(0).css('top',top1);
                $('.runList').eq(1).css('top',top2);
            },time);
        }
        $('.running').hover(function(){
            clearInterval(interval);
        },function(){
            shopinfo(inter_time);
        });
    });
}
initOrderScroll();
</script>
