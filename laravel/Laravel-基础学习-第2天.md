# 用户注册页面的实现
* 创建控制器 `php artisan make:controller UserController`
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{   
    /**
     * 注册页面
     */
    public function register() {
        // 渲染试图， 去找 /resources/views/user/register.blade.php
        return view('user.register');
    }
}
```
* 创建视图 /resources/views/user/register.blade.php
```
{{--  继承第一天的布局模板  --}}
@extends('layouts.master')

{{--  填补站位符， @section(参数) 参数对应 @yeild(参数)  --}}
@section('title', '主页')

@section('content')
<div class="container">
    这是注册页面
</div>
@endsection
```
* 创建一条路由并且在导航上使用它
```
# /routes/web.php
// 用户功能实现学习 相关路由配置
Route::get('register', 'UserController@register')->name('register');

# nav.blade.php
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    {{--  这里是 brand  --}}
    <a class="navbar-brand" href="#">Laravel5.6学习</a>
    {{--  这是里 小屏幕的菜单开关  --}}
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    {{--  这是 导航菜单：这些链接会在大屏幕平铺展开， 而在小屏幕由上面的开关打开  --}}
    <div class="collapse navbar-collapse" id="navbar">
        {{--  只要给 ul 一个 .mr-auto 类，他就能在大屏幕下将下面的 div 顶向屏幕最右边  --}}
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href=" {{ route('test.home') }} ">主页</a></li>
            <li class="nav-item"><a class="nav-link" href=" {{ route('test.lists') }} ">列表</a></li>
            <li class="nav-item"><a class="nav-link" href=" {{ route('test.show') }} ">详情</a></li>
            <li class="nav-item"><a class="nav-link disabled" href="#">不可用</a></li>
        </ul>
        <div>
            <a href="" class="btn btn-outline-primary">登录</a>
            <a href=" {{ route('register') }} " class="btn btn-outline-success">注册</a>
        </idv>
    </div>
</nav>
```

# migrations 数据迁移
* 数据迁移文件位于 /database/migrations/ 下
* 数据迁移的主要作用就是对项目所用数据库进行版本管理，它可以定义表结构，修改表结构，并支持对上面两种操作进行回滚。( up() 方法创建/修改表， down() 方法执行相应的回滚操作 )
* 来看看 /database/migrations/ 下自带的两个迁移文件
```
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ::create('user数据表', 然后调用一个闭包去编辑表结构)
        Schema::create('users', function (Blueprint $table) {
            // 一般来说 “increments = int primary key auto_increment” 即主键 
            $table->increments('id');
            // 这个是创建 varchar 类型的 `name` 字段，默认 varchar 长度为255
            $table->string('name');
            // 这个是创建 email , 并添加唯一约束
            $table->string('email')->unique();
            $table->string('password');
            // 是否记住我 字段
            $table->rememberToken();
            // 这会创建 2个字段： 创建时间created_time 更新时间updated_time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 这句话的意思是 DROP DATABASE IF EXISTS 'users'
        Schema::dropIfExists('users');
    }
}
```
* 使用上面的迁移文件创建数据表
    * 在 mysql 中创建数据库， 创建用户
    ```
    CREATE DATABASE `laravelStudy` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    CREATE USER `laravelStudy`@`localhost` IDENTIFIED BY 'laravelStudy';
    GRANT ALL ON `laravelStudy`.* TO `laravelStudy`@`localhost`;
    ```
    * 在 /.env 中配置数据库相关信息
    ```
    # 数据库 软件名
    DB_CONNECTION=mysql
    # ip 地址
    DB_HOST=127.0.0.1
    # 端口号
    DB_PORT=3306
    # 数据库名 用户名 密码
    DB_DATABASE=laravelStudy
    DB_USERNAME=laravelStudy
    DB_PASSWORD=laravelStudy
    ```
    * “执行迁移” `php artisan migrate` 
    > 命令行提示： Migration table created successfully.

* 具体发生了什么：
    * 根据 两个迁移文件的 up()方法 帮我们生成了2张数据表
    * 同时还生成了一个 migrations 表，可以看到这张表里记录了上面新生成两张表的迁移文件名称，以及批次 **“batch”** 最重要的就是这张表里的 “batch” 字段，这两张表的 batch = 1 ,说明他们是我们第一次执行 `php artisan migrate` 命令时生成的表，那么下次我们再创建迁移文件对数据表的结构进行修改时，它将再次在 migrations 表中插入新数据，而那时的 batch = 2。 **如果我们会滚，则会会滚 batch 值最大的迁移文件的 down() 方法。**

* 试试回滚命令 `php artisan migrate:rollback` => 执行了 down() 方法

# 模型
* laravel中的模型是通过 “ORM对象关系映射” 映射到数据库中的 => 可以说一个模型对应一张数据表。
* 模型一般直接放在 /app/ 下， 上面提到laravel 给我们默认提供了关于 users 的迁移文件， 它自然也给我们创建了一个模型 User.php ( **laravel中默认 Model模型 必须是 首字母大写单数形式 => models数据表得 必须是 全小写复数形式** )
* 创建一个模型 `php artisan make:model Article -m` => 创建一个模型 Article 同时 -m 创建一个 迁移文件 （在该模型没有对应数据表的时候用这个命令）
* 打开 /app/Article.php 模型
```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{   
    // 默认 Article模型 对应 articles表 ，当然你也可以自己指定。 （不建议）
    // protected $table = "articles";
}
```

# tinker 模式测试代码
* 进入 tinker 模式 `php artisan tinker` => 命令提示符变为 `>>>` 则进入了 tinker 模式
* 来玩一玩 App\User
```
# 引用 User 模型
use App\User

# 以下代码使用的方法都是模型类可以使用的静态方法
# 插入 几条 数据
User::create(['name'=>'laravelStudy', 'email'=>'example@test.com', 'password'=>bcrypt('laravelStudy')]);

# 查询所有数据
User::all();

# 查询第一条数据
User::first();

# 根据 主键id 获取数据
User::find(1);

# 实例化对象
$user = User::first(); //将 users 表中的第一条数据以对象的形式映射到变量$user中

# 通过对象设置字段的值，并且更新数据
$user->name = 'laravelStudy_tinker'; //设置对象的属性 等同于 给字段设置新的值
$user->save(); //保存生效

# 通过 update() 方法一次修改多个字段
$user->update(['name'=>'laravelStudy', 'email'=>'ILoveLaravel@haha.com']);
```
* 总结： tinker 可以帮助我们在命令行中运行 php 代码， 主要用于测试， 可以让我们不需要在控制器或者模型中写一些测试代码，测试完成后又去删除或者注释代码。

# 带参数的路由
* 编辑 UserController 新建一个 show() 方法
```
public function show($user) {
    // dd => dump & die => 打印数据，终止程序
    dd($user);
}
```
* 学习在配置路由时给路由绑定参数 /routes/web.php
```
Route::get('user/show/{user}', 'UserController@show')->name('user.show'); //uri 为 '.../user/show/必传参数$user' 
```

> 注意 路有中的 {user} 对应 控制器方法中的参数 ($user) 。你换个名字就找不到了

* 如何访问？
```
http://www.laravelstudy.com/user/show/ => 找不到网页 （因为路由配置中 {参数} 即必传参）
http://www.laravelstudy.com/user/show/test => 网页显示 "laravelStudy"
```

* 体会 “依赖注入” 的便利 UserController
```
...
use App\User; // 引用 User 模型

...
// 在参数列表中以 “依赖注入 DI” 的形式实例化一个 User 对象
public function show(User $user) {
    // dd => dump & die => 打印数据，终止程序
    dd($user);
}
```

# 资源路由
* RESTful设计原则的概念

> 把数据看成一个资源， 我们的后台编程无非是对数据进行 “花式 CURD 增删改查”。

* 创建一条资源路由 /routes/web.php `php artisan make:controller ArticleController --resource`
```
// 资源路由 ('路由前缀', '资源控制器');
Route::resource('article', 'ArticleController');
```
* 使用 `php artisan route:list` 查看路有列表 => 会发现路由多了关于 资源article 的 列表index 新增页面create 保存插入store 显示单条数据show 编辑页面edit 更新update 删除destroy 等多条路由 => 对应 ArticleController 下的多个随着命令参数 `--resource` 创建出来的多个方法。

* 使用 `php artisan make:controller ExampleUserController --resource --model=User` 创建一个资源控制器，自带 CURD 方法，同时一些方法实现了依赖注入在参数列表中构造 **User对象** 。

* 完成 show() 方法根据id获取数据并在视图上显示
    * 删除之前的UserController ， 新建一个 restful控制器 并且在参数列表中完成依赖注入构造 `php artisan make:controller UserController --resource --model=User`
    * 更新 /routes/web.php 路由配置 
    ```
    # 把之前的用于学习的都干掉
    ...
    Route::resource('user', 'UserController');
    ```
    * 使用 `php artisan route:list` 可以查看当前生效的路由，或者路由是否有冲突（报错）。
    * 在 UserController 里完成 show()方法
    ```
    public function show(User $user) //这里已经通过依赖注入帮我们实例化了一个User对象
    {
        return view('user.show', compact('user')); // view('视图名', compact('向视图传递变量'));
    }
    ```
    * 完成视图 /resources/views/user/show.blade.php
    ```
    @extends('layouts.master')

    @section('title', '主页')

    @section('content')
    <div class="container">
        {{--  {{ $对象->属性 }}  --}}
        <p><small>用户名：</small>&nbsp; {{ $user->name }} </p> 
        <p><small>邮箱：</small>&nbsp; {{ $user->email }} </p>
    </div>
    @endsection
    ```
    * 通过查看 route list 我们看到 指向 UserController@show 的路由是 GET动作 的 user/{user}， 即访问url为 '.../user/参数' => 访问 '.../user/1' => 页面显示id为1的用户的 用户名 和 邮箱

# 美化并组件化 show.blade.php
* 增加一条命名规则： /resources/views/ 下的页面组件，我们统一放在 components/ 下。 且 components/ 下组件名都以 _ 开头
* ../components/nav.blade.php 重命名为 _nav.blade.php， 记得在引用它的布局页面 ../layouts/master.blade.php中改一下，然后我们新建一个 _userCard.blade.php
```
<div class="container">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"> {{ $user->name }} </h5>
            <p class="card-text">
                <small>Email:</small> &nbsp;
                {{ $user->email }}
            </p>
        </div>
    </div>
</div>
```
* 在 ../user/show.blade.php 中使用该组件
```
@extends('layouts.master')

@section('title', '用户信息')

@section('content')
<div class="container">
    {{--  导入组件 @include('组件名', ['导入变量' => 值])  --}}
    @include('components._userCard', ['user'=>$user])
</div>
@endsection
```
* 导入组件时，我们可以不写第二参数，也可以显示 user=>$user，但是我们可以多复制几行 `@include('components._userCard', ['user'=>$user])`， 然后可以发现页面显示了很多条一模一样的数据。
