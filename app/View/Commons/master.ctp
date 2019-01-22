
<?php echo $this->fetch('extra'); ?>

<div id="mk-imgs-list">
    <div id="mk-list-header">
        <div id="btns">
        <input type="button"  id="mk-bt-upload" class="img-ope" value="上传图片" style="display: none;width: 100px" />
        <input type="button"  id="mk-bt-add-fold" value="新建相册" class="fold-ope" style="width: 100px" />
        <input type="button"  id="mk-bt-del-folds" value="删除相册" class="fold-ope" style="display: none;width: 100px" />
         <input type="button"  id="mk-bt-del-images" class="img-ope" value="删除图片" style="display: none;width: 100px" />
         <input type="button"  id="mk-bt-move-folds" value="移动相册" class="fold-ope" style="display: none;width: 100px" />
         <input type="button"  id="mk-bt-move-images" class="img-ope" value="移动图片" style="display: none;width: 100px" />
        </div>
        <div  id="mk-items-move-div">
            请从左侧选择移动到达的目标相册: <br/>
            <input type="input" name="target_fold" id="mk-target-fold" readonly="readonly" style="width:500px"/>
            <input type="hidden" name="target_fold_id" id="mk-target-fold-id" value="0" />
            或者<a href="#" id="mk-select-root-a">选择图片根目录</a><br/>
            <input type="button" name="confirm_move" id="mk-confirm-move" value="确定"/>
            <input type="button" name="cancel_move" id="mk-cancel-move" value="取消"/>
        </div>
    </div>
    <div id="mk-list-content" data-toggle="modal-gallery" data-target="#modal-gallery" data-selector="div.gallery-item">
       
    </div>

</div>



<div id="dialog-message" title="上传图片" style="display: none">
    <div class="container">
        <div class="page-header">       
            <blockquote>
                <p>上传图片前，请确保进入你要放入图片的目录。请选择'jpg|jpeg|png|gif'格式的图片，每个图片大小不得超过1M。同时上传文件数不得超过100。 </p>
            </blockquote>
        </div>
        <!-- The file upload form used as target for the file upload widget -->
        <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
            <!-- Redirect browsers with JavaScript disabled to the origin page -->
            <noscript><input type="hidden" name="redirect" value="http://blueimp.github.com/jQuery-File-Upload/"></noscript>
            <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
            <div class="row fileupload-buttonbar">
                <div class="span7">
                    <!-- The fileinput-button span is used to style the file input field as button -->
                    <span class="btn btn-success fileinput-button">
                        <i class="icon-plus icon-white"></i>
                        <span>添加图片...</span>
                        <input type="file" name="files[]" multiple>
                    </span>
                    <button type="submit" class="btn btn-primary start">
                        <i class="icon-upload icon-white"></i>
                        <span>上传全部</span>
                    </button>
                    <button type="reset" class="btn btn-warning cancel">
                        <i class="icon-ban-circle icon-white"></i>
                        <span>取消所有上传</span>
                    </button>
                  
                    <button type="button" class="btn btn-danger delete">
                        <i class="icon-trash icon-white"></i>
                        <span>删除项目</span>
                    </button>
                    <input type="checkbox" class="toggle">
                    <!-- The loading indicator is shown during file processing -->
                    <span class="fileupload-loading"></span>
                </div>
                <!-- The global progress information -->
                <div class="span5 fileupload-progress fade">
                    <!-- The global progress bar -->
                    <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                        <div class="bar" style="width:0%;"></div>
                    </div>
                    <!-- The extended global progress information -->
                    <div class="progress-extended">&nbsp;</div>
                </div>
            </div>
            <!-- The table listing the files available for upload/download -->
            <table role="presentation" class="table table-striped" data-toggle="modal-gallery" data-target="#modal-gallery" data-selector="div.up-item"><colgroup>
                    <col style="width: 12%;" />
                    <col style="width: 50%;" />
                    <col style="width: 14%;" />
                    <col style="width: 22%;" />

                </colgroup><tbody class="files" ></tbody></table>
        </form> 
    </div>
</div>


<div id="dialog-fold" title="创建新相册?" style="display: none">
     <p>
	 <span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>
      输入新相册的名称，请不要包含特殊字符，如: /，&，%，....
	 </p>
     <p><span style="float:left">相册名字:</span> <input type="text" name="fold_name" id="mk-fold-name" style="float:left; margin-top:8px"/></p>
     <p class="mk-tips" style="display:none"></p>
</div>

<div id="dialog-confirm-delete" title="Empty the recycle bin?"  style="display: none">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>These items will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="dialog-common" title="操作提示?"  style="display: none">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>  <span id="dialog-content"></span></p>
</div>
<div id="dialog-confirm" title="确认操作?"  style="display: none">
  <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>  <span id="dialog-content"></span></p>
</div>

<div class="mk-msg-tip">
   <div class="toast-outer">
     <div class="toast-msg ellipsis" >
</div>
</div>
</div>