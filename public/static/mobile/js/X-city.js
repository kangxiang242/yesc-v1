var cityData = province;

var is_show = 0;

var province_id = 0;

var region_id = 0;

var street_id = 0;

var obj;

var callback = null;

$('body').on('click','.x-select .province',function(){
	var province = new choiceProvince($(this));
		obj.val(province.name);
		province_id = province.id;
		
		var RegionBox = new createRegionBox(province.id);
		$('.ul-select-content').html(RegionBox.tmp);
});

$('body').on('click','.x-select .region',function(){
	var province = new choiceProvince($(this));
	obj.val(obj.val()+'/'+province.name);
	region_id = province.id;
	
	var StreetBox = new createStreetBox(province_id,region_id);

	$('.ul-select-content').html(StreetBox.tmp);

});

$('body').on('click','.x-select .street',function(){
	var province = new choiceProvince($(this));
	obj.val(obj.val()+'/'+province.name);
	region_id = province.id;
	$('.x-select').remove();
	is_show=0;
	if(callback){
		callback();
	}
	
});




 var Xcity = function(input,call){

	if(is_show == 0){
		var box = new createCityBox();
		obj = input;
		input.after(box.tmp);
		is_show = 1;
		callback = call;
	}
} 


var createCityBox = function(province_id,region_id){

	var tmp = '<div  class="x-select"><ul class="ul-select-content">';
	for(var i=0;i<cityData.length;i++){
		tmp += '<li><a class="province" data-id="'+cityData[i].id+'" href="javascript:;">'+cityData[i].name+'</a></li>';
		
	}
	tmp += '</ul><div>';

	this.tmp = tmp;
}


var createRegionBox = function(province_id){
	var tmp = '';
	for(var i=0;i<cityData.length;i++){
		if(province_id && cityData[i].id == province_id){
			var son = cityData[i].son;

			for(var j=0;j<son.length;j++){
				tmp += '<li><a class="region" data-id="'+son[j].id+'" href="javascript:;">'+son[j].name+'</a></li>';
			}

			break;
		}
	}
	this.tmp = tmp;
}

var createStreetBox = function(province_id,region_id){

	var tmp = '';
	for(var i=0;i<cityData.length;i++){
		if(province_id && cityData[i].id == province_id){
			var son = cityData[i].son;
	
			for(var j=0;j<son.length;j++){
				
				if(region_id && son[j].id == region_id){
					var sec = son[j].sec;
					for(var k=0;k<sec.length;k++){
						tmp += '<li><a class="street" data-id="'+sec[k].id+'" href="javascript:;">'+sec[k].name+'</a></li>';
					}
					
					break;
				}
				

			}

			break;
		}
	}

	this.tmp = tmp;
}



var choiceProvince = function(obj){
	var city_id = obj.attr('data-id');
	var name = obj.text();
	this.id = city_id;
	this.name = name;
	
}

var choiceRegion = function(obj){
	
}