# Geetest 集成大致过程
1. 实现登录的大致逻辑
2. 注册一个[极验](http://www.geetest.com/)的帐号
3. 在 “极验” 的后台管理中注册一个行为验证
4. 根据 官方Demo 配置我们的控制器和路由
5. 根据 官方Demo 配置我们的登录模板
6. 测试

# Geetest 集成详细过程
1. 实现登录的大致逻辑
    * 创建控制器 `php artisan make:controller GeetestController`
    * 编辑控制器 /app/Http/Controllers/GeetestController
    ```
    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    /**
    * 这是一个集成 Geetest 验证码的 Demo 类
    */
    class GeetestController extends Controller
    {   
        /**
        * 导入登录视图
        */
        public function login() {
            return view('Geetest/login');
        }

        /**
        * 验证用户信息
        */
        public function check() {
            return '用户已经在前端通过了验证码验证, 你可以在这里完善后续的逻辑';
        }
    }
    ```
    * 视图就是简单的表单，省略。

2. 省略 => “注册”
3. 省略 => “后台登录” => “行为验证” => 申请一个 id & key

4. 配置控制器和路由
    * 首先， Demo 给出的核心类库 是一个类文件叫 **class.geetestlib.php**, 类名叫 **GeetestLib** 。我们创建一个类名一样的控制器来代替它 `php artisan make:controller GeetestLib`
    ```
    # 不要拷贝类，拷贝类里面的内容进来即可
    ```
    * GeetestController 控制器实现逻辑
    ```
    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Http\Controllers\GeetestLib; // 我们创建然后拷贝得来的 GeetestLib 核心库

    /**
    * 这是一个集成 Geetest 验证码的 Demo 类
    */
    class GeetestController extends Controller
    {   
        // 这里配置 id & key
        private $captchaId = "5d467a3cb22a9310837d51720c5251f0";
        private $privateKey = "40764e6b94344f780d4b6b07148c9495";

        /**
        * 导入登录视图
        */
        public function login() {
            return view('Geetest/login');
        }

        /**
        * 验证用户信息
        */
        public function check() {
            return '用户已经在前端通过了验证码验证, 你可以在这里完善后续的逻辑';
        }

        /**
        * 实现验证功能： 直接复制官方demo提供得
        */
        public function startCaptchaServlet() {
            // 这里使用配置的 id & key
            $GtSdk = new GeetestLib($this->captchaId, $this->privateKey);
            session_start();
            
            $data = array(
                "user_id" => "test", # 网站用户id
                "client_type" => "web", #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
                "ip_address" => "127.0.0.1" # 请在此处传输用户请求验证时所携带的IP
            );
            
            $status = $GtSdk->pre_process($data, 1);
            $_SESSION['gtserver'] = $status;
            $_SESSION['user_id'] = $data['user_id'];
            echo $GtSdk->get_response_str();
        }
    }
    ```

    * 配置路由 /routes/web.php
    ```
    // 集成 Geetest 验证码
    Route::get('GeetestLogin', 'GeetestController@login'); //登录页面
    Route::get('GeetestCheck', 'GeetestController@check'); //登录验证 （我们没写具体逻辑）
    Route::get('GeetestStartCaptchaServlet', 'GeetestController@startCaptchaServlet'); // 调用方法启用验证码
    ```

5. 完善登录模板 /resources/views/Geetest/login.blade.php
    * 需要导入 jquery (我们用npm run dev编译的app.js整合了jquery)
    * 需要导入 Demo 给出 gt.js ，我们放在 public/js 下 `<script src="/js/gt.js"></script>`

    > 其实理论上还可以放在 /resouces/assets/js/ 下， 并且在 /resouces/assets/js/app.js 中 require 进来让它参与被编译，直接在 public/js 中打包整合生效。

    * 在模板上，需要定义两个样式类 `.show & .hide` => 用于 gt.js 操控提示信息的样式

    > 同样可以写进 /resouces/assets/sass/ 下

    * 给 表单提交 “登录” 按钮一个id
    * 拷贝 Demo 中提供的前端 逻辑js， 注意绑定下这个按钮
    * 注意下 .ajax 配置的 url 必须是我们在 web.php 中定义的路有 'GeetestStartCaptchaServlet'
    
    * 具体代码
    ```
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- 这是我们用 npm run dev 编译后的 css / js -->
        <link rel="stylesheet" href="/css/app.css">
        <script src="/js/app.js"></script>

        <!-- 这里需要用到两个样式 -->
        <style>
            .show {
                display: block;
            }
            .hide {
                display: none;
            }
        </style>

        <title> Geetest 集成 Demo</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="text-center">Geetest 集成 Demo
                        <small>
                            <a href="http://www.geetest.com/"> Geetest 官方网站 </a>
                        </small> 
                    </h1>
                </div>
                <div class="col-lg-12">
                    <form method="GET" action="/GeetestCheck">
                        <div class="form-group">
                            <label for="exampleInputEmail1">模拟邮箱地址</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="请输入邮箱...">
                            <small id="emailHelp" class="form-text text-muted">我们不会公开您的邮箱</small>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">模拟密码</label>
                            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="请输入密码...">
                        </div>
                        <div class="form-group">
                            <div id="embed-captcha"></div>
                            <p id="wait" class="show">正在加载验证码......</p>
                            <p id="notice" class="hide">请先完成验证</p>
                        </div>
                        <!-- 这里需要绑定一个按钮 -->
                        <button type="submit" class="btn btn-primary" id="embed-submit">登录</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 引用 gt.js -->
        <script src="/js/gt.js"></script>
        <!-- 直接复制官方Demo里的js代码 -->
        <script>
            var handlerEmbed = function (captchaObj) {
                $("#embed-submit").click(function (e) {
                    var validate = captchaObj.getValidate();
                    if (!validate) {
                        $("#notice")[0].className = "show";
                        setTimeout(function () {
                            $("#notice")[0].className = "hide";
                        }, 2000);
                        e.preventDefault();
                    }
                });
                // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
                captchaObj.appendTo("#embed-captcha");
                captchaObj.onReady(function () {
                    $("#wait")[0].className = "hide";
                });
                // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
            };
            $.ajax({
                // 获取id，challenge，success（是否启用failback）
                url: "/GeetestStartCaptchaServlet", // 加随机数防止缓存
                type: "get",
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    // 使用initGeetest接口
                    // 参数1：配置参数
                    // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                    initGeetest({
                        gt: data.gt,
                        challenge: data.challenge,
                        new_captcha: data.new_captcha,
                        product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                        offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                        // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
                    }, handlerEmbed);
                }
            });
        </script>
    </body>
    </html>
    ```
6. 测试成功

# 可以优化的地方
* 最好不要用一个 “控制器” 充当核心类库， 应该把GeetestLib 想办法集成到另一个地方去
* 视图模板上的 js & css 应该写在 resources/assets 里面参与生成 app.css & app.js 的编译
* 具体登录逻辑我们没写。应该还可以在登录验证 check() 方法再确认一次 Geetest验证 是否成功，可以参考 [Demo](https://github.com/GeeTeam/gt3-php-sdk/archive/master.zip)
