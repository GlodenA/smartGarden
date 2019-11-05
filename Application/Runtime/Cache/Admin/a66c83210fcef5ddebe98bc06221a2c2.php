<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="Ch">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="bookmark" href="/favicon.ico" />
    <title><?php echo C('WEB_NAME');?></title>

    <!-- CSS -->
    <!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500"> -->
    <link rel="stylesheet" href="<?php echo C('ADMIN_CSS_PATH');?>/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo C('ADMIN_CSS_PATH');?>/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo C('ADMIN_CSS_PATH');?>/login.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <!-- <link rel="shortcut icon" href="/WFGarden/Public/Admin/ico/favicon.png"> -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/WFGarden/Public/Admin/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/WFGarden/Public/Admin/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/WFGarden/Public/Admin/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/WFGarden/Public/Admin/ico/apple-touch-icon-57-precomposed.png">

</head>

<body>

<!-- Top content -->
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text">
                    <h1><strong><?php echo C('WEB_NAME');?></strong> 后台管理</h1>
                    <div class="description">
                    <!--
                        <p>
                            山东省数字证书认证管理有限公司 <a href="http://www.sdca.com.cn/"><strong>http://www.sdca.com.cn/</strong></a>
                        </p>
                    </div>
                    -->
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>管理员登录</h3>
                            <p>请输入正确的帐号密码进行登录:</p>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-lock"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <form role="form" action="" method="post" class="login-form" id="form" onsubmit="return false;">
                            <div class="form-group">
                                <label class="sr-only" for="form-username">Username</label>
                                <input type="text" name="form-username" placeholder="用户名" class="form-username form-control" id="form-username">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="form-password">Password</label>
                                <input type="password" name="form-password" placeholder="密码" class="form-password form-control" id="form-password">
                            </div>
                            <button type="submit" class="btn">登  录</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Javascript -->
<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.backstretch.min.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/layer/layer.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.validate.min.js" type="text/javascript"></script>
<!--[if lt IE 10]>
<script src="<?php echo C('ADMIN_JS_PATH');?>/placeholder.js"></script>
<![endif]-->
<script type="text/javascript">
    jQuery(document).ready(function() {

        /*
         Fullscreen background
         */
        $.backstretch("<?php echo C('ADMIN_IMAGE_PATH');?>/login_back.jpg");

        /*
         Form validation
         */
//        $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function() {
//            $(this).removeClass('input-error');
//        });
//
//        $('.login-form').on('submit', function(e) {
//            $(this).find('input[type="text"], input[type="password"], textarea').each(function(){
//                if( $(this).val() == "" ) {
//                    e.preventDefault();
//                    $(this).addClass('input-error');
//                } else {
//                    $(this).removeClass('input-error');
//                    fnLogin();
//                }
//            });
//
//        });


    });
    $(function(){
        $("#form").validate({
            rules: {
                'form-username': {
                    required: true,
                    minlength: 5
                },
                'form-password': {
                    required: true,
                    minlength: 5
                }
            },
            messages: {
                'form-username': {
                    required: '请输入用户名',
                    minlength: '用户名不得少于五个字符'
                },
                'form-password': {
                    required: '请输入密码',
                    minlength: '密码长度不得少于五位'
                }
            },
            submitHandler:function(form){
                fnLogin();
            }
        });
    })

    function fnLogin() {
        DMS.ajaxPost("/WFGarden/manager.php?s=/Index/login",$('#form').serialize(),function(ret){
            if(ret.status==1){
                layer.msg('登录成功', {icon: 1,time: 1000},  function() {
                    if(ret.url){
                        window.location.href = ret.url;
                    }else{
                        window.location.href = window.location.href;
                    }
                });
            }else{
                layer.msg(ret.info);
            }
        })
    }
</script>
</body>
</html>