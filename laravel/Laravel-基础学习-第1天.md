# 1. 路由
* 路由定义文件位于 /routes/web.php
```
/**
 * 默认的第一条路由如下
 * 
 * Route::动作('uri', 闭包() {
 *  return view('视图');
 * });
 * 
 * 路由的功能：
 * 1、如下调用一个闭包函数返回一张视图
 * 2、映射与 控制器@方法 的关系 => 该 'uri' 访问 'XxController@Xx方法'
 */
Route::get('/', function () {
    return view('welcome');
});
```

# 2. 使用命令行创建一个控制器
* laravel 框架自带命令行工具，只要你用控制台进入了laravel框架下，都可以使用 `php artisan` 执行一些命令
* 创建一个TestController控制器 `php artisan make:controller TestController`
* 控制器的路径是 /app/Http/Controllers/
* 打开我们创建的控制器 TestController
```
<?php

// 1命名空间
namespace App\Http\Controllers;

// 2引用空间类元素
use Illuminate\Http\Request;

// 3定义控制器类
class TestController extends Controller
{
    // 4这里面我们就可以构建我们需要的方法实现应用逻辑
}

```
* 写几个方法，和他们对应的路由
```
# 控制器层 TestController
// 4
...
public function home() {
    return 'home';
}

public function lists() {
    return 'lists';
}

public function show() {
    return 'show';
}
...

# 路由定义文件 web.php
// 路由定义
Route::get('/', 'TestController@home'); // Route::action('uri', 'Controller@function');
Route::get('home', 'TestController@home');
Route::get('lists', 'TestController@lists');
Route::get('show', 'TestController@show');
```

# 3.很熟悉的package.json
* 我们知道package.json是npm用于声明包依赖关系的配置文件(不了解的你需要学习nodeJS.npm)了
* 我们发现laravel中有 /package.json 文件，原来文档上的那句，laravel天生就集成了 bootstrap+vue，就只是它写了个package.json在这里，那么用`npm install`装上他们吧。
* 参考 /package.json 文件，有个 dev 命令，我们跑一下 `npm run dev`： 然后你就在 /public/css & /public/js 下生成了 app.css & app.js （自动集成了boostrap jquery vue）

# 4. 视图学习： 引用 app.css & app.js
* 了解 view() 函数 : 渲染视图
```
# TestController
...
public function home() {
    /**
        * 视图都在 /resources/views/ 下，比如laravel的欢迎页 welcome.blade.php
        * 视图都默认用 blade 模板引擎渲染，所以他们都叫 xxx.blade.php 
        * view('这里可以创建个文件夹/这里再写具体的视图')
        * 比如下面，我们就是去找 /resources/views/test/ 下 home.blade.php
        */
    return view('test/home');
}
...
```
* 新建 /resources/views/test/ 文件夹，在它下面新建 home.blade.php
```
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 编译好的css -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- 编译好的js -->
    <script src="/js/app.js"></script>

    <title>Test控制器专用视图</title>
</head>
<body>
    <!-- 这里主要是看看bootstrap.css有没有真的放进来 -->
    <h1 class="bg-primary">测试_Home</h1>

    <!-- 这里主要是看看jquery.js有没有真的放进来 -->
    <script>
        $(function() {
            console.log('Laravel真厉害， 他帮我打包好了css和js文件！ 我们来学习视图吧');
        })
    </script>
</body>
</html>
```
* bootstrap 和 jquery 被直接干进去的原因是 /resources/asstes/ 下的 app.js 和 app.scss
```
# app.js
require('./bootstrap'); //引入了bootstrap.js

# app.scss
// 字体
@import url("https://fonts.googleapis.com/css?family=Raleway:300,400,600");

// 变量
@import "variables";

// 引入了 /node_modules 下的 bootstrap
@import '~bootstrap/scss/bootstrap';
```
* 关于 /resources/assets 下的 js/ 和 sass/ 文件，其实就是我们可以自定义的的 .js 和 .css ，只不过他们需要编译一次 `npm run dev` 编译进 /public/js & /public/css 以投入使用 
* npm run watch (学过npm的都知道，你改scss，它自动编译css)

# 5. 视图学习： 布局模板, @extends, @yield, @section, @include
> vsCode 有叫 laravel 5 Snippets & laravel blade Snippets 的插件

* 完善 TestController
```
// 完善 lists() 和 show() 方法，让他们载入相应的视图
public function lists() {
    return view('test/lists');
}

public function show() {
    return view('test/show');
}
```
* 完善 /resoureces/views/test/ 下的 lists.blade.php 和 show.blade.php。 省略了，这里不实现功能，写两个字代替一下就行了。
* **优化** 使用布局模板，将相同的部分只写一次，将不同的部分用 **@yield** 占位： 新建文件夹 /resources/views/layouts 下新建文件master.blade.php
```
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 编译好的css -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- 编译好的js -->
    <script src="/js/app.js"></script>

    <title>
        {{--  @yield 占位同时给定默认值  --}}
        @yield('title', '布局模板默认标题')
    </title>
</head>
<body>
    
    {{--  不同部分我们用 @yield 占位  --}}
    @yield('content')
</body>
</html>
```

> 你打开浏览器控制台可能会出现错误提示说找不到 "#App"元素 那是vue.js 的锅，现在不用在意这些细节。

* 继承 @extends 布局模板以缩减代码， 使用 **@section** 填补占位符 ，以 home.blade.php 举例
```
{{--  继承布局模板  --}}
@extends('layouts.master')

{{--  填补站位符， @section(参数) 参数对应 @yeild(参数)  --}}
@section('title', '主页')

@section('content')
    {{--  对于不只一行的内容，我们用 @section + @endsection 包起来  --}}
    <h1 class="bg-primary">这是主页</h1>
@endsection
```

* 导航栏是每个页面都需要的“组件”，我们来创建一个吧，新建文件夹 /resources/views/components 新建 navbar.blade.php
```
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="#">主页 <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">列表</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">详情</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="">不可选</a>
            </li>
        </ul>
    </div>
</nav>
```

> 以上代码 copy 自 [bootstrap4中文网提供的bootstrap文档](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)

* 在 master.blade.php 使用 **@include** 载入导航栏组件
```
{{--  引用导航组件  --}}
@include('components.navbar')
```

# 路由真正投入使用
* 在真正的项目中，我们定义了路由，可不是让用户去在浏览器地址里面根据他要访问的地址去输入url。而应该是弄一些 a标签 并给它们绑定超链接，比如 `<a href="/lists">列表</a>` 。
* 但是这里有个问题，如果有一天，有需求将 '/lists' 修改为其他的路由，我不是就得在所有用到该路由的界面修改 a.href 的指向地址。因此我们这里在 /routes/web.php 中给路由定义 **别名**
```
...

// ->name(路由别名)
Route::get('home', 'TestController@home')->name('test.home');
Route::get('lists', 'TestController@lists')->name('test.lists');
Route::get('show', 'TestController@show')->name('test.show');
```
* 在视图层引用别名
```
<!-- {{}} 这是 blade 模板的插值语法， 相当于 <?php ?>  -->
<a href="{{ route('test.lists') }}">列表</a>
<!-- route('路由别名') -->
```

# 第一天总结
* 路由方面 /routes/web.php
    * 定义路由 `Route::action('uri', 'Controller@function');`
    * 闭包路由则将第二参数设置为一个匿名函数
    * 路由别名则在后面添加 `...->name('别名');`
* 视图方面 /resources/views/
    * 使用 “布局模板”： 1、将重复的代码写进 layouts/xx.blade.php 2、在其他模板使用 **@extends('layouts.xx')** 继承它。
    * 使用 “站位符” 标记不同区域， 使用 “填充符” 填充不同区域： 1、在 “布局模板” 使用 **@yield('区域名称')** 标记 2、在其他模板使用 **@section('区域名称')** 填充。
    * “站位符” 可以有默认值： `@yield('key', 'defaultValue')` ， 如果你不填充该区域，则使用defaultValue，否则你需要使用`@section('key', 'yourValue')` “覆盖” 掉 defaultValue。
    * 页面 “组件化”： 1、创建组件 components/xx.blade.php 2、在需要用到得模板使用 **@include('components.xx')** 来引入它。 
    * 在视图层通过路由别名调用路由 `{{ route('路由别名') }}` 
* 控制器方面 /app/Http/Controllers/
    * 创建控制器 `php artisan make:controller 控制器名首字母大写Controller`
    * 控制器分3块
    ```
    <?php

    // 第1块： 命名空间
    namespace App\Http\Controllers;

    // 第2块： 引用其他空间类元素
    use Illuminate\Http\Request;

    // 第3块： 具体定义
    class TestController extends Controller
    {   
        // 这里面就是我们要写的实现各种业务逻辑的函数
    }

    ```
    * 控制器渲染视图 `return view('视图文件夹/视图名称');`
* Laravel中前端的那些事
> Laravel 虽然不强制你使用哪个 JavaScript 或 CSS 预处理器，但还是提供了适用多数应用的 Bootstrap 和 Vue 来作为起点。默认情况下，Laravel 使用 NPM 安装这些前端依赖包。

* /package.json 中的 devDependencies 属性 写明了它用的包, 使用命令 `npm install` 自动装上
* laravel提供了 **Laravel Mix** 插件，使我们可以在 /resources/assets/ 下编辑 .js 和 .scss ，最后和默认的插件混合起来编译，编译使用命令 `npm run dev` 来在 public/css & public/js 下生成 app.css & app.js 
