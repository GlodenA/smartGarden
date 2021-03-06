<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="Ch">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="bookmark" href="/favicon.ico" />
    <title><?php echo C('WEB_NAME');?></title>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Favicon and touch icons -->
    <!-- <link rel="shortcut icon" href="/Public/Admin/ico/favicon.png"> -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/Public/Admin/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/Public/Admin/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/Public/Admin/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="/Public/Admin/ico/apple-touch-icon-57-precomposed.png">

    <link rel="stylesheet" href="/Public/Admin/Css//util/flex.css">
    <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
    <!-- 引入样式 -->
    <link rel="stylesheet" href="/Public/Admin/element/index.css">
    <!-- 引入组件库 -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <style media="screen">
      #app{
        background-image: url(/Public/Admin/login/loginBg.png);
        background-position: center;
        background-size: cover;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
      }
      .login-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 24px;
      }
      .login-title .prefix{
        margin-right: 12px;
        width: 8px;
        border-radius: 4px;
        background-color: #33D792;
      }
      .page-title{
        position: absolute;
        top: 12px;
        left: 1rem;
        color: white;
      }
      .page-title h2{
        margin-left: 1rem;
      }
    </style>
</head>

<body>
  <div id="app">
    <div class="page-title row items-center">
      <img src="/Public/Admin/login/logo.svg" alt="">
      <h2>智慧园林后台管理</h2>
    </div>
    <div class="row justify-center items-center" style="height:100%;">
      <div>
        <img src="/Public/Admin/login/pic01.png" alt="">
      </div>
      <el-card style="width: 380px;" class="ml4 p3 pt3">
        <div class="login-title row">
          <div class="prefix"></div>
          后台管理登录
        </div>
        <el-form class="my3" :model="loginUser" ref="loginForm" :rules="rules">
          <el-form-item prop="username">
            <el-input v-model="loginUser.username" placeholder="请输入用户名" prefix-icon="el-icon-user"/>
          </el-form-item>
          <el-form-item prop="password" class="mt3">
            <el-input v-model="loginUser.password" type="password" placeholder="请输入密码" prefix-icon="el-icon-lock"/>
          </el-form-item>
        </el-form>
        <div class="row justify-center mt4">
          <el-button type="primary" style="width:100%;" @click="doLogin">登录</el-button>
        </div>
      </el-card>
    </div>
  </div>

<!-- Javascript -->
<script type="text/javascript" src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo C('ADMIN_JS_PATH');?>/layer.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script>

<!--[if lt IE 10]>
<script src="<?php echo C('ADMIN_JS_PATH');?>/placeholder.js"></script>
<![endif]-->
<script type="text/javascript">
  const app = new Vue({
    el: '#app',
    data() {
      return {
        rules: {
          username: [
            { required: true, message: '请输入用户名！', trigger: 'blur' },
          ],
          password: [
            { required: true, message: '请输入密码！', trigger: 'blur' },
          ]
        },
        loginUser: {
          username: '',
          password: ''
        }
      }
    },
    methods: {
      doLogin(){
        this.$refs.loginForm.validate(r => {
          if(r){
            DMS.ajaxPost("/manager.php?s=/Index/login", {
              'form-username': this.loginUser.username,
              'form-password': this.loginUser.password
            },function(ret){
                if(ret.status==1){
                    layer.msg('登录成功', {icon: 1,time: 1000},  function() {
                        if(ret.url){
                            window.location.href = ret.url;
                        }else{
                            window.location.href = window.location.href;
                        }
                    })
                }else{
                    layer.msg(ret.info);
                }
            })
          }
        })
      }
    }
  })
</script>
</body>
</html>