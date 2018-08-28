# 完成账户信息修改功能
* 利用 resource 路由为我们提供的路由： 'user.edit'载入修改资料的界面 和 'user.update'完成修改的逻辑
* 编辑对应方法 UserController@edit 载入视图
```
public function edit(User $user) //这里通过依赖注入实例化了 $user
{
    return view('user.edit', compact('user')); //这里将 $user 传给视图
}
```
* 编辑视图处理 ： **记得伪造为 method="PATCH" 的表单**
```
@extends('layouts.master') 

@section('title', '修改资料') 

@section('content')
<div class="container">
    <h1 class="text-center">修改资料</h1>
    @include('components._error', ['errors' => $errors])

    <form method="POST" action=" {{ route('user.update', $user->id) }} ">
        {{-- 重点： 因为 user.update 路由要求method="PUT/PATCH" 但是普通form并不支持，所以需要我们自己添加一个 input.hidden 表单项来伪造 --}}
        @method('PATCH')
        @csrf
        <div class="form-group">
            <label for="name">用户名</label>
            <input type="text" class="form-control" id="name" placeholder="请输入用户名" name="name" value="{{ old('name') ? old('name') : $user->name }}">
            {{-- 这里是判断 old('name') 是否有值，如果没有，就用我们传过来的$user --}}
        </div>
        <div class="form-group">
            <label for="password">新密码</label>
            <input type="password" class="form-control" id="password" placeholder="请输入密码" name="password">
            <input type="password" class="form-control" placeholder="请在此输入密码以确认" name="password_confirmation">
        </div>
        <button type="rest" class="btn btn-secondary">重置</button>
        <button type="submit" class="btn btn-primary">修改</button>
    </form>
</div>
@endsection
```
* 在 _navbar.blade.php 绑定一个跳转到编辑页面的超链接 `Auth::id()` 获取当前登陆用户的主键id
```
<a href="{{ route('user.edit', Auth::id()) }}" class="btn btn-outline-success"> {{ Auth::user()->name }} </a>
```
* 完善 UserController@update 更新数据库中的用户信息
```
public function update(UserRequest $request, User $user)
{
    // ...
}
```
* 上面有两个坑： 第一个是 `User $user` => 路由要求我们得传修改数据的主键进来！ => 因此表单上 `<form ... action=" {{ route('user.update', $user->id) }} ">`
* 第二个就是使用 `UserRequest $request` 验证数据的时候，会报错“邮箱没填，用户名重复等...”
```
return [
    'name' => 'required|min:8|max:32|unique:users,name,' . Auth::id(), // unique:表,字段,排除校验自己
    'email' => 'sometimes|required|email|unique:users', // sometimes 只有在表单提交的数据中，存在 email 字段时校验
    'password' => 'required|min:8|confirmed'
];
```
* 最后我还是觉得这种太麻烦了，我不如自己再新建一个 Request 来完成关于 “更新逻辑” 的验证 `php artisan make:request UserEditRequest` 编辑 UserEditRequest@rule 并将 UserRequest 还原
```
public function rules()
    {
        return [
            'name' => 'required|min:8|max:32|unique:users,name,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed'
        ];
    }
```
* 完善功能 UserController@update
```
public function update(UserEditRequest $request, User $user) // 这里通过 UserEditRequest 验证
{
    // 判断是否修改过用户名或者密码
    if($request->name ** $user->name || Hash::check($request->password, $user->password)) { // Hash::check(v1, v2) 判断v1加密后是否等于v2
        session()->flash('danger', '您未修改任何内容');
        return redirect()->back();
    }
    // 处理数据
    $data['name'] = $request->name;
    if($request->password) {
        $data['password'] = Hash::make($request->password);
    }
    // 执行更新和跳转
    $user->update($data);
    session()->flash('success', '编辑资料成功！');
    return redirect()->route('user.show', [$user]);
}
```
# 用户资料编辑总结
* UserController@edit 展示编辑页面， 在编辑页面中需要使用 `@method('PATCH')` 来伪造表单的 action="PATCH" (因为普通 form 不支持patch)， 同时别忘了使用 `input.value={{ old('name') ? old('name') : $user->name }}` 来判断是否 修改出错，出错则用 old() 调出原来的值，没出错就用 $user->name 显示用户名。
* UserController@update 完成 “接受和验证数据->数据入库->完成跳转” 逻辑。
    * 接受和验证数据，我们先开始通过编辑 UserRequest@rule 来重新设定验证规则，但是非常繁琐：
        * `'name' => '..|unique:users,name,'.Auth::id()` => 排除唯一验证时没修改name导致验证自己和自己重名
        * `'email' => 'sometimes|...'` => 当表单中有 email 时再验证...
    * 所以我们新建了一个 Request 进行验证 `php artisan make:request UserEditRequest` ， 只验证用户名和密码。 **nullable** 可以为空。
    ```
    public function rules()
    {
        return [
            'name' => 'required|min:8|max:32|unique:users,name,' . Auth::id(),
            'password' => 'nullable|min:8|confirmed'
        ];
    }
    ``` 
    * update() 方法需要2个参数，第一个就是我们上面验证的，第二个则是修改用户的主键id，我们可以通过 `Auth::id()` 或者传递过去的$user `$user->id` 来获取。因此 `form.action="{{ route('user.update', $user->id) }}"`
    * update() 方法中，我们需要判断用户是否修改了用户名或者密码之一，然后需要处理要更新的数据 $data ，判断密码是否为空，为空则不更新密码。最后使用 $user->update($data) 更新数据
    ```
    // 唯一需要注意的就是 Hash::check(用户输入的密码, 经过Hash加密的旧密码) => 是将用户输入的密码加密后 对比旧密码，如果两者相同，则返回true
    ```

# 使用中间件 **middleware** 判断用户是否登陆
* 定义构造函数： UserController@__contrusct
```
 public function __construct()
{
    $this->middleware('auth'); //调用 中间件auth 判断用户是否登陆
}
```
* 这样一来，用户不登陆，访问控制器内的任何方法，都会被强制跳转到登陆页面，但是有一个问题，注册和新增用户也会强制跳转到登陆页面，所以我们应该使用 `...->except(['方法名1', '方法名2'])` 排除那些不需要登陆就可以访问的方法
```
/**
    * 定义可以被公开访问（不需要登陆）的方法
    */
private $public = ['create', 'store'];

/**
    * 通过构造函数调用中间件判断用户是否登陆
    */
public function __construct()
{
    // 调用中间件 “auth” 判断用户是否登陆 -> 排除(可以被公开访问的方法)
    $this->middleware('auth')->except($this->public);
}
```

# 使用 Policy 来控制权限

> 上面实现的编辑用户资料的功能其实有很严重的权限错误：应该设置只能自己编辑自己！但是其实通过修改浏览器地址指向的url，我们可以编辑所有其他的用户。

* 创建 Policy 策略 `php artisan make:policy UserPolicy` => 会在 /app/policies/ 下创建一个 UserPplicy 文件
* 但是这样创建在接下来我们依然要去绑定模型和自己编写方法，太麻烦了，我们创建一个针对 User 模型的策略。（即修改 users表的权限控制策略） `php artisan make:policy UserPolicy --model=User`，查看它的内部代码
```
<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization; //引用trait

    public function view(User $user, User $model) //view策略
    {
        //
    }
    public function create(User $user) //create策略
    {
        //
    }
    public function update(User $user, User $model) //update策略
    {
        //
    }

    public function delete(User $user, User $model) //delete策略
    {
        //
    }
}

// 只要对应的策略返回 true，则表明可以执行接下来的操作。
```
* 当然现在这个东西是不可以直接使用的！需要在 “服务提供商Providers” /app/Providers/ 下的 **AuthServiceProvider** 中注册
```
protected $policies = [
    'App\Model' => 'App\Policies\ModelPolicy',
    'App\User' => 'App\policies\UserPolicy', //这里填写UserPolicy的命名空间
];
``` 
* 在控制器中调用策略进行权限认证 UserController@update `authorize('验证方法名', 用户)`
```
public function update(UserEditRequest $request, User $user) // 这里通过 UserEditRequest 验证
{
    // 判断用户是否在权限策略内进行操作（自己正在更新自己，而不是越权更新他人）
    $this->authorize('update', $user); //这里的 'update' 对应 UserPolicy@update, $user 对应 当前用户 
    ...
}
```
* 完善 UserPolicy@update **方法返回值为 true 则通过验证**
```
public function update(User $user, User $model) //第一个参数表示当前登陆的用户，第二个参数表示被修改的用户
{
    // 判断当前登陆的用户是否为当前被修改的用户
    return $user ** $model;
}
```

# 总结 Policy 的使用
* Policy 策略定义文件都在 /app/Policies/ 下
* 创建 Policy 并指定该策略针对的模型 `php artisan make:policy XxPolicy --model=Xx`
* 创建好的 Policy 里面的方法就是不同的权限认证场景，在控制器中使用 `$this->authorize('方法名', 当前用户)` 进行调用。 **一旦 方法() 返回true 则通过验证，因此在方法内，我们需要进行条件判断**
* 创建好的 Policy 不可以直接使用，需要在 /app/Providers/ 内注册，这里我们是针对用户权限的策略，因此需要在AuthServiceProvider 中注册
```
protected $policies = [
    'App\Model' => 'App\Policies\ModelPolicy',
    'App\User' => 'App\policies\UserPolicy', // 模型命名空间 => 策略命名空间
];
```
