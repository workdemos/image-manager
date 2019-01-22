<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>
        <div id="login_dialog"></div>
        <script id="template-upload" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-upload fade">
            <td>
            <span class="preview"></span>
            </td>
            <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
            <div><span class="label label-important">错误</span> {%=file.error%}</div>
            {% } %}
            </td>
            <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            {% } %}
            </td>
            <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
            <button class="btn btn-primary start">
            <i class="icon-upload icon-white"></i>
            <span>上载</span>
            </button>
            {% } %}
            {% if (!i) { %}
            <button class="btn btn-warning cancel">
            <i class="icon-ban-circle icon-white"></i>
            <span>取消</span>
            </button>
            {% } %}
            </td>
            </tr>
            {% } %}
        </script>
        <!-- The template to display files available for download -->
        <script id="template-download" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
            <tr class="template-download fade">
            <td>
            <span class="preview">
            {% if (file.thumbnail_url) { %}
            <img data-href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery="#gallery-uploaded" src="{%=file.thumbnail_url%}">
            {% } %}
            </span>
            </td>
            <td>
            <p class="name">
            {% if (file.url) { %}
            <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            {% } else { %}
            <span>{%=file.name%}</span>
            {% } %}
            </p>
            {% if (file.error) { %}
            <div><span class="label label-danger">错误</span> {%=file.error%}</div>
            {% } %}
            </td>
            <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
            {% if (file.name) { %}
            <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
            <i class="glyphicon glyphicon-trash"></i>
            <span>删除条目</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
            <button class="btn btn-warning cancel">
            <i class="glyphicon glyphicon-ban-circle"></i>
            <span>取消</span>
            </button>
            {% } %}
            </td>
            </tr>
            {% } %}
        </script>

        <script  id="template-image-list" type="text/x-tmpl">
            {% if(o.track.length > 0){ %}
            <div id="mk-item-list-navi">
            <a href="parent" class="mk-fold-return"    data-ppid="{%=o.ppid%}">返回上一级</a> | 
            <a href="root"  data-ppid="0" id="fold_0" class="mk-list-navi-a">全部相册</a>
            {% for (var i=0; i<o.track.length;i++){ %}
            {% if(i !==o.track.length-1){ %}
            » <a href="{%=o.track[i].Speedfold.name%}"  data-ppid="{%=o.track[i].Speedfold.id%}" id="fold_{%=o.track[i].Speedfold.id%}" class="mk-list-navi-a">{%=o.track[i].Speedfold.name%} </a>
            {% }else{ %}
            » {%=o.track[i].Speedfold.name%}
            {% } %}
            {% } %}
            </div>
            {% } %}
          
            {% if(o.images.length==0){ %}
            <div class="mk-empty-fold-tips">空相册。还没有上传图片？  请先建立相册或者上传图片。如果上传图片，该目录将不能再建子目录；如果建立了子目录，改相册是不能上传图片的，但是还是可以建立其他子目录！</div>
            {% }else{ %}

            {% for (var i=0; i<o.images.length;i++){ %}
            <div class="mk-speed-img gallery-item" 
            id="img_{%=o.images[i].Image.id%}"             
            data-foldid="{%=o.images[i].Image.fold_id%}" 
            data-imgid="{%=o.images[i].Image.id%}"
            data-href="{%=o.images[i].Image.uri%}" data-gallery="#blueimp-gallery-imgs"    title="{%=o.images[i].Image.title%}">            
            <image  src="{%=o.images[i].Image.uri%}_130x130.jpg" width="130" height="130" />        

            <div class="mk-img-ope"><a class='del'>删除</a> <a class='move'>移动</a> </div>      
            <span class="mk-actived-icon">&nbsp;</span>
            </div>
            {% } %}
            <div id="mk-list-paging" >               
            <p>
            <a href="#" data-pid="{%=o.pid%}" class="mk-refresh-fold" data-sub="0">刷新当前目录</a>
            |        
            {%=o.pages.count%}
            </p>
            <div class="paging">
            <span class="mk-items-ar"><input type="checkbox" id="mk-sel-all" value="1">全选</span>
            <span class="mk-items-ar"><input type="checkbox" value="2" id="mk-sel-reverse">反选</span>
            {%#o.pages.first%}
            {%#o.pages.prev%}
            {%#o.pages.numbers%}
            {%#o.pages.next%}
            {%#o.pages.last%}                
            </div>
            </div> 
            {% } %}
        </script>

        <script  id="template-image-list-table" type="text/x-tmpl">
            {% if(o.track.length > 0){ %}
            <div id="mk-item-list-navi">
              返回上一级 | 
            <a href="root" data-sub="false" data-foldid="0" id="fold_0" class="mk-list-navi-a">Root</a>
            {% for (var i=0; i<o.track.length;i++){ %}
            {% if(i !==o.track.length-1){ %}
            » <a href="{%=o.track[i].Speedfold.name%}" data-sub="false" data-foldid="{%=o.track[i].Speedfold.id%}" id="fold_{%=o.track[i].Speedfold.id%}" class="mk-list-navi-a">{%=o.track[i].Speedfold.name%} </a>
            {% }else{ %}
            » {%=o.track[i].Speedfold.name%}
            {% } %}
            {% } %}
            </div>
            {% } %}
           
            {% if(o.images.length==0){ %}
            <div class="mk-empty-fold-tips">空相册。还没有上传图片？  请先建立相册或者上传图片。如果上传图片，该目录将不能再建子目录；如果建立了子目录，改相册是不能上传图片的，但是还是可以建立其他子目录！</div>
            {% }else{ %}

            {% for (var i=0; i<o.images.length;i++){ %}
            <div class="mk-speed-img gallery-item" 
            id="img_{%=o.images[i].Image.id%}"                    
            data-imgid="{%=o.images[i].Image.id%}"
            data-href="{%=o.images[i].Image.uri%}" data-gallery="#blueimp-gallery-imgs"    title="{%=o.images[i].Image.title%}">            
            <image  src="{%=o.images[i].Image.uri%}_30x30.jpg" width="30" height="30" />        

            <div class="mk-img-ope"><a class='del'>删除</a> <a class='move'>移动</a> </div>      
            <span class="mk-actived-icon">&nbsp;</span>
            </div>
            {% } %}
            <div id="mk-list-paging" >               
            <p>
            <a href="#" data-pid="{%=o.pid%}" class="mk-refresh-fold" data-sub="0">刷新当前目录</a>
            |        
            {%=o.pages.count%}
            </p>
            <div class="paging">
            <span class="mk-items-ar"><input type="checkbox" id="mk-sel-all" value="1">全选</span>
            <span class="mk-items-ar"><input type="checkbox" value="2" id="mk-sel-reverse">反选</span>
            {%#o.pages.first%}
            {%#o.pages.prev%}
            {%#o.pages.numbers%}
            {%#o.pages.next%}
            {%#o.pages.last%}                
            </div>
            </div> 
            {% } %}
        </script>
        
        <script  id="template-fold-list" type="text/x-tmpl">
            {% if(o.track.length > 0){ %}
            <div id="mk-item-list-navi">
           <a href="parent" class="mk-fold-return"  data-ppid="{%=o.ppid%}">  返回上一级</a> | 
            <a href="root"  data-ppid="0" id="fold_0" class="mk-list-navi-a">全部相册</a>
            {% for (var i=0; i<o.track.length;i++){ %}
            {% if(i !==o.track.length-1){ %}
            » <a href="{%=o.track[i].Speedfold.name%}" data-ppid="{%=o.track[i].Speedfold.id%}" id="fold_{%=o.track[i].Speedfold.id%}" class="mk-list-navi-a">{%=o.track[i].Speedfold.name%} </a>
            {% }else{ %}
            » {%=o.track[i].Speedfold.name%}
            {% } %}
            {% } %}
            </div>
            {% } %}          

            {% if(o.folds.length>0){ %}
            {% for (var i=0; i<o.folds.length;i++){ %}
            <div class="mk-speed-img" id="fold_{%=o.folds[i].Speedfold.id%}" 
            data-foldid='{%=o.folds[i].Speedfold.id%}' 
            data-sub='{%=o.folds[i].Speedfold.has_child%}'
            title='{%=o.folds[i].Speedfold.name%}'> 
            {% if(o.folds[i].Speedfold.cover !==""){ %}
            <image src="{%=o.folds[i].Speedfold.cover%}_130x130.jpg" width="130" height="130" />                   
            {% }else{ %}
            <img  src="/img/Folder.png" />                   
            {% } %}                     
            <span class="mk-fold-cover">&nbsp;</span>
            <span id="mk-img-title">{%=o.folds[i].Speedfold.name%}</span>
            <div class="mk-img-ope"><a class='del'>删除</a> <a class='move'>移动</a></div>     
            <span class="mk-actived-icon">&nbsp;</span>
            </div>
            {% } %}

            <div id="mk-list-paging" >           
            <p>
            <a href="#" data-pid="{%=o.pid%}" class="mk-refresh-fold" data-sub="1">刷新当前目录</a>
            |
            {%=o.pages.count%}
            </p>
            <div class="paging"> 
            <span class="mk-items-ar"><input type="checkbox" id="mk-sel-all" value="1">全选</span><span class="mk-items-ar"><input type="checkbox" value="2" id="mk-sel-reverse">反选</span>
            {%#o.pages.first%}
            {%#o.pages.prev%}
            {%#o.pages.numbers%}
            {%#o.pages.next%}
            {%#o.pages.last%}                
            </div>
            </div> 
            {% }else{ %}
            {% if(o.pid ==0){ %}
            <div class="mk-empty-fold-tips">从这里开始吧！</div>
            {% } %}
            {% } %}
        </script>

        <script id="template-tree" type="text/x-tmpl">
            <ul>
            {% if(o.folds.length>0){ %}
            {% for(var i=0; i<o.folds.length; i++){  %}
            <li id="rhtml_{%=o.folds[i].Speedfold.id%}" 
            dataid="{%=o.folds[i].Speedfold.id%}"
            dataname="{%=o.folds[i].Speedfold.name%}"
            sub="{%=o.folds[i].Speedfold.has_child  %}" 
            class="{% if(o.folds[i].Speedfold.has_child==1){ %}jstree-closed{% }else{ %} jstree-leaf {% } %}  {% if(i ==o.folds.length){ %} jstree-last{% } %}">
            <a href="#">{%=o.folds[i].Speedfold.name%}</a>
            </li>
            {% } %}
            {% }else{ %}
            <li id="rhtml_0" dataid="0" dataname="root" sub="1" class="jstree-leaf jstree-last"><a href="#">Root</a>
            {% } %}            
            </ul>
        </script>

        <script id="template-fold" type="text/x-tmpl">
            <div data-sub="false" id="fold_{%=o.fold.id%}" data-foldid="{%=o.fold.id%}" class="mk-speed-img"> 
            <img src="/img/Folder.png">
            <span id="mk-img-title">{%=o.fold.name%}</span>
            <div class="mk-img-ope" style="display: none;"><a>删除</a> <a>移动</a> <a>edit</a></div>           
            </div>
        </script>