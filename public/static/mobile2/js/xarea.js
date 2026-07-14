$(function(){

    var stores_height;
    var is_repeat = 0;
    var store_item_num;

    var order_type = $('input[name="order_type"]:checked').val();

    initialize();
    $('input[name="order_type"]').click(function(){

        initialize();
    })
    function initialize(){
        var order_type = $('input[name="order_type"]:checked').val();
        initialSelectStatus(0)
        if(order_type > 0){
            $('#address-row-main').hide();
            $('#time-row-main').hide();
            $("#store-row-main").show();
            $('.show-store-shop').empty();

        }else{
            $('#address-row-main').show();
            $('#time-row-main').show();

            $('.show-store-shop').empty();
            $("#store-row-main").hide();
        }
    }



    /**
     *
     * @param type 0全部恢复 1恢复市县 2恢复地区 3恢复路段
     */
    function initialSelectStatus(type){

        var _option1 = '<option data-color="#ccc" value="">請選擇縣市</option>';

        var _option2 = '<option data-color="#ccc" value="">請選擇地區</option>';

        var _option3 = '<option data-color="#ccc" value="">請選擇路段</option>';

        if(type == 0 || type==1){
            $('input[name="city"]').val('');
        }

        if(type == 0 || type==2){
            $('input[name="county"]').val('');
        }

        if(type == 0 || type==3){
            $('input[name="street"]').val('');
        }

        if(type == 0 || type==4){
            $('#store-row-main').hide();
        }

        if(type == 0 || type==5){
            $('#casual').hide();
            $('.store-balance').css('height','auto')
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

        var height = store_item_num*72+store_item_num*20
        $('.stores').animate({height:height+'px'},'300',function(){
            is_repeat = 0;
        });

    }

    function hideStoreAll(obj){

        if(is_repeat == 1){
            return;
        }
        is_repeat = 1

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
    }

    function renderingStoreShop(data){
        var template = $('#store-template').html();
        var city = $('#city').val();
        var county = $('#county').val();
        var road = $('#street').val();



        var html = '';
        for (var i=0;i<data.length;i++){
            var shop_address = data[i]['shop_address'];
            var new_address = shop_address.replace(city+county+road, '');
            html += template.replace(/\{store_id}/g, data[i]['shop_no']);
            html = html.replace(/\{store_name}/g, data[i]['shop_name']);
            html = html.replace(/\{store_city}/g, city+county);
            html = html.replace(/\{store_road}/g, road);
            html = html.replace(/\{store_address}/g, new_address);
            if(data[i]['shop_type'] == 1){
                html = html.replace(/\{store_icon}/g, '/static/img/family.jpg');
            }else if(data[i]['shop_type'] == 2){
                html = html.replace(/\{store_icon}/g, '/static/img/ok.jpg');
            }else if(data[i]['shop_type'] == 3){
                html = html.replace(/\{store_icon}/g, '/static/img/hilife.jpg');
            }else{
                html = html.replace(/\{store_icon}/g, '/static/img/711.jpg');
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

    $('body').on('click','.store-label',function(){
        if($(this).find('.store-box').attr('data-not-show')){
            return false;
        }

        var height = $('#casual').height()+16;
        var _this = $(this);
        $('.store-balance').animate({height:height},'300',function(){
            $('#casual').find('.store-name').text(_this.find('.store-name').text());
            $('#casual').find('.store-city').text(_this.find('.store-city').text());
            $('#casual').find('.store-road').text(_this.find('.store-road').text());
            $('#casual').find('.store-address').text(_this.find('.store-address').text());
            $('#casual').find('.store-icon img').attr('src',_this.find('.store-icon img').attr('src'));
            $('#casual').show();


        });
    });

    $('.store-more a').click(function(){
        $('#casual').hide();
        var height = $('.show-store-shop').height()+16
        $('.temp_store_radio').click();
        $('.store-balance').animate({height:height+'px'},'300',function(){

        });
    });

})

