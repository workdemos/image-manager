<div id="mk-login-form">
<h2>用户登录</h2>
<?php echo $this->Session->flash('auth'); ?>

<div id='to_login_area' ></div>
</div>


<script>
    $(function(){
        var global = {};
        function clearPageCache(){
	var xhr=$.getJSON('/users/pageCache.json',function(json){	
		for(var e in json.data)
		{ 
                                                     global[e] = json.data[e]; //全局变量o
			if(json.data[e])
				$('.' + e).show();
			else $('.' + e).hide();
		}            
	});
          return xhr;
}
         function show_login(float,target) {
             $(target).html("<img src='/img/throbber.gif' />");
        $.getJSON(global.pc_login + "/login?callback=?", {a: "removeLoginDiv", login_url: global.pc_host +  "/users/login"})
                .done(function(json) { 
            
            if(float){
                $("#login_dialog").html(json.content).dialog({modal: true ,
                position: {my: "top-2", at: "top", of: window},
                draggable: false,
                dialogClass: "login-float",
                width: 700,
                height: 300,
                resizable: false,
                show: 'blind'});
            }else{
             $(target).html(json.content);
            }
        })
                .fail(function(jqxhr, textStatus, error) {
            var err = textStatus + ', ' + error;
            console.log("Request Failed: " + err);
        });
    }
    var xhr =  clearPageCache();
    xhr.done(function(){  show_login(0,"#to_login_area"); });    });
  
</script>