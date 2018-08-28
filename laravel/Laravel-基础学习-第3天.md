# 用户注册功能的实现
* 现在使用资源控制器 UserController 来完成注册
```
public function create()
{
    return view('user.create');
}
```
* 完善 /resources/views/user/create.blade.php **在form中添加 @csrf 防止伪造表单攻击我们的服务器**
```
@extends('layouts.master') 

@section('title', '用户注册') 

@section('content')
<div class="container">
    <h1 class="text-center">注册</h1>
    <form method="POST" action=" {{ route('user.store') }} ">
        {{--  这里是为了防止 csrf（跨站请求伪造） 攻击而配置的隐藏保护令牌 --}}
        @csrf
        <div class="form-group">
            <label for="name">用户名</label>
            <input type="text" class="form-control" id="name" placeholder="请输入用户名" name="name">
        </div>
        <div class="form-group">
            <label for="email">邮箱</label>
            <input type="email" class="form-control" id="email" placeholder="请输入邮箱" name="email">
        </div>
        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" class="form-control" id="password" placeholder="请输入密码" name="password">
            <input type="password" class="form-control" placeholder="请在此输入密码以确认" name="password_confirmation">
        </div>
        <button type="rest" class="btn btn-secondary">重置</button>
        <button type="submit" class="btn btn-primary">注册</button>
    </form>
</div>
@endsection
```
* 验证用户输入的数据
```
public function store(Request $request)
{   
    // 注意：$request 即通过 依赖注入 自动生成的用户提交的数据的临时存储变量
    // 我们需要先验证用户的数据
    $this->validate($request, [
        'name' => 'required|min:8|max:32|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed' //注意 confirmed => 其实是验证的页面上的 password_confirmation 是否等于 password
    ]);
    echo '没有错误！';
}
```

> 注意此时如果用户输入正确，则会显示 “没有错误！” ，但是如果不能通过验证，则会弹回 create.blade.php，但是用户输入的东西也都没有了。

* 在视图上使用 old()方法 读取上次用户输入的但是没有通过验证的数据提升用户体验 `<input type="text" ... name="name" value="{{ old('name') }}">`

> old('字段名') => 这是存在Session中的，只生效一次的临时保存的用户输入的信息（不会保存 type = password 类型的数据）

* 在视图上显示错误提示信息

> 一旦出错， laravel 框架会将错误信息存放在一个叫 $errors 的变量中并传递给视图模板

```
{{--  @if(判断是否有错误)   --}}
@if(count($errors) > 0)
    <ul class="alert alert-danger">
        {{--  @foreach( $datas as $data )  --}}
        {{--  $errors->all()获取所有信息  --}}
        @foreach ($errors->all() as $error)
            <li> {{ $error }} </li>
        @endforeach
        {{--  记得结束循环@endforeach  --}}
    </ul>
@endif
{{--  记得结束判断@endif  --}}
```

* 优化： 将这个错误用于提示的 html代码片段 放置到组件视图 /resources/views/components/ 下 取名叫做 _error.blade.php ， 然后在 create.blade.php 中 `@include('components._error')` 引用

> 这时的错误提示信息是英文的！

# i18n（国际化） 的 laravel
* 语言包都放在 /resources/lang/ 下， 默认只有 en/ 英文语言包
* 使用哪个语言包的配置文件是 /config/app.php 中的 `'locale' => 'en',` 
* 下载语言包，确保composer可用且位于laravel框架下 `composer require caouecs/laravel-lang`
* 下载好的语言包位于 /vendor/caouecs/laravel-lang/src/ 中文叫 zh-CN/ 将它复制到 /resources/lang/ 下，并配置 /config/app.php `locale => 'zh-CN'`
* 用于验证和错误提示的语言包叫 validation.php

# 自定义错误提示信息
* 在 UserController 中自定义错误提示信息
```
...
$this->validate($request, [
    'name' => 'required|min:8|max:32|unique:users',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed' //注意 confirmed => 其实是验证的页面上的 password_confirmation 是否等于 password
], [
    // 如果你在这里定义错误提示信息， 则会覆盖由语言包提供的默认的提示信息
    'name.required' => '用户名不可能不填，这辈子都不可能',
]);
// $this->validate($要验证的数据, [验证规则...], [错误提示信息...]);
...
```

# 创建一个专门用于验证的 Request 类
* 创建一个 request 类 `php artisan make:request UserRequest`。
* 生成的文件位于 /app/Http/Requests/ 
* 编辑 /app/Http/Requests/UserRequest
```
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    // 权限认证
    public function authorize()
    {
        return true; //这里先改为true跳过验证
    }

    // rules() 定义规则
    public function rules()
    {
        return [
            'name' => 'required|min:8|max:32|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ];
    }
    // messages() 定义提示信息
    public function messages()
    {
        return [
            'name.required' => '名字是不可以不写的，这辈子都不可能不写。',
        ];
    }
}
```
* 在 UserController 中使用 UserRequest : 
```
// 1、 引用 UserRequest
...
use App\Http\Requests\UserRequest;

// 2、 在参数列表中使用依赖注入的形式，构造$request
...
public function store(UserRequest $request)
{   
    // 这里面的验证我们不再需要了
    echo '没有错误！';
}
...
```

* 完成注册功能
```
// 引用 Hash 类 
...
use Illuminate\Support\Facades\Hash;

// 完成注册逻辑
public function store(UserRequest $request)
{   
    // 获取所有post数据
    $data = $request->post();
    
    // 处理数据
    unset($data['_token']); //干掉 @csrf
    unset($data['password_confirmation']); //干掉 确认密码
    $data['password'] = Hash::make($data['password']); //使用 Hash::make() 加密密码
        // dd($data);
    
    // 插入数据
    $user = User::create($data); //插入成功后返回值为新增成功后的对象
    return redirect()->route('user.show', [$user]); //如果将对象作为 route() 的第二参数，laravel则会自动解析$user中的id，将主键id作为真正的参数
}
```

> 可以使用 User::insert() 插入数据，但是 created_time 和 updated_time 字段不会更新时间
> **所以最好使用 User::create**

# 页面重定向 redirect() 
* 页面之间的跳转可以使用 `return redirect()` 实现
```
return redirect('/'); //跳转到 '/' 路由即 根网页
return redirect()->route('user.create'); //跳转到某命名路由
return redirect()->route('user.show', [参数列表]); //跳转到名字叫 'user.show' 的路由，并且传递参数
```

# 闪存 session()->flash() 
* 其实 old() 就是laravel偷偷帮我们使用这种方式实现的
```
// 控制器中设置闪存 session()->flash('key', 'value');
session()->flash('success', '成功');

// 视图中判断和读取闪存
{{--  判断是否有闪存 success 存在 has()  --}}
@if (session()->has('success'))
    {{--  如果存在则显示 get() --}}
    <p class="bg-success">{{ session()->get('success') }}</p>
@endif
```

> 方便我们部署提示消息

# 部署一个公共提示消息的视图组件
* 创建一个闪存组件的视图 /resources/views/components/_message.blade.php
```
{{--  公共提示消息视图模板  --}}
<div class="container">
    {{--  定义3中情况然后循环  --}}
    @foreach (['success', 'danger', 'info'] as $msg)
        {{--  如果闪存中存有3种情况中的一种  --}}
        @if (session()->has($msg))
            {{--  则显示一个对应颜色的提示框  --}}
            <div class="alert alert-{{$msg}}">
                {{--  显示提示信息  --}}
                {{session()->get($msg)}}
            </div>
        @endif
    @endforeach
</div>
```
* 在注册逻辑中，添加数据成功后，页面重定向之前添加一条闪存
```
public function store(UserRequest $request)
{   
    // 获取所有post数据
    ...
    
    // 处理数据
    ...
    
    // 插入数据
    $user = User::create($data); 
    // 增加闪存
    session()->flash('success', '注册成功，以下是您的账号信息！');
    // 跳转页面
    return redirect()->route('user.show', [$user]); 
}
```

# 完善登陆功能
* 创建一个控制器专门用于登陆操作 `php artisan make:controller LoginController`
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * 登陆页面
     */
    public function login() {
        return view('login.login');
    }

    /**
     * 存储登陆信息
     */
    public function store() {

    }

    /**
     * 注销
     */
    public function logout() {

    }
}
```
* 配置路由 /routes/web.php
```
Route::get('login', 'LoginController@login')->name('login');
// 可以定义两个同名，同uri 的路由，只要他们的action 不一样即是两条不同的路由
Route::post('login', 'LoginController@store')->name('login'); 
Route::get('logout', 'LoginController@logout')->name('logout');
```
* 登陆页面视图 /resources/views/login/login.blade.php => 直接复制 create.blade.php ，不要邮箱和密码确认即可。
* 使用 Auth 验证登陆
```
// 1 引用Auth
...
use Auth;

// 2 调用 Auth::attempt(用户信息) 验证
public function store(Request $request) {
// 验证数据： 因为只有2个字段，只需要验证他们是否填写了即可
$user = $this->validate($request, [
    'name' => 'required',
    'password' => 'required',
]); //$this->validate() 方法如果通过验证 会将表单数据变成一个数组当作返回值
    //dd($user);

// 使用 Auth 验证用户名密码是否正确
$res = Auth::attempt($user); // Auth::attempt(要求参数是一个数组) 返回值是boolean 验证通过是true 否则是false
if(!$res) {
    session()->flash('danger', '用户名或密码错误！'); //使用增加一条提示“密码错误”的闪存信息
    return redirect()->back()->withInput(); //back() 返回前一页面 withInput() 将表单数据也还回去
}
session()->flash('success', '欢迎');
return redirect('/');    
}
```
* 在视图层使用 Auth 提供的方法判断用户是否登陆 `Auth::check()` ，如果登陆，使用 `Auth::user()` 提取用户数据并显示
```
{{--  如果登陆 Auth::check() ** true  --}}
@if (Auth::check())
    {{--  Auth::user() 提取用户信息为对象 ->name 访问用户名  --}}
    <a href="#" class="btn btn-outline-success"> {{ Auth::user()->name }} </a>
    <a href="{{ route('logout') }}" class="btn btn-outline-danger"> 注销 </a>
    @else
    <a href="{{ route('user.create') }}" class="btn btn-outline-success">注册</a>
    <a href="{{ route('login') }}" class="btn btn-outline-primary">登陆</a>
@endif
```
* 注册逻辑中，让用户注册成功后自动登陆 UserController
```
// 1 使用 Auth
use Auth;

// 2 调用 Auth::login() 将注册信息作为参数，实现直接登陆
public function store(UserRequest $request)
{   
    // 获取所有post数据
    ...
    // 处理数据
    ...
    // 插入数据
    ...
    // 增加闪存
    ...
    // 注册成功后自动登陆
    Auth::login($user); //Auth::login(直接将$user数据信息传入)
    // 跳转页面
    ...
}
```
* 完成退出功能
```
public function logout() {
    Auth::logout(); // Auth::logout() 会清空已登陆的用户信息
    session()->flash('info', '您已成功退出！'); //发送一条提示信息
    return redirect()->route('login'); //跳转到登陆页面
}
```
* 完成 “记住我” 功能

> Auth::attmept() 其实是将登陆信息写入 Cookies 中
> 可以看到普通登陆后，laravel_session的有效时间就是一天以内（具体多久我没算）

    * 首先登陆页面上的表单更新一下
    ```
    <div class="form-check">
        {{--  勾选为true 不勾选没有 remember_me 字段  --}}
        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="true">
        <label class="form-check-label" for="remember_me">记住我</label>
    </div>
    ```

    * 然后在登陆逻辑中
    ```
    // 使用 Auth 验证 Auth::attempt(用户信息, 是否记住用户true记住)
    $res = Auth::attempt($user, $request->has('remember_me')); // $request->has('某个字段') 如果字段存在则返回true 否则返回false
    ```

> 可以看到这样做会在 Cookies 里生成一个 `remember_web_xxxxxxxxxxxx` 的字段，有效期大约5年。它会存一个和 users表中 remember_token 字段有关的一个值。通过比对两个值来确定用户是否勾选过长期登陆。

# 总结一下
* post表单需要添加 `@csrf` 来告诉laravel，这是内部自己的表单，可以信赖
* 验证数据可以在控制器内部验证，也可以新建一个 Request 验证
    * 自己内部验证
    ```
    $this->validate($data, [验证规则], [提示信息]);
    ```
    * 建一个 Request 验证 `php artisan make:request XxRequest` => 然后在构建 $request 的时候使用 XxRequest 为规范类
    ```
    // rules() 定义规则
    public function rules()
    {
        return [
            'name' => 'required|min:8|max:32|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ];
    }
    // messages() 定义提示信息
    public function messages()
    {
        return [
            'name.required' => '名字是不可以不写的，这辈子都不可能不写。',
        ];
    }
    ```
* 在视图层表单项使用 `value="{{ old('表单字段') }}"` 使出错后，除 password 字段的其他字段可以依然存在，提升用户体验
* 一旦验证出错，错误都存放在一个叫 $errors 的对象中，可以通过遍历 $errors->all() 来显示错误。
* `session()->flash('key', 'value')` 显示临时的提示信息
* `Auth::login($用户信息)` => 注册的时候我们将用户信息直接作为参数给login()方法实现了登陆
* `Auth::logout()` => 注销登陆的账号
* `Auth::attempt($用户信息, 是否记住账号)` => 登陆验证用户名密码， 第二参数默认false不记住
* `Auth::check()` => 判断用户是否登陆
* `Auth::user()` => 获取用户的信息，可以通过 `Auth::user()->属性` 来展示一些信息，比如 `...->name` 显示用户名
