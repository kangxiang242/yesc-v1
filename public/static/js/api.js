(function($){
    $.fn.center = function(){
        var top = this.offset().top-($(window).height()/2);

        $('body,html').animate({scrollTop:top}, 200);
    }
})(jQuery)

var pop;
function orderStore(){

    var name = $("input[name='name']").val();
    var phone = $("input[name='phone']").val();
    var email = $("input[name='email']").val();
    var city = $("select[name='city']").val();
    var county = $("select[name='county']").val();
    var street = $("select[name='street']").val();
    var address = $("input[name='address']").val();
    var goods_id = $("input[name='goods_id']").val();
    var order_type = $("input[name='order_type']:checked").val();
    var store_id = $("input[name='store_id']:checked").val();

    var is_mobile = false;
    if (window.matchMedia("(max-width: 768px)").matches) {
        is_mobile = true;

    }
    if(order_type === undefined){
        $('.form-radio').contip({
            align: is_mobile?'bottom':'right',
            html: '請選擇配送方式',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }


    if(!name){

        $('input[name="name"]').focus();
        $('input[name="name"]').contip({
            align: is_mobile?'bottom':'right',
            html: '請填寫收件人姓名',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();

        return false;
    }

    if(!phone){

        $('input[name="phone"]').focus();
        $('input[name="phone"]').contip({
            align: is_mobile?'bottom':'right',
            html: '請填寫收件人聯絡電話',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }
    if(!(/^09\d{8}$/.test(phone))){

        $('input[name="phone"]').focus();
        $('input[name="phone"]').contip({
            align: is_mobile?'bottom':'right',
            html: '請填寫正確的聯絡電話',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!email){

        $('input[name="email"]').focus();
        $('input[name="email"]').contip({
            align: is_mobile?'bottom':'right',
            html: '請填寫收件人郵箱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){

        $('input[name="email"]').focus();
        $('input[name="email"]').contip({
            align: is_mobile?'bottom':'right',
            html: '請填寫正確的郵箱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!city){

        $("select[name='city']").contip({
            align: 'bottom',
            html: '請選擇縣市',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }

    if(!county){

        $("select[name='county']").contip({
            align: 'bottom',
            html: '請選擇地區',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }
    if(!street){

        $("select[name='street']").contip({
            align: 'bottom',
            html: '請選擇路段',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 2000,
            repeat:false,
        }).show();
        return false;
    }
    if(order_type > 0){

        if(!store_id){

            $('#show-store-shop').contip({
                align: 'bottom',
                html: '請選擇門市',
                fade: 360,
                opacity:0.6,
                delay_out:100,
                trigger:'focus',
                auto_close: 2000,
                repeat:false,
            }).show();
            return false;
        }

    }else{

        if(!address){

            $('input[name="address"]').focus();
            $('input[name="address"]').contip({
                align: is_mobile?'bottom':'right',
                html: '請填寫詳細地址',
                fade: 360,
                opacity:0.6,
                delay_out:100,
                trigger:'focus',
                auto_close: 2000,
                repeat:false,
            }).show();
            return false;
        }
    }
    if(!goods_id){

        return false;
    }





    addLoadingActionBtn('.form-btn');

    $.ajax({
        type: $('#order-form').attr('method'),
        url: $('#order-form').attr('action'),
        data: $('#order-form').serialize(),
        dataType: "json",
        success: function(data){
            observer_order('order-form',data.observer,function () {
                window.location.href = "/order/"+data.data.id;
            });
        },
        error:function(jqXHR, textStatus, errorThrown){
            var response = JSON.parse(jqXHR.responseText)
            Swal.fire({
                title: '訂單提交失敗!',
                text: response.msg,
                icon: 'error',
                confirmButtonText: '我知道了'
            })
            closeLoadingActionBtn('.submit-btn');
        }
    });
    return false;
}

function orderCheck(){

    if($('.form-btn').attr('disabled')){
        return false;
    }

    var email = $("input[name='email']").val();
    var phone = $("input[name='phone']").val();


    if(!email){
        $("input[name='email']").focus();
        $('input[name="email"]').contip({
            align: 'bottom',
            html: '請填寫訂購人姓名',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }
    if(!phone){
        $("input[name='phone']").focus();
        $('input[name="phone"]').contip({
            align: 'bottom',
            html: '請填寫訂購電話',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }
    if(!(/^09\d{8}$/.test(phone))){
        $("input[name='phone']").focus();
        $('input[name="phone"]').contip({
            align: 'bottom',
            html: '電話格式有誤',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }




    addLoadingActionBtn('.form-btn');

    $.ajax({
        type: $('#check-form').attr('method'),
        url: $('#check-form').attr('action'),
        data: $('#check-form').serialize(),
        dataType: "json",
        success: function(data){
            if(data.code == 200){
                window.location.href = data.jump;
            }else{

                Swal.fire({
                    title: '查詢失敗!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: '我知道了'
                })
            }
            closeLoadingActionBtn('.form-btn');
            $("input[name='email']").val('');
            $("input[name='phone']").val('');
            $("input[name='captcha_code']").val('');
            $("img.captcha").click()
        },
        error:function(jqXHR, textStatus, errorThrown){
            var response = JSON.parse(jqXHR.responseText)
            Swal.fire({
                title: '查詢失敗!',
                text: response.message,
                icon: 'error',
                confirmButtonText: '我知道了'
            })
            closeLoadingActionBtn('.form-btn');
        }
    });
    return false;

}

function messageStore(){
    var name = $("input[name='name").val();

    var email = $("input[name='email']").val();
    var content = $("textarea[name='content']").val();
    if(!name){
        $('input[name="name"]').focus();
        $('input[name="name"]').contip({
            align: 'bottom',
            html: '請填寫您的昵稱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }

    if(!email){
        $("input[name='email']").focus();
        $('input[name="email"]').contip({
            align: 'bottom',
            html: '請填寫您的電子郵箱',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }
    if(email.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){

        $("input[name='email']").focus();
        $('input[name="email"]').contip({
            align: 'bottom',
            html: '電子郵箱格式錯誤',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }

    if(!content){

        $("textarea[name='content']").focus()
        $('textarea[name="content"]').contip({
            align: 'bottom',
            html: '請填寫留言内容',
            fade: 360,
            opacity:0.6,
            delay_out:100,
            trigger:'focus',
            auto_close: 3000,
            repeat:false,
        }).show();
        return false;
    }

    if($('.form-btn').attr('disabled')){
        return false;
    }
    addLoadingActionBtn('.form-btn');

    $.ajax({
        type: $('#message-form').attr('method'),
        url: $('#message-form').attr('action'),
        data: $('#message-form').serialize(),
        dataType: "json",
        success: function(data){
            if (data.code == 200){
                observer_message('message-form',data.observer,function () {
                    Swal.fire({
                        title: data.msg,
                        text: data.sub_msg,
                        icon: 'success',
                        confirmButtonText: '我知道了'
                    })

                    $("input[name='name").val('');
                    $("input[name='phone']").val('');
                    $("input[name='email']").val('');
                    $("textarea[name='content']").val('');
                    closeLoadingActionBtn('.form-btn')
                });
                closeLoadingActionBtn('.form-btn');
            }else{
                Swal.fire({
                    title: data.msg,
                    text: data.sub_msg,
                    icon: 'error',
                    confirmButtonText: '我知道了'
                })
                closeLoadingActionBtn('.form-btn');
            }

        },
        error:function(jqXHR, textStatus, errorThrown){
            var response = JSON.parse(jqXHR.responseText)

            Swal.fire({
                title: "留言失敗!",
                text: response.message,
                icon: 'error',
                confirmButtonText: '我知道了'
            })
            closeLoadingActionBtn('.form-btn');
        }
    });
    return false;
}


function warnInfo(elem,text){
    var left = parseInt(elem.parent().css('width'))+10;
    elem.css('color',"rgb(239, 60, 60)");
    elem.text(text);
}


$("input,textarea").blur(function(){
    $(this).attr('placeholder',$(this).attr('x-placeholder'));
    $(this).removeAttr('x-placeholder');
});

$("input,textarea").focus(function(){
    $(this).attr('x-placeholder',$(this).attr('placeholder'));
    $(this).removeAttr('placeholder');
})


function submit(elem,options={}){
    var _this = $(elem);
    if(!options.dataType){
        options.dataType = 'json';
    }

    _this.ajaxForm({
        url: _this.attr('action'), //提交地址：默认是form的action,如果申明,则会覆盖
        type: _this.attr('method'),   //默认是form的method（get or post），如果申明，则会覆盖
        beforeSubmit: function(){

            var validate_result = true;
            var error_validate_message;
            var error_validate_elem;
            if(options.validate){
                validate_result = options.validate()
            }
            var request_data = {};
            _this.find('input,select,textarea').each(function () {
                var $this = $(this);
                var validate = $this.attr('data-validate');

                //检查条件
                var condition = $this.attr('data-validate-condition');
                var is_adopt_condition = true;
                if(condition){
                    var wheres = condition.split(':');
                    if(wheres[0]){
                        var where_val = request_data[wheres[0]];
                        if(where_val != wheres[1]){
                            is_adopt_condition = false;
                        }
                    }
                }

                var name = $this.attr('name');
                var val
                if($this.attr('type') ==='radio'){
                    val = _this.find('input[name="'+name+'"]:checked').val();
                }else{
                    val = $this.val()
                }
                request_data[name] = val;

                if(validate && is_adopt_condition && validate_result === true){




                    var rules = validate.split('|');
                    for(var i = 0;i < rules.length;i++){



                        var rule_untreated = rules[i];
                        if(rule_untreated && validate_result === true){
                            var rule_compose = rule_untreated.split(':')
                            var rule = rule_compose[0]

                            switch (rule) {
                                case "required":
                                    if(!val){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "必填字段不能爲空"
                                        }
                                        validate_result = false;

                                    }
                                    break;
                                case "mobile":
                                    if(!(/^09\d{8}$/.test(val))){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "聯絡電話格式錯誤"
                                        }
                                        validate_result = false;
                                    }
                                    break
                                case "email":
                                    if(val.search(/^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.(?:com|cn|tw|info|net)$/) == -1){
                                        if(rule_compose[1]){
                                            error_validate_message = rule_compose[1];
                                        }else{
                                            error_validate_message = "郵箱格式錯誤"
                                        }
                                        validate_result = false;
                                    }
                                    break
                                default:
                            }
                            error_validate_elem = $this;
                        }

                    }
                }

            });


            if(validate_result === false){
                if(error_validate_elem){
                    $(error_validate_elem).focus()
                }

                Swal.fire({
                    icon:'error',
                    iconColor:'#fff',
                    text: error_validate_message,
                    color:'#fff',
                    background:'rgba(0,0,0,0.7)',
                    width:'auto',
                    backdrop:false,
                    timer:1000,
                    timerProgressBar:false,
                    showConfirmButton:false,
                })

                return false;
            }


            if(options.before){
                options.before();
            }else{
                Swal.fire({
                    width:"50%",
                    text: "正在處理中",
                    backdrop:true,
                    allowOutsideClick:false,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                })
            }

            _this.find('button').attr('disabled','disabled')

        }, //提交前的回调函数
        success: function(result){

            if(result.redirect){

                window.location.href = result.redirect;
            }else{
                if(options.success){
                    options.success(result);
                }else{
                    var swal_options = {};
                    swal_options.iconColor = "#fff";
                    if(result.title){
                        swal_options.title = result.title;
                    }
                    if(result.message){
                        swal_options.text = result.message;
                    }
                    swal_options.background = "rgba(0,0,0,1)";
                    swal_options.width = "auto";
                    swal_options.backdrop = "false";
                    swal_options.timer = 2000;
                    swal_options.showConfirmButton = false;
                    swal_options.color = "#fff";
                    if(result.status == true){
                        swal_options.icon = 'success'
                    }else{
                        swal_options.icon = 'error'
                    }
                    Swal.fire(swal_options)
                    _this.clearForm()
                }
            }

        },
        error:function(XMLHttpRequest){
            var error = XMLHttpRequest.responseJSON;
            Swal.fire({
                icon:'error',
                iconColor:'#fff',
                text: error.message,
                color:'#fff',
                background:'rgba(0,0,0,0.7)',
                width:'auto',
                backdrop:false,
                timer:1000,
                timerProgressBar:false,
                showConfirmButton:false,
            })
        },
        complete:function(XMLHttpRequest){
            _this.find('button').removeAttr('disabled')
            var res = XMLHttpRequest.responseJSON;
            _this.find("input[name='_token']").val(res._token)
            _this.find("img.captcha").click()

        },
        dataType: options.dataType, //html(默认), xml, script, json...接受服务端返回的类型
        //clearForm: true,  //成功提交后，是否清除所有表单元素的值
    })
    return false;

}

function addLoadingActionBtn(elem){
    var load = '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:50px;height:11px;" viewBox="0 0 120 30" fill="currentColor"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite"></animate></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"></animate><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite"></animate></circle></svg>'
    $(elem).attr('data-tmp-text',$(elem).html());
    $(elem).html(load);
    $(elem).addClass('loading-action-btn-disabled');
    $(elem).attr('disabled','disabled');
}

function closeLoadingActionBtn(elem){
    $(elem).removeClass('loading-action-btn-disabled')
    $(elem).removeAttr('disabled');
    $(elem).html($(elem).attr('data-tmp-text'));
}
