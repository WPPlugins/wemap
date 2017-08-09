  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-100095262-1', 'auto');
  ga('send', 'pageview');


jQuery(function() {
function api_get(uri, callback){
    jQuery.ajax({
      type: "GET",
      url: wemapenv.api + uri,
      dataType: 'json',
      async: false,
      headers: {
        "Authorization": "Bearer " + Cookies.get('oauth2_token_wemap')
      },
      success: callback
    });
}



jQuery('#list-pinpoints').change(function() {
    document.getElementById('catpoint_pick').style.display = 'block';
    if (document.getElementById('list-pinpoints').value == '2') {
        document.getElementById('catpoint_pick').style.display = 'none';
    }
});

jQuery('#choice-insert').change(function() {
    var targetElement = document.getElementById('choice-insert');
    var lists = document.getElementById('choice-insert-lists');
    if (targetElement.value == '') {
        lists.style.display = 'none';
    } else if (targetElement.value == 'lists') {
        lists.style.display = 'inline';
    }
});

window.wemap_getCookie = function(cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return '';
}

jQuery('#dialog').dialog({
    autoOpen: false,
    show: {effect: 'blind', duration: 1000},
    hide: {effect: 'clip', duration: 1000}
});

jQuery('#opener').click(function() {
    jQuery('#dialog').dialog('open');
    if(jQuery("#dialog input").length > 0 )
        return;

    api_get('/v3.0/pinpoints/categories', function(data){
        data.forEach( function(category){
            jQuery('#dialog').append('<input class="img_category" type="image" src="'+ category.image_url +'" value="'+ category.id +'" style="height:39px;" />');
        });

        jQuery('.img_category').click(function() {
            jQuery('#id_cat').val(this.value);
            jQuery('#id_cat').attr('src', this.src);

            document.getElementById('opener').src = this.src;
            jQuery('#dialog').dialog('close');
        });
    });
});

jQuery('#choice-insert,#list-livemaps').click(function(){
    if(jQuery("#list-livemaps-pinpoint option").length > 0 
        || jQuery("#list-livemaps option").length > 1)
        return;

    api_get('/v3.0/livemaps?limit=50', function(data) {
        data.results.forEach( function(livemap){
            jQuery('#list-livemaps').append('<option value="'+ livemap.id +'">'+ livemap.name +'</option>');
            jQuery('#list-livemaps-pinpoint').append('<option value="'+ livemap.id +'">'+ livemap.name +'</option>');
        });
    });
    api_get('/v3.0/lists?user='+ wemapenv.user +'&limit=400', function(data){
        data.results.forEach( function(list){
            jQuery('#list-lists-pinpoint').append('<option value="'+ list.id +'">'+ list.name +'</option>');
        });
    });
});

function api_save_pinpoint(pinpoint) {
    jQuery.ajax({
      type: "POST",
      url: wemapenv.api + '/v3.0/pinpoints',
      contentType: "application/json;charset=UTF-8",
      dataType: 'json',
      data: JSON.stringify(pinpoint),
      async: false,
      headers: {
        "Authorization": "Bearer " + Cookies.get('oauth2_token_wemap')
      },
      success: function pinpoint_created(data) {
            jQuery('#new_pinpoint_id').val(data.id);
            jQuery('#preview_pp').attr('src', wemapenv.livemap + 'enabledcontrols=false&ppid=' + data.id);
        }
    });
};

jQuery('#btn_wemap_save').click(function(){
    pinpoint = {
        "latitude": parseFloat(jQuery('#pinpoint_latitude').val()),
        "longitude": parseFloat(jQuery('#pinpoint_longitude').val()),
        "category": parseInt(jQuery('#id_cat').val()),
        "name": jQuery('input[name="post_title"]').val(),
        "tags": jQuery('#tax-input-post_tag').val().split(',')
    };

    if (jQuery('#image_picpoint')[0].files.length > 0){
        file = jQuery('#image_picpoint')[0].files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            bits = e.target.result.split(',', 2)
            pinpoint.media_file = {
                "content": bits[1],
                "name": file.name,
                "type": file.type
            }
            api_save_pinpoint(pinpoint);
        };
        reader.readAsDataURL(file);
        return;
    }

    api_save_pinpoint(pinpoint);
});



});

