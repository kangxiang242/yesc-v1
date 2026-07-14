

var stores_height;
var is_repeat = 0;
var store_item_num;

var order_type = $('input[name="order_type"]:checked').val();

getCity(order_type);

$('input[name="order_type"]').click(function(){
    initialSelectStatus(0)
    getCity($(this).val());
    order_type = $('input[name="order_type"]:checked').val();
    $('.editor-store').hide();
    if(order_type > 0){
        $("#address-row-main").hide();
        $("#store-row-main").show();
        $('#time-row-main').hide();

    }else{
        $("#address-row-main").show();
        $('#time-row-main').show();
        $("#store-row-main").hide();
    }
})



$('#city').change(function(){
    if($(this).val()){
        initialSelectStatus(2);
        initialSelectStatus(3);
        initialSelectStatus(4);
        getCounty(order_type,$(this).val());
    }
});

$('#county').change(function(){
    var city_name = $('#city').val();
    if($(this).val() && city_name){
        initialSelectStatus(3);
        initialSelectStatus(4);
        getRoad(order_type,city_name,$(this).val());
    }
});

$('#street').change(function(){
    var city_name = $('#city').val();
    var county_name = $('#county').val();
    if($(this).val() && city_name && county_name && order_type>0){
        initialSelectStatus(4);
        getShop(order_type,city_name,county_name,$(this).val());
    }
});



function selectOption(province,id){
    if(id=='street'){
        var _option = '<option value="">--選擇路段--</option>';
    }else if(id=='county'){
        var _option = '<option value="">--選擇地區--</option>';
    }else{
        var _option = '<option value="">--選擇縣市--</option>';
    }

    for (i in province)
    {
        _option += '<option data-id="'+province[i].id+'" value="'+province[i].name+'">'+province[i].name+'</option>'
    }

    $('#'+id).html(_option);
}


function getCity(type){

    selectLoadingEffect('#load-1');

    $.ajax({
        type : "GET",  //提交方式
        url : "/area/city",//路径
        data : {
            "type":type,
        },
        dataType:'json',
        success : function(result) {//返回数据根据结果进行相应的处理

            if(isJSON(result)){
                result = JSON.parse(result);
            }

            selectOption(result,'city');
            removeLoadingEffect('#load-1')
        }
    });
}

function getCounty(type,city_name){

    selectLoadingEffect('#load-2');

    $.ajax({
        type : "GET",  //提交方式
        url : "/area/county",//路径
        dataType:'json',
        data : {
            "type":type,
            "city_name" : city_name
        },
        success : function(result) {//返回数据根据结果进行相应的处理

            if(isJSON(result)){
                result = JSON.parse(result);
            }
            selectOption(result,'county');
            removeLoadingEffect('#load-2')
        }
    });
}

function getRoad(type,city_name,county_name){
    selectLoadingEffect('#load-3');
    $.ajax({
        type : "GET",  //提交方式
        url : "/area/road",//路径
        dataType:'json',
        data : {
            "type":type,
            "city_name" : city_name,
            "county_name":county_name,
        },
        success : function(result) {//返回数据根据结果进行相应的处理
            if(isJSON(result)){
                result = JSON.parse(result);
            }
            selectOption(result,'street');
            removeLoadingEffect('#load-3')
        }
    });
}

function getShop(type,city_name,county_name,road_name){
    $('#store-row-main').show();
    $('.editor-store').hide();
    $('.stores').show();
    var load = '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block store-load" style="width:50px;height:11px;opacity: 0.55;position: absolute;top: 50%;transform: translate(-50%,-50%);left: 50%;" viewBox="0 0 120 30" fill="currentColor"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg>'
    $('.show-store-shop').empty();
    $('.show-store-shop').append(load);
    $.ajax({
        type : "GET",  //提交方式
        url : "/area/shop",//路径
        dataType:'json',
        data : {
            "type":type,
            "city_name" : city_name,
            "county_name":county_name,
            "road_name":road_name,
        },
        success : function(result) {//返回数据根据结果进行相应的处理
            if(isJSON(result)){
                result = JSON.parse(result);
            }
            $('.store-load').remove();
            store_item_num = result.length;
            renderingStoreShop(result);

        }
    });
}


function selectLoadingEffect(elem){
    $(elem).find('select').empty();
    var load = '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block select-load" style="width:50px;height:11px;opacity: 0.55;position: absolute;top: 50%;transform: translateY(-50%);left: 14px;" viewBox="0 0 120 30" fill="currentColor"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg>'
    $(elem).append(load);
    $(elem).attr('disabled','disabled');
}

function removeLoadingEffect(elem){
    $(elem).removeAttr('disabled');
    $(elem).find('.select-load').remove();
}

/**
 *
 * @param type 0全部恢复 1恢复市县 2恢复地区 3恢复路段
 */
function initialSelectStatus(type){

    var _option1 = '<option value="">--選擇縣市--</option>';

    var _option2 = '<option value="">--選擇地區--</option>';

    var _option3 = '<option value="">--選擇路段--</option>';

    if(type == 0 || type==1){
        $('#city').html(_option1);
    }

    if(type == 0 || type==2){
        $('#county').html(_option2);
    }

    if(type == 0 || type==3){
        $('#street').html(_option3);
    }

    if(type == 0 || type==4){
        $('.stores').hide();
        //$('#store-row-main').hide();
    }

}

/**
 * 判断是否是json
 * @param str
 * @returns {boolean}
 */
function isJSON(str) {
    if (typeof str == 'string') {
        try {
            JSON.parse(str);
            return true;
        } catch(e) {
            return false;
        }
    }
}


function showStoreAll(){
    if(is_repeat == 1){
        return;
    }
    is_repeat = 1
    $('#main-store-shop').hide();
    $('.editor-store').hide();
    $('.show-store-shop .store-item').each(function(){
        $(this).find('.store-info-box').css("border", "1px solid #ccc");
        $(this).find('input').removeAttr("checked");
        $(this).find('.radioInput').removeClass('radioInput').addClass('radioInput2');
    });
    var height = store_item_num*72+store_item_num*20
    $('.stores').animate({height:height+'px'},'300',function(){
        is_repeat = 0;
    });

}

function hideStoreAll(obj){

    if(is_repeat == 1){
        return;
    }
    $('#store-messages').hide();
    is_repeat = 1
    obj.find('.radioInput2').removeClass('radioInput2').addClass('radioInput');
    $('.stores').animate({height:'72px'},'300',function(){
        $('#main-store-shop').show();
        editMainStoreShop(obj);
        $('.editor-store').show();
        obj.find('.store-info-box').addClass('store-info-box-vate');
        obj.siblings().find('.store-info-box').removeClass('store-info-box-vate');
        is_repeat = 0;
    });

}

$('body').on('click','.store-item-label',function(){
    if(store_item_num == 1){
        return;
    }

    hideStoreAll($(this).parents('.store-item'));
});

function editMainStoreShop(obj){
    $('#main-store-shop .store-name').text(obj.find('.store-name').text());
    $('#main-store-shop .store-address').text(obj.find('.store-address').text());
    $('#main-store-shop .store-icon img').attr('src',obj.find('.store-icon img').attr('src'));
}

function renderingStoreShop(data){
    var template = $('#store-template').html();
    var html = '';
    for (var i=0;i<data.length;i++){
        html += template.replace(/\{store_id}/g, data[i]['shop_no']);
        html = html.replace(/\{store_name}/g, data[i]['shop_name']);
        html = html.replace(/\{store_address}/g, data[i]['shop_address']);
        if(data[i]['shop_type'] == 1){
            html = html.replace(/\{store_icon}/g, '/static/home/images/family.jpg');
        }else if(data[i]['shop_type'] == 2){
            html = html.replace(/\{store_icon}/g, '/static/home/images/ok.jpg');
        }else if(data[i]['shop_type'] == 3){
            html = html.replace(/\{store_icon}/g, '/static/home/images/hilife.jpg');
        }else{
            html = html.replace(/\{store_icon}/g, '/static/home/images/711.png');
        }
    }
    $('.show-store-shop').html(html);
    if(store_item_num == 1){
        $('input[name="store_id"]').attr('checked',true);
        $('.stores').css('height','72px');
        $('.store-info-box').addClass('store-info-box-vate');
    }else{
        $('.stores').css('height','auto');
    }

    $('#main-store-shop').hide();
    $('.editor-store').hide();

}

$('body').on('click',"input[name='store_id']",function(){

    var val = $(this).val();

    if(store_item_num == 1){
        return;
    }
    hideStoreAll($('#label-store-'+val).parents('.store-item'));

});