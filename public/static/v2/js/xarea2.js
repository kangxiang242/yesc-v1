var city_option = '<option value="">--選擇縣市--</option>';
for (i in province)
{
    city_option += '<option value="'+province[i].name+'">'+province[i].name+'</option>'
}
$('#city').html(city_option);

$('#city').change(function(){
    var city = $(this).val();
    var region = getRegion(city);
    var region_option = '<option value="">--選擇區域--</option>';
    for (i in region)
    {
        region_option += '<option value="'+region[i].name+'">'+region[i].name+'</option>'
    }

    $('#region').html(region_option);
});

function getRegion(city){
    for (i in province)
    {
        if(city == province[i].name){

            return province[i].son;
        }

    }
}