# 介绍
* [Laravel](https://laravel.com/) 是一款 MVC架构、 目前最流行的 PHP框架。
* Laravel的优点在于： 丰富的composer类库支持， 优雅的代码， 未来的主流框架（目前市场占有率最高的框架）
* Laravel的缺点在于： 过于优雅（我们只需要编写极少的代码即可实现功能，意味着底层极其复杂的封装）导致程序的执行效率略低， 和thinkphp等国内主流框架相比，上手难度略高（因为它为我们集成了很多其他的功能，甚至你还需要学习nodeJS相关的知识）。
* 本项目，是完全使用 Laravel框架 内的所提供的最基础，但是又是最有用（能显著提升我们开发效率）的工具而开发出来的。在学习过程中，你只需要操作一次数据库，不需要自己构建html视图模板（当然还是要写一些html和js代码的），不需要考虑外部的css、js。本教程的目的完全为向各位 phper 以及对 laravel 有兴趣的小伙伴推荐这款我相信是未来主流的php框架。
# 准备工作
* 确保你了解 php面向对象编程 的基础知识， 会html和简单的js， 在css方面： 我们使用laravel内置的 [bootstrap4](https://v4.bootcss.com/docs/4.0/getting-started/introduction/)， 最后，一定要会使用 [composer](https://www.phpcomposer.com/)。
* 唯一一次操作数据库： 创建用户、数据库，授权

> 如果你愿意用root用户，你甚至只需要 create 一个 database 即可。 （不过不推荐，我的习惯是一个项目 对应 一个用户 + 一个数据库，root则只用来管理他们）

```
# 创建用户 blog, 密码自定义
CREATE USER 'blog'@'%' IDENTIFIED BY '密码';
# 创建数据库 blog, 设置默认编码为utf8
CREATE DATABASE `blog` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
# 授权 授予 blog库下所有表的 所有权限 给 用户blog
GRANT ALL on blog.* to 'blog'@'%';
```

* 使用 composer 创建一个 laravel 项目 取名叫blog
```
# 进入你本地服务器用于存放网站文档的目录，输入命令
composer create-project --prefer-dist laravel/laravel blog
```

* 你还需要配置一个虚拟主机以提升开发效率（直接访问url，不需要 “localhost/项目/public” 访问 ），你可以选择手动配置，或者选择集成开发环境创建项目（推荐：mac=>mamp 、 windows=>laragon。linux=>宝塔面板）。

> 下文中，“/” 即表示 laravel 框架的根目录

* 配置 /.env 文件
```
# 数据库配置
DB_CONNECTION=mysql #类型
DB_HOST=127.0.0.1 #ip
DB_PORT=3306 #端口
DB_DATABASE=数据库名
DB_USERNAME=用户名
DB_PASSWORD=密码
```
* 下载中文包 `composer require caouecs/laravel-lang` 然后将 /vendor/caouecs/src/zh-CN/ 放入 /resources/lang/ 下
* 配置一下 /config/app.php
```
# 时区
'timezone' => 'Asia/Shanghai',
# 语言
'locale' => 'zh-CN',
```
# 准备工作总结
* 1、创建用户、数据库，然后授权。
* 2、使用 composer 创建项目。
* 3、配置 laravel 的环境 ./env 。 然后使用 composer 安装了汉化包，并且在 /config/app.php 中设置时区并且让中文包生效。

# 第一阶段： Migration、Factory、Seeder
> 你可能没有见过上面3个名词，不过和他们有关的文件都存放在 /database/ 下： 通过这个文件夹的名称，你大概已经猜到：这三个文件都是用来操作数据库的。
-----------------------------
* 上文中，我们只是创建了数据库，并没有创建数据表，现在来确定一下我们的数据表
    * 一个用户表 users
    * 一个博客表 blogs
    * 一个评论表 comments

    > 项目是一个个人博客，因此只有博主可以发布、删除、修改博客。其他用户则可以查看博客和发布评论。

* 使用 Migration 创建这3张数据表
    * 打开命令行，创建 migrations 
        `php artisan make:migration create_blogs --create=blogs` 博客表， 
        `php artisan make:migration create_comments --create=comments` 评论表

    > php aritsan 是laravel内置的命令 你可以直接在控制台输入它，则会在控制台提示你接下来你能输入的命令。
    > 上文我们就使用 make:migration 帮我们创建了迁移文件， --create 是参数，即告诉这条命令，帮我们创建一个用于创建数据表的迁移文件

* 为什么不创建用户表呢？ 打开 /database/migrations/ 你会发现有一个2014年就创建好的 针对 users 和 password_resets 表。这是框架自带的。

* 编辑这两个迁移文件
    * create_blogs
    ```
    // 首先类定义中，有两个方法，up()可以理解为正向操作：创建表，而 down()可以理解为回滚操作：删除表。
    // up()
    Schema::create('blogs', function (Blueprint $table) {
        $table->increments('id');
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });

    // down() 已经自动帮我们写好了。
    ```
    * create_comments
    ```
    // up()
    Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content');
            $table->integer('blog_id'); //这条评论是针对哪一篇博客的？
            $table->integer('user_id'); //这条评论是哪一位用户发送的？
            $table->timestamps();
        });
    ```
* 执行迁移： 1、确保你的 /.env 配置正确 2、确保你的数据库可以正常使用 3、确保数据库中没有数据表或者没有和users blogs comments重名的数据表 `php artisan migrate`
* 打开数据库（你可以任选一款数据库管理工具，或者直接使用mysql的命令行），打开数据库 blog ，你会发现有以下表
    * blogs => 我们创建的博客表
    * comments => 我们创建的评论表
    * migrations => 系统创建的迁移记录表
    * password_resets => 框架自带迁移文件生成的重置密码用表
    * users => 框架自带的用户表

* 主要解释一下 migrations 表：
    * 这是一个记录你的迁移文件名称和批次的表。它的主要作用是通过记录批次，方便你对数据库进行版本控制：打开 migrations表，你会发现，当前记录了4张表的迁移文件名，而他们的batch都是1，你可以理解为当前数据库是第一批，版本1。
    * 如果你执行 `php artisan migrate:rollback` 即回滚数据库，将会执行批次batch最大的记录的那些迁移文件的 down() 方法。

* migration 的作用：1、帮我们省略了去写sql语句的麻烦，2、让我们对数据库可以进行有效的版本控制。
-------------------------
* 使用模型工厂 Factory 来插入虚构的数据

> 在日常的开发中，我们需要很多模拟的数据进行测试，模型工厂的作用就是帮我们快速的，随机的生成这些数据。

* 创建模型工厂  `php artisan make:factory BlogFactory --model=Blog` ， 关于评论表的模型工厂请自己写。
* 注意此时我们其实没有模型 Blog 和模型 Comment，我们只是创建了数据表而已。因此我们再创建两个模型。
`php artisan make:model Blog`， 关于评论表的模型请自己写。

> 细心的你可能发现了，我们的数据表和模型的名字是有区别的：数据表为“小写复数形式”，而模型名为“大写单数形式”。 创建的模型都存在于 /app/ 下。

* 编辑模型工厂 /database/factories
    * BlogFactory
    ```
    // 使用 Faker 类为我们提供的生成随机伪造数据的方法生成数据 
     return [
        'title' => $faker->name,
        'content' => $faker->text,
    ];
    ```
    * CommentFactory
    ```
    return [
        'content' => $faker->text,
        'blog_id' => 1,
        'user_id' => 1, 
    ];
    ```
* 使用 tinker 模式调试代码
    * 进入 “修补匠模式” `php artisan tinker` ， 当命令提示符变为 ">>>" 时，你就处于tinker模式下了，此时你可以输入php代码，或者调用laravel提供的全局函数，甚至引用一个类，调用它的静态方法或者实例化它。
    * 在 tinker 模式下使用全局函数 factory() 生成模拟的数据 `factory(App\Blog::class)->make()` 此时屏幕上会显示，它给你模拟出来的一个虚拟数据数组。
    * 使用 create() 一次性向数据表中插入100条模拟的数据 `factory(App\Blog::class, 100)->create()` 打开数据库，您会发现100条标题和内容都无关紧要，但是对我们快速开发特别有用的测试数据已经存放在数据库中了。
------------------
* 使用 Seeder 一次性完成多个数据库的批量虚拟数据插入
* 创建 Seeder (如果你处于 tinker, 【ctrl】+【c】 先退出) `php artisan make:seeder UserTableSeeder` ，针对博客表和评论表的Seeder创建命令自己写。
* 打开 /database/seeds/ 我们创建的Seeder都在这里了，不过多了一个 DatabaseSeeder.php，我们等下再来了解它，先编辑其他Seeder，以 UserTableSeeder.php 为例
```
...
use App\User; // 在 class 关键字前面，引用一下 User 模型

class ...
public function run() 
{
    factory(App\User::class, 50)->create(); //向users表中插入50条模拟数据
    $user = User::find(1); //插入完后，找到 id 为 1 的用户
    $user->name = "najiuyuanzou"; //设置 用户名
    $user->email = "najiuyuanzou@test.com"; //设置 邮箱
    $user->password = bcrypt('liuhaoyu'); //设置 密码
    $user->save(); //保存
}
```

> 在这里我们明确1号用户为真实的可用的管理员用户！所以我们设置一下它的 用户名 邮箱 以及密码

* 其余的Seeder我们可以只插入模拟的数据即可。

* 现在回过头来，编辑 DatabaseSeeder
```
public function run()
{
    // $this->call(UsersTableSeeder::class); // 这里给我们举例了如何引用其他Seeder
    $this->call(UserTableSeeder::class);
    $this->call(BlogTableSeeder::class);
    $this->call(CommentTableSeeder::class); // 这里有先后关系需要注意一下： 评论n : 1文章/用户，所以应该把它写在最后
}
```
-------------------------
* 使用命令，刷新整个数据库并且执行模拟数据插入 `php artisan migrate:refresh --seed` => 查看数据库，发现数据库重置了，并且 users blogs comments 每张表都有很多虚拟的数据。

# 第一阶段总结
* 学习使用 migrations 的创建、编辑、执行以及回滚： 实现对数据表的结构更改以及数据库版本管理（说白了就是个带日志的数据表结构管理工具）
* 学习使用 factories 的创建、使用 tinker 调试、使用 factory() 全局函数制造和插入数据。
* 学习使用 seeds 的创建(Seeder)、编辑其他Seeder(在 run() 中调用 factory() )、编辑DatabaseSeed(在 run() 中调用 其他 Seeder)。
* 最后通过它们3个的配合，使用命令刷新了整个数据库并且分别向3张表插入了很多模拟的数据，便于我们开发。

> 也许你到这里会觉得这还不如你写sql语句。但是请相信我，等你熟练掌握使用这些东西之后，你的开发速度会非常快！（毕竟你不需要再 "INSERT INTO table values ()" 复制粘贴修改100遍了）

# 第二阶段： Auth的使用
* 输入神奇的命令，看看发生了什么 `php artisan make:auth`
* 打开浏览器输入你配置的虚拟主机地址，你会看到一个Laravel框架的欢迎页面，这不是重点，点击右上角的 register ，你可以注册账号，点击 login 你可以登陆... 
* 本阶段结束。

# 第二阶段继续： Auth
* 好吧你可能很懵逼，但这就是Laravel的厉害之处，那个2014年就建好的migration迁移文件可不是个摆设。它就是通过操作users表来实现注册登陆等等的。
* `php artisan make:auth` 到底干了哪些事情呢？
    * 它给你创造了一组控制器，位于 /app/Http/Controllers/Auth
    * 它在 /routes/web.php 中给你定义了2条路由
    ```
    Auth::routes(); //这是用户操作相关的路由

    Route::get('/home', 'HomeController@index')->name('home'); //这是主页的路由
    ```
    * 它给你创造了一组视图 /resources/views/auth/ 下是用户操作相关的路由， home.blade.php是主页， layouts/下是布局模板。

* 我们改良一下它自动为我们生成的东西
    * 路由方面 (routes/web.php) 我们将根路由也指向视图 home.blade.php ，这是最后的有效代码：
    ```
    // Route::action('uri', 'Controller@function');
    Route::get('/', 'HomeController@index');

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
    ```
    * 控制器方面 app/Http/Controllers/ 我们修改一下 HomeController.php
    ```
    /**
     * 这里这个构造函数调用了 中间件auth 对我们进行权限认证
     * 即要求我们必须登陆才可以访问该控制器的其他方法
     * 有两种解决方法，一直是在 $this->middleware('auth')->except('你要排除权限认证的方法')，比如 ...->except('index')
     * 另一种是直接干掉这个函数（我们确定这个控制器就只是来展示首页的，那么就干掉它吧）
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    ```
    * 视图方面：HomeController@index （这里我指的是 Home控制器的 index() 方法）调用了 `return view('视图名称')` 来抓取视图显示在页面上，现在打开浏览器访问主页，你就可以看得到 home.blade.php 中的内容了，我们看看 /resources/views/home.blade.php 的内容: **重点： @extens @section**
    ```
    {{--  内容不重要我们等下要改，先来看下 @符号 的作用  --}}

    {{--  @extends继承其他模板  --}}
    @extends('layouts.app') {{--  这里的layouts.app => /resources/views/layouts/app.blade.php  --}}

    {{--  @section 填充在布局模板上用 @yield 标注的占位符  --}}
    @section('content') {{--  你可以在 /resources/views/layouts/app.blade.php 看到 @ yield('content')标注的占位符 --}}
        ... 这里面是html内容
    @endsection
    ```
    
    > Auth为我们生成的整个视图模板的逻辑： layouts/app.blade.php 为布局模板，其他模板都继承该模板。

    * 最后我们“汉化”这些视图
        * home.blade.php，你可以自由发挥，展示一个好看的主页，这是我的
        ```
        @extends('layouts.app')

        @section('content')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">欢迎！这里是 “horry” 的个人博客</div>

                        <div class="card-body">
                            horry。

                            {{--  这里等下要添加一个跳转到展示文章列表页面的按钮  --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection
        ```
        * ../layouts/app.blade.php & ../auth/login.blade.php & register.blade.php
        ```
        # 首先布局模板我们需要把 brand 登陆、注册这些东西改一改
        {{ config('app.name', 'Laravel') }} => 我的博客 //注意这里有一个 config('app.name') 该函数其实是读取的 /.env 里的 APP_NAME 值，且默认值为 'Laravel' ，也就是说，你改 APP_NAME 也可以改这里显示的值，不过我嫌麻烦，直接查找替换了。
        {{ __('Login') }} => 登陆
        {{ __('Register') }} => 注册
        {{ __('Logout') }} => 退出
        
        # 然后登陆模板：
        {{ __('Login') }} => 登陆
        {{ __('E-Mail Address') }} => 邮箱
        {{ __('Password') }} => 密码
        {{ __('Remember Me') }} => 记住我
        然后我们把 ForgetPassword 那个按钮给干掉吧（这个找回密码的功能需要一个SMTP服务的邮箱才能实现，现在暂时不弄）

        # 然后注册模板
        {{ __('Register') }} => 注册
        {{ __('Name') }} => 昵称（用户名）
        {{ __('Confirm Password') }} => 密码确认

        # 有可能有说漏的，反正自己看着页面上的英文查找替换成中文就可以了。
        ```

# 第二阶段总结
* 我们使用一条命令就实现了用户操作的相关功能。
* 但是这条命令生成的视图是英文的，所以需要我们改成中文。
* 这条命令主要是 生成了一组用户操作的控制器+主页控制器（其实还有中间件），生成了2条路由，生成了一组视图。

# 第三阶段_1： 路由、模型、视图、控制器详解。
* 如果你完全熟悉MVC架构，可以跳过这一步。
* 这里用大白话解释：
    * 路由： /routes/web.php 浏览器中输入的地址，比如定义 `Route::get('home', 'HomeController@home')` => 即表示，你输入 "http://localhost/blog/public/**home**" 是以GET的请求方式去请求 HomeController 的 home() 方法。
    * 模型： /app/ 一个模型对应数据库中的一张数据表。（注意大小写和单复数，模型:Model => 数据表:models）
    * 视图： /resources/views/ 视图就是普通的html模板，它等待控制器通过 `return view()` 调用和渲染它，最终展示给网站访客。
    * 控制器： /app/Http/Controllers/ 处理数据、调用模型、简单地操作数据库、渲染视图...，都由它完成。
    * 总结 => 路由定义在浏览器中访问某控制器中某方法的地址，控制器完成一系列操作：如果需要操作数据库，需要调用模型，每一个模型对应一张表。如果需要显示数据，则需要找到框架内指定位置的视图，对它完成渲染。

# 第三阶段_2：资源路由、在资源控制器中完成对博客的增删改查。

> 我们写的程序，除了前台好看的界面，就是后台的程序，而后台的程序无非就是“增删改查”以及“花式增删改查”罢了。
> 因此，仔细想想，对于一张数据表的操作，我们通常就需要这些行为：1、一个分页展示所有数据的列表 2、一个添加数据的功能 3、一个编辑数据的功能 4、一个显示单条数据详细信息的功能 5、一个删除功能。

* 创建一个资源控制器，一次性帮我们生成能实现上面5个功能的方法 `php artisan make:controller BlogController --resource --model=Blog` ( --resouce生成的控制器为资源控制器即自带 CURD增删改查 所有方法的控制器 ) ( --model 是让生成的控制器在参数列表中自动帮我们完成依赖注入生成实际变量 )
* 根据 三_1 阶段的说法，我们其实需要设置很多路由，来对应生成的 BlogController 下的各种方法，Laravel已经帮我们想到了所以它给我们提供了这样一种方法配置路由，编辑 /routes/web.php ,在最后面添加这么一句 `Route::resource('blog', 'BlogController');`
* 有一个命令可以看到当前所有生效的路由 `php artisan route:list` ，控制台自动弹出的表格中，Method 表示 请求方法，即
`Route::这里的方法()`  ，URI 表示 请求地址，即 `Route::action('这里就是uri')`， name 表示 路由别名，可以通过 `Route::action('uri', 'Controller@function')->name('这里可以定义路由别名')`。
* 你会发现，有7条关于 blog 的路由，这就是 `Route::resource('blog', 'BlogController')` 帮我们生成的。（5个功能7条是因为 添加和编辑多了2条载入视图的路由）
* 完成增删改查吧: 首先完成 BlogController@index : 展示列表
    * 先来个入口链接，打开 home.blade.php 
    ```
    {{--  上面说过这里会添加一个按钮  --}}
    
    <a href="{{ route('blog.index') }}" class="btn btn-lg btn-block btn-primary">点击这里查看我的博客</a>
    ```
    * 编辑 BlogController@index => 这里还是再提醒一下吧，这是说 BlogController 的 index() 方法。控制器文件都在 app\Http\Controllers 中
    ```
    <?php
    namespace App\Http\Controllers;
    use App\Blog; //这里是使用命令创建控制器时，通过 --model=Blog 自动帮我们生成的
    use Illuminate\Http\Request;

    class BlogController extends Controller
    {
        public function index()
        {
            // 查询数据，并且让查询结果是一个可分页对象
            $blogs = Blog::orderBy('created_at', 'desc') // 调用 Blog模型 的静态方法 orderBy('根据created_at字段', '倒叙排序')
                ->paginate(6); // -> 链式操作：paginate(6) 即数据没页6条
            // 跳转到视图并传值
            return view('blog.index', [ //第一个参数是说，视图模板是 /resources/views/blog/index.blade.php
                'blogs' => $blogs, //这里是说，我们给视图传递一个叫 $'blogs'的变量，值是前面我们查询的数据，也叫$blogs。
            ]); // view() 的第二参数也可以使用 view(..., compact('blogs'))
        }
    ```
    * 此时刷新页面当然会报错了，因为我们的视图还不存在，新建文件夹 /resources/views/blog/index.blade.php
    ```
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">这是页面小标题</div>

                <div class="card-body">
                    这里是内容
                </div>
            </div>
        </div>
    </div>

    @endsection
    ```
    
    > 这就是我们所有页面的布局，可以复制一份，作为模板。

    * 完成视图里面的内容
    ```
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">文章列表</div>

                <div class="card-body">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-info">
                            <tr>
                                <th>文章标题</th>
                                <th>发布时间</th>
                                <th>相关操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- 这里通过 @foreach 遍历数据 --}} @foreach ($blogs as $blog)
                            <tr>
                                <td>{{ $blog->title }}</td>
                                <td>{{ $blog->created_at }}</td>
                                <td></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            {{-- 这里通过 $blogs->links() 来显示分页按钮 --}} {{ $blogs->links() }}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    ```

    * 完成文章添加 添加入口链接， ../layouts/app.blade.php
    ```
    {{-- route('路由别名') 在视图上就是一个指向 BlogController@create 的链接 --}}
    <a href="{{ route('blog.create') }}" class="dropdown-item"> 添加文章 </a>
    ```

    * 完成文章的添加 BlogController@create
    ```
    public function create()
    {
        return view('blog.create'); //载入视图
    }
    ```
    * 编辑视图 **重点：表单中添加@csrf告诉框架，这是我们自己的表单，不用担心csrf跨站请求伪造的攻击**
    ```
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">添加文章</div>

                <div class="card-body">
                    {{--  from.method="POST" action="通过 route()函数读取路由别名 " --}}
                    <form method="POST" action="{{ route('blog.store') }}">
                        {{--  声明 csrf 令牌  --}}
                        @csrf
                        <div class="form-group">
                            <label for="title">文章标题</label>
                            <input type="text" class="form-control" id="title" placeholder="请输入文章标题" name="title">
                        </div>
                        <div class="form-group">
                            <label for="content">文章内容</label>
                            <textarea id="content" cols="30" rows="10" class="form-control" name="content"></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">发布新文章</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection
    ```

    > 所谓跨站请求伪造，可以理解为来自于其他ip的表单，恶意请求我们的服务器。Laravel提供了一种防范这种攻击的手段，即将自己的路由隐藏起来，只有带有 @csrf 声明的表单可以找得到接收表单信息的路由

    * 编辑 BlogController@store
    ```
    public function store(Request $request) //这里的 $request 是通过依赖注入的方法实例化的 Request 类的对象，包含的有所有请求的信息
    {
        // 我们只需要调用 Blog模型 的静态方法 create() 插入 $request->post() 数据即可
        $blog = Blog::create($request->post()); //改方法的返回值是新插入的数据生成的对象
        // redirect() 页面重定向
        return redirect()->route('blog.show', $blog); // 这里我们将 $blog 作为参数请求 BlogController@show 
    }
    ```

    * 回到页面，点击提交，会发现报错了，Laravel是一个极其注重安全的框架，用户能修改哪些字段，必须要在模型文件中声明，因此打开 app\Blog.php 模型文件
    ```
    // 可填字段白名单
    protected $fillable = [
        'title', 'content'
    ];
    ```
    * 再次提交，页面一片空白，是因为我们的 BlogController@show 方法还没有写，不过你可以注意到地址栏已经发生了改变。

    * 完成 show 方法
    ```
    public function show(Blog $blog) //这里已经通过依赖注入的形式帮我们实例化了 $blog
    {
        return view('blog.show', [
            'blog' => $blog, //直接将$blog传给视图进行渲染
        ]);
    }
    ```
    * 新建 ../blog/show.blade.php
    ```
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">文章详情</div>

                <div class="card-body">
                    <h1 class="text-center">{{ $blog->title }}</h1>

                    <p>发布时间<small>{{ $blog->created_at }}</small></p>

                    <hr>

                    <p> {{ $blog->content }} </p>
                </div>
            </div>
        </div>
    </div>

    @endsection
    ```

    * 刷新页面，文章就显示出来了。

    * 完成我们的编辑入口链接: 在  ../blog/index.blade.php & show.blade.php 中合理的位置添加一个编辑按钮
    ```
    <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-info">编辑文章</a>
    ```
    * 完成 BlogController@edit
    ```
    public function edit(Blog $blog)
    {
        return view('blog.edit', [
            'blog' => $blog,
        ]);
    }
    ```

    * 完成视图 **重点：action声明文章编号，根据路由要求action在表单中使用@method伪造请求动作类型**
    ```
    @extends('layouts.app')

    @section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">编辑文章</div>

                <div class="card-body">
                    {{-- action需要声明当前编辑的文章编号$blog->id --}}
                    <form method="POST" action="{{ route('blog.update', $blog->id) }}">
                        {{--  声明 csrf 令牌  --}}
                        @csrf
                        {{--  伪造 PATCH 方法  --}}
                        @method("PATCH")
                        <div class="form-group">
                            <label for="title">文章标题</label>
                            <input type="text" class="form-control" id="title" placeholder="请输入文章标题" name="title" value="{{ $blog->title }}">
                        </div>
                        <div class="form-group">
                            <label for="content">文章内容</label>
                            <textarea id="content" cols="30" rows="10" class="form-control" name="content">{{ $blog->content }}</textarea>
                        </div>
                        <p>发表于<small>{{ $blog->created_at }}</small></p>
                        <p>修改于<small>{{ $blog->updated_at }}</small></p>
                        <button class="btn btn-primary" type="submit">确认编辑</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection
    ```
    * 完成 BlogController@update 
    ```
    public function update(Request $request, Blog $blog)
    {
        $blog->update($request->post()); //调用 $blog对象->update(更新数据组成的数组) 更新
        return redirect()->route('blog.show', $blog);
    }
    ```
    * 完成删除功能 在 index.blade.php 和 show.blade.php 合理的位置插入删除按钮
    ```
    <a href="javascript:deleteConfirm({{ $blog->id }});" class="btn btn-danger btn-sm">删除文章</a>
                                
    {{--  因为删除也需要 csrf 令牌认证，所以弄个表单  --}}
    <form method="POST" action="{{ route('blog.destroy', $blog->id) }}" id="delete-blog-{{ $blog->id }}">
        @csrf
        {{--  这里伪造DELETE请求  --}}
        @method("DELETE")
    </form>
    ```
    * “删除文章” 按钮其实是调用了一个 js 函数，我们在 ../layousts/app.blade.php 中完成
    ```
    <script>
        function deleteConfirm(id) {
            var confirm = window.confirm('确认要删除这篇文章吗？');
            if(confirm **= true) {
                $("#delete-blog-" + id).submit(); //提交表单
            }else {
                window.alert('你选择不删除！');
            }
        }
    </script>
    ```
    * 完成 BlogController@delete 方法
    ```
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('blog.index'); //跳转到首页
    }
    ```
    * 增删改查完成。
* 完善和优化
    * 首先无论增删改查操作，成功后我们没有任何提示，我们使用 session 闪存方法消息吧：
        * 新建组件视图文件夹 /resources/views/components/
        * 然后新建一个组件视图 _message.blade.php => 组件视图我们都用`_`下划线开头
        ```
        <div class="container">
            {{--  遍历 success danger 这两个我们等会会在 session->flash() 方法中设置的"key"  --}}
            @foreach (['success', 'danger'] as $msg)
                {{--  当key存在的时候，证明我们给 session flash 闪存里面装载了一次提示信息，那么就显示提示信息  --}}
                @if (session()->has($msg)) 
                    <div class="alert alert-{{ $msg }}">
                        <ul>
                            <li>{{ session()->get($msg) }}</li>
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>
        ```
        * 在 ../layousts/app.blade.php 中导入该组件 **重点：@include 导入html片段**
        ```
        {{--  在导航下面，内容上面导入  --}}
        @include('components._message')
        ```
        * 编辑 BlogController 里的各种方法，在执行成功某些方法时，页面重定向前，装载闪存。以删除举例
        ```
        $blog->delete();
        session()->flash('success', '删除文章成功！'); //装载session闪存
        return redirect()->route('blog.index');
        ```

    * 然后有个问题，就是在于，我们这是一个个人博客，所以只有我们自己可以对博客文章进行增删改，而用户只可以进行查看。因此我们需要：
        * 使用构造函数调用 auth中间件 来排除没有登陆的用户查看文章详情: 编辑 BlogController
        ```
        public function __construct()
        {
            $this->middleware('auth')->except('index');
        }
        ```
        * 在 新增create、编辑edit、和删除方法中加入一次用户认证，以 create 方法举例
        ```
        // 因为比较简单，所以我们不用Policy进行认证，我会在以后的教程里面教大家如何使用Policy策略进行权限认证
        // 这里我们就使用判断当前用户在数据表中信息的主键id是不是1即可（因为我们在Seeder里面把编号为1的用户设置为了可用的管理员账号）

        // 1、在代码开头引用 Auth

        // 2、在方法内先判断一下是不是 1号用户
        if(Auth::user()->id != 1) { // Auth::user() 获取当前用户信息 -> id获取属性id（主键）
            session()->flash('danger', '抱歉，只有博主才可以新增文章！');
            return redirect()->back();
        }
        ```
* 针对博客的增删改查在这里就结束了。

# 第三阶段总结
* 我们使用命令创建了一个 “资源控制器”
* 我们在 /routes/web.php 定义了一条资源路由
* 我们使用 BlogController 中的7个方法完成了对 博客文章 的 CURD（增删改查）操作。
* 我们优化了一下体验，使用 `session()->flash()` 装载闪存信息，用一个组件html片段加载信息，最后用`@include()`在模板上加载这个html组件。
* 我们最后增加了一个简单的权限认证，判断进行增删改的用户是不是管理员，不是管理员则不允许操作，直接装载一条错误提示闪存，然后返回。

# 第四阶段 评论功能
* 新建一个评论资源控制器 `php artisan make:controller CommentController --model=Commment`
* 新增一条资源路由，但只支持发表 /routes/web.php `Route::resource('comment', 'CommentController', ['only' => 'store']);`

> 其实我们可以定义一条 `Route::post('comment', 'CommentController@store')` 路由，但是为什么要写资源路由呢？因为我要告诉你资源路由可以用 `['onlu'=>'操作']` 让其只支持一种操作：）

* 在文章详情页面下方增加一个表单 show.blade.php
```
<form method="POST" action="{{ route('comment.store') }}">
    @csrf
    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="blog_id" value="{{ $blog->id }}">

    <div class="form-group">
        <label for="content"></label>
        <textarea id="content" class="form-control" cols="30" rows="10" name="content">您对这篇文章有什么看法呢？</textarea>
    </div>
    <button class="btn btn-primary" type="submit">发表评论</button>
</form>
```

* 编辑 CommentController@store
```
public function store(Request $request)
{
    Comment::create($request->post());
    session()->flash('success', '评论成功！');
    return redirect()->back();
}
```
* 提交评论出错了，又忘了写可填字段白名单，编辑模型 app\Comment
```
protected $fillable = [
    'content', 'user_id', 'blog_id'
];
```
* 展示评论
    * 首先我们需要确定 Blog 和 Comment 的关系 => Blog 1:n Comment “一篇博客有多个评论”
    * 我们来绑定他们的关系
        * app\Blog.php 
        ```
        // 绑定1:n关系
        public function comments() {
            return $this->hasMany('App\Comment'); // 1 hasMany n
        }
        ```
        * app\Comment.php
        ```
        public function blog() {
            return $this->belongsTo('App\Blog'); // n belongsTo 1
        }
        ```
    * 然后通过他们的关系，我们可以在 BlogController@show 方法中调用 `$blog->comments` 来获取属于这篇文章的评论
    ```
    // 查询评论
    $comments = $blog->comments;
    // 视图渲染
    return view('blog.show', [
        'blog' => $blog,
        'comments' => $comments, //把评论也传过去
    ]);
    ```
    * 在视图层遍历评论 show.blade.php
    ```
    <h3>评论</h3>
    <ul>
        @foreach ($comments as $comment)
            <li><small>{{ $comment->userName() }} 评论说：</small>“ {{ $comment->content }} ”</li>
        @endforeach
    </ul>
    ```
    * 注意我们调用了 $comment->userName() 方法，现在没有，所以我们再 app\Comment.php 模型中完成
    ```
    use App\User; //引用模型

    // 根据 user_id 获取用户名
    public function userName() {
        return User::find($this->user_id)->name; //这里通过当前对象的 user_id 获取 user对象， 然后指向->name属性
    }
    ```
* 评论验证

> 在博客中，我们就没有使用验证，那是因为项目定位是一个个人博客，能够操纵博客增删改的只有我们自己。而评论则是只要有人注册账号，就可以评论了，所以我们应该对评论进行一些校验以防恶意攻击。
    
* 新建一个请求Request `php artisan make:request CommentRequest`

* 新建的请求位于 app\Http\Requests\ 下，编辑 CommentRequest
    ```
    // 1、 开启授权
    public function authorize()
    {
        return true; //如果返回false则所有请求都无法生效，会告诉你没有授权（其实在这里面我们是需要去进行判断的，但是这里的逻辑很简单：只有登陆才能查看文章详情，才能看到文章详情下面发表评论的表单，才能发表评论。）所以我们这里直接 return true;
    }

    // 2、 编辑规则
    public function rules()
    {
        return [
            'content' => 'required|min:15|max:100', //这里你可以定义规则我的是：必填、最少15字、最多100字
        ];
    }
    ```

    * 在 CommentController@store 方法的参数列表中通过 CommentRequest 构造 $request， 自动完成校验
    ```
    public function store(CommentRequest $request) // 这将 Request 改成 CommentRequest 就会自动调用 CommentRequest@rules 来校验请求的数据了
    {
        Comment::create($request->post());
        session()->flash('success', '评论成功！');
        return redirect()->back();
    }
    ```

    * 优化视图 show.blade.php
    ```
    {{-- 样式里面加一个判断，判断是否有关于content的错误有的话给样式给文本域加一个红边边 --}}
    <textarea id="content" class="form-control {{ $errors->has('content') ? ' is-invalid' : '' }}" cols="30" rows="10" name="content">你对文章有什么看法呢？</textarea>

    {{-- 如果有错误，再显示一个小的错误提示信息 --}}
    @if ($errors->has('content'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('content') }}</strong>
        </span>
    @endif
    ```
    * 此时提交表单，左下角会提醒你 “内容不能为空”，如果你想改“内容两个字”，可以打开 /resources/lang/zh-CN/validation.php
    ```
    'content' => '内容', //这里就是配置字段的中文名，你把它改成评论即可。
    ```
    * 有时候文章过长，导致提交了，往下拉才看得到文本域变红，所以我们需要新建一个错误组件../components/_error.blade.php
    ```
    {{--  判断是否有错误  --}}
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            {{--  遍历所有错误  --}}
            @foreach ($errors->all() as $error)
                <ul>
                    {{--  打印错误  --}}
                    <li> {{ $error }}</li>
                </ul>
            @endforeach
        </div>
    @endif
    ```
    * 然后在 show.blade.php 中引用 `@include('components._error')`
# 第四阶段总结
* 我们依然创建资源控制器，但是在路由中使用`['only'=>'store']` 让资源路由只暴露指向 CommentController@store 的路由
* 我们学会了通过`hasMany() & belongsTo()`绑定模型之间的1:n关系。然后通过`文章->评论+s;`的方法直接获取了属于某篇文章的所有评论。
* 我们学会了创建请求Request，并且在它的内部配置验证规则，在控制器层中通过依赖注入的形式验证数据。
* 一旦表单提交的数据不符合 Request@rules Laravel会自动帮我们生成一个叫 $errors 的数组，它存放着所有的错误信息， 我们在视图上通过判断它是否有 `content` 字段来判断是否是表单提交的评论有问题，然后修改文本域的样式并且在下方用一个小的提示span显示错误提示信息
* 错误提示信息显示的是“内容 怎么怎么样...”，我们想把“内容”改成评论只需要修改中文语言包下的validation.php中的'content'字段的别名即可。

# 第五阶段 最后总结
* 想让项目上线，也许你需要
    * 更好看的html排版
    * 重新执行一次 `php artisan migrate:rollback` 
    * 权限认证太水了。你需要学习使用 Policy 来进行更安全和全面的权限认证。
> 也许文字很多，但是真正的代码可能只有不到100行，你如果熟练掌握，可能不需要30分钟，甚至10分钟，你就可以开发出这样一个博客了。
* 视图方面
    * 我们有通过 auth 生成的模板
    * Laravel 自带的 bootstrap4 + jquery 
    * 所以我们解决了css和js的问题 => 我们只是写了一个 “确认删除” 的前端代码
* 数据库方面
    * 我们有 /database/ 下提供的 3套解决方案 Migration / Factory / Seeder 来帮我们解决数据库管理的问题
    * 因为上面的解决方案，我们甚至只写了 建用户、建表、授权3条 数据库操作语句。
* 路由方面
    * Auth 自动帮我们生成了用户操作相关路由
    * 我们使用资源路由来映射一个 CURD 控制器
* 控制器和模型方面，通过命令生成的所有类文件，都几乎帮我们写好了，我们只需要完成里面的逻辑。
* 当然，我们还有 Request 请求认证 Policy 策略控制等等一些列的特性没有学习，我们也只使用了一次composer，其实在开发Laravel时，我们还可以使用非常多的，支持Laravel的，完善的轮子可以利用。

> 现在请告诉我，它是否配得上 “优雅” 的两字？：）

> 希望大家可以喜欢、学习和推广Laravel。如果您愿意付出比学习thinkphp5多0.01分的努力，我想这个框架是非常简单的。

> 如果您依然讨厌它的庞大，我向您推荐 Lumen 框架。
