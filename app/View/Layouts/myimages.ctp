<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$cakeDescription = __d('cake_dev', 'Speed-Trade 图片管理');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $cakeDescription ?>:
            <?php echo $title_for_layout; ?>
        </title>
        <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->Html->css('cake.generic');
        echo $this->Html->css('sm/jquery-ui-1.10.3.custom.min');

        echo $this->Html->script('jquery-1.9.1');
        echo $this->fetch('script');
        ?>
        <!-- Bootstrap CSS Toolkit styles -->
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <!-- Bootstrap styles for responsive website layout, supporting different screen sizes -->
        <link rel="stylesheet" href="/css/bootstrap-responsive.min.css">
        <!-- Bootstrap CSS fixes for IE6 -->
        <!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
        <!-- Bootstrap Image Gallery styles -->
        <link rel="stylesheet" href="/css/blueimp-gallery.min.css">
        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
        <link rel="stylesheet" href="/css/jquery.fileupload-ui.css">
        <!-- CSS adjustments for browsers with JavaScript disabled -->
        <noscript><link rel="stylesheet" href="/css/jquery.fileupload-ui-noscript.css"></noscript>
       
        <?php echo $this->fetch('css'); ?>
         <link rel="stylesheet" href="/css/speed_imgs.css">
    </head>
    <body>
        <div id="container">
            <div id="header">
                <p class="jtitle"><?php echo $this->Html->link($cakeDescription, '/'); ?></p>


                <div class="mk-login-div">
                    你好，<?php echo $this->Session->read('Auth.User.m_maker_account'); ?> ! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <a href="/users/logout" >退出</a>   | 
                    <a target="_blank" href="<?php echo Configure::read("maker_admin_url"); ?>?ssid=<?php echo $this->Session->read('Auth.User.ssid'); ?>&m=<?php echo $this->Session->read('Auth.User.account'); ?>&s_t=<?php echo $this->Session->read('Auth.User.token'); ?>">
                        登录到我的商家后台
                    </a>

                </div>              
            </div>

            <!--            <div class="tips-start">
                        <div class="mk-scroll-tips" >
                            <ul>
                                <li>单击选择</li>
                                <li>双击相册进入下一层</li>
                                <li>双击图片，打开幻灯片</li>                   
                            </ul>
                        </div>
                        </div>-->
            <div id="content">

                <div id="mk-left-panel">
                    <div id="mk-tree" class="mk-tree">

                    </div>
                </div>
                <div id="mk-right-panel"><?php echo $this->Session->flash(); ?>

                    <?php echo $this->fetch('content'); ?>
                </div>

            </div>
            <div id="footer">
                Power By Speed-Trade Tech Dev.
                <?php
                //    echo $this->Html->link($this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')), '/', array('target' => '_blank', 'escape' => false) );
                ?>
            </div>
        </div>
        <?php
        //echo $this->element('sql_dump'); 
        ?>

        <?php echo $this->element('js_templates'); ?>

        <script src="/js/jquery-ui-1.10.3.custom.min.js"></script>
        <script src="/js/jquery.history.js"></script>
        <script src="/js/speed_imgs.js"></script>
        <script src="/js/blueimp-gallery.min.js"></script>       
        <script src="/js/jquery.blueimp-gallery.js"></script>
        <script src="/js/jquery.jstree.js"></script>         
        <script src="/js/tmpl.js"></script>      
        <script src="/js/load-image.min.js"></script>      
        <script src="/js/canvas-to-blob.min.js"></script>
        <script src="/js/jquery.fileupload.js"></script>
        <script src="/js/jquery.iframe-transport.js"></script>     
        <script src="/js/jquery.fileupload-process.js"></script>     
        <script src="/js/jquery.fileupload-image.js"></script>     
        <script src="/js/jquery.fileupload-validate.js"></script>       
        <script src="/js/jquery.fileupload-ui.js"></script>
        <script src="/js/!script.js"></script>
    </body>
</html>
