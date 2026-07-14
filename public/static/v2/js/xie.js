var tips_layer;

function tips(msg,sub_msg,iconfont,color_class,options){
    var close_location='';
    if(options){
        if(options.close_location){
            close_location='data-location-href="'+options.close_location+'" ';
        }
    }

    if(!sub_msg){
        sub_msg = '';
    }

     tips_layer = layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        shadeClose: false,
         time:2000,
         skin: 'tips-main',
        content: '<div class="info-tips-skin">\n' +
            '        <p class="text '+color_class+'-text">'+msg+'</p>\n' +

            '        <p class="sub-text">'+sub_msg+'</p>\n' +
            '    </div>'
    });
}

$('body').on('click','.close_tips_layer',function(){
    layer.close(tips_layer);
    location.href=$(this).attr('data-location-href')
})


var xie = {
    success:function(msg,sub_msg,options){

        tips(msg,sub_msg,'&#xe639;','success',options)
    },
    error:function(msg,sub_msg,options){
        tips(msg,sub_msg,'&#xe6f9;','error',options)
    }
};
//xie.success('恭喜下单成功','商品經隱秘包裹后隨即寄出');
if(flash_data){
    var _flash = JSON.parse(flash_data);

    if(_flash.code==200){
        xie.success(_flash.msg,_flash.sub_msg);
    }else if(_flash.code==400){
        xie.error(_flash.msg,_flash.sub_msg);
    }
}
