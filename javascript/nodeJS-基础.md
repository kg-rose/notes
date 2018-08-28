# [Node.js](https://nodejs.org/en/)
* 可以理解为能在操作系统上跑的js（不仅仅在浏览器）
* 还能当web服务器哟~
* 用的Chrome V8 JS 引擎（谷歌搞出来的浏览器处理js脚本的引擎）
* 事件驱动
* 非阻塞IO模型

# 简单理解
* nodeJS是服务端的javascript
* 它是一种脚本语言，需要一个解释器。而浏览器就充当了这个解释器（chrome v8 引擎 由c++ 语言开发）将js脚本转换为机器语言后执行。
* nodeJS就是把v8引擎（谷歌开源了这个引擎）给单独搞出来然后像编写一个Nigix服务器软件一样弄出来了这么一个叫NodeJs的软件  ，部署在服务器上。
* nodeJs主要提供给我们了一个Js的本地运行环境，还可以操作一些文件（打包工具），同时可以与数据库通讯（实现后端功能）。
* 实际应用：API开发，小工具开发，web全栈开发。

# 安装
* 最好装LTS（长期支持）版，是一个安装包，下一步就行
* 验证是否安装成功，命令行
```
# 显示版本
node -v
```
* 编辑器：vscode（我再也不想看到sublime让我付钱的弹窗了），只装一个插件：
【Ctrl】+【Shift】+【x】找：**Terminal：编辑器内打开命令行** （ctrl+`）打开终端即可在编辑器中执行命令行

# nodejs模块
* 定义和暴露模块
```
// 定义一个方法
var counter = function(arr) {
    return "这里有" + arr.length + "个元素在数组里";
}

// 定义一个方法
var adder = function (a, b) {
    return `两数和为 ${a+b}`;
}

// 定义一个变量
var pi = 3.14;

// 暴露模块 module.exports.模块名 = 要暴露的东西
// module.exports.counter = counter;
// module.exports.adder = adder;
// module.exports.pi = pi;
// 简写
module.exports = {
    // 键 : 值
    counter : counter,
    adder : adder,
    pi : pi
}
```
* 引用和使用模块
```
// var 定义一个变量 = require('模块存放的路径');
var modules = require('./node模块');
// require引用的是一个module.exports暴露出来的对象，所以也可以直接引用它下面的某一个属性。
var pi = require('./node模块').pi;

// modules 是一个对象，调用方法就用 （对象.属性）即可
console.log(modules.counter(['1', '2', '3']));
console.log(modules.adder(1, 2));
console.log(modules.pi);
```

# 事件
* 1
```
// 引用事件模块
var events = require('events');

// 新建一个事件对象
var myEmitter = new events.EventEmitter();

// 绑定事件 .on('事件名', 事件触发回调函数(){//...});
myEmitter.on('someEvent', function(message1, message2) {
    console.log(message1 + message2);
});

// 手动触发事件 .emit('事件名', 参数列表);
myEmitter.emit('someEvent', '触发1', '触发2');
```
* 2
```
// 引用事件模块
var events = require('events');
// 引用工具库模块 util
var util = require('util');

// 定义一个Person类
var Person = function(name) {
    // 创建类时给属性name一个值
    this.name = name;
}

// 工具库.继承(让Person类, 继承events模块下的EventEmitter类)
util.inherits(Person, events.EventEmitter);

// 创建三个对象
var xiaoming = new Person('xiaoming');
var lili = new Person('lili');
var lucy = new Person('lucy');

// 将三个对象放进一个数组
var people = [xiaoming, lili, lucy];

// 循环数据
people.forEach(function(person) {
    // 给每个对象绑定一个事件
    person.on('speak', function(message) {
        // 事件名speak, 事件执行控制台日志记录 对象.名字属性说参数message
        console.log(person.name + "说" + message);
    });
});

// 触发事件
xiaoming.emit('speak', 'hi1');
lili.emit('speak', 'hi2');
lucy.emit('speak', 'hi3');
```

# 同步文件IO操作
* 同步，阻塞式的：如果我们写文件那一部分代码，写的内容非常大，那计算机会一直在那里执行写入代码，而不继续往下跑程序。
```
// 引入文件系统模块
var fs = require('fs');

// 读取文件 fsreadFileSynt("文件", "编码");
var content = fs.readFileSync("readMe.txt", "utf-8");
    // console.log(content);

// 写文件
fs.writeFileSync("writeMe.txt", content);
    // console.log("文件writeMe.txt创建成功，内容写入成功");
// writeFileSync会覆盖原内容
fs.writeFileSync("writeMe.txt", "Hello World");
```

# 异步读写文件
* 异步非阻塞的意义：某一部很耗时，影响用户体验（用户得一直等这一步执行完）再继续向下进行其他操作，而nodejs采用异步方法就是在“事件队列”里注册一些很耗时的事件，然后在主线程程序执行完，再执行这些耗时的操作。
```
// 1：引用文件模块
var fs = require('fs');

// 2：异步读文件， fs.readFile("文件", "编码", 回调函数(发生错误, 文件内容) {//...});
var content = fs.readFile("readMe.txt", "utf-8", function(error, data) {
    // 异步写文件
    fs.writeFile("writeMe.txt", data, function() {
        console.log("写入完毕");
    });
});

// 3：控制台打印提示
console.log("读取完毕");

/**
 * 为什么“读取”完毕会先出现？
 * 3段代码依旧是按顺序执行的，
 * 但是执行2的时候，只是执行：node自动在“事件队列”里注册一个事件（如果文件过大，采用同不方法就会阻塞其他操作）
 * 然后直接执行3
 * 然后再回头去，执行“事件队列”里的事件：读取文件
 */
```

# 目录操作
```
// 引用文件系统模块
var fs = require('fs');

fs.unlink("writeMe.txt", function() {
    console.log("删除成功");
});

// 同步就是后面加Sync，参数不加回调函数因为没啥需要回调的程序顺着跑
// fs.unlinkSync("writeMe.txt");

/* 实现一个小功能 */
// 创建目录stuff
fs.mkdir('stuff', function() {
    // 读取文件readMe.txt
    fs.readFile('readMe.txt', 'utf-8', function(error, data) {
        // 将文件内容写到 ./stuff/writeMe.txt中
        fs.writeFile('./stuff/writeMe.txt', data, function(){
            console.log('文件写入成功');
        });
    });
});
```

# 流和管道
* stream流
    * 读
    ```
    // 模块
    var fs = require('fs');
    
    // 创建读取流 createReadStream(当前路径/文件名);
    // var myReadStream = fs.createReadStream(__dirname + '/readMe.txt');
    // 设置编码（不设置的话是buffer（可以理解为机器才能读得懂的东西），而且文件很大就有很多buffer。）
    // myReadStream.setEncoding('utf-8');
    // 可以直接这么写
    var myReadStream = fs.createReadStream(__dirname + '/readMe.txt', 'utf-8');
    
    var data = "";
    
    // 'data'事件 读取ing 
    myReadStream.on('data', function(chunk) {
        data += chunk;
    });
    
    // 'end'事件 读取完成后
    myReadStream.on('end', function() {
        console.log("数据接收完毕");
        console.log(data);
    });
    ```
    * 写
    ```
    // 模块
    var fs = require('fs');
    
    // 创建读取流
    var myReadStream = fs.createReadStream(__dirname + '/readMe.txt', 'utf-8');
    // 创建写入流(文件名)
    var myWriteStream = fs.createWriteStream(__dirname + '/writeMe.txt');
    
    // myReadStream.on('data', function(chunk) {
    //     // 写 .write
    //     myWriteStream.write(chunk);
    // });
    
    // 新建写入内容
    var writeData = "Hello World";
    // 写
    myWriteStream.write(writeData);
    // 写完啥事不干，把流关了
    myWriteStream.end();
    // finish结束事件 
    myWriteStream.on('finish', function(){
        // 回调函数打一个提示信息出来
        console.log('文件写入完成');
    });

    ```
* 管道grep
```
// 模块
var fs = require('fs');

// 创建读取流
var myReadStream = fs.createReadStream(__dirname + '/readMe.txt', 'utf-8');
// 创建写入流(文件名)
var myWriteStream = fs.createWriteStream(__dirname + '/writeMe.txt');

// 读取流.pipe(写入流) ： 读取文件流内容，写入写入文件流
myReadStream.pipe(myWriteStream);
```
# web服务
* 由**http模块**实现web服务器
* 操作类似操作文件流：1、实例化服务器=》2、接收请求request，响应请求response=》3、响应请求时文件流写入响应信息（照着http响应写，有响应头，相应体等）响应内容传给浏览器。

# web服务具体实现 
```
// http模块
var http = require('http');

// 创建服务器(回调函数2个参数(请求, 响应){//...})
var server = http.createServer(function(request, response) {
    // 这里的控制台日志输出是在服务器端的
    console.log("接收请求");
    // 写响应头 writeHead(状态码, {内容})
    response.writeHead(200, {
        'Content-Type': 'text/plain',
    });
    // 写响应体 write
    // response.write('Hello World!');
    // 关闭文件流（结束一次http请求和响应）
    // response.end();
    // 可以直接写 end(响应体);
    response.end('Hello World!');
});

// listen(端口, ip)
server.listen(3000, '127.0.0.1');
console.log("服务器启动了！");
```

# JSON
* Content-Type: application/json
```
// http模块
var http = require('http');

// 创建请求
var server = http.createServer(function(request, response) {
    console.log("接收请求");
    response.writeHead(200, {
        // json的内容-类型为 application/json
        'Content-Type': 'application/json'
    });
    // 定义一个json对象
    var myJson = {
        'name': 'liuhaoyu',
        'job': 'programmer',
        'age': 22
    }
    // 解析一下json对象为字符串 JSON.stringify(json对象)
    // 把字符串形式的json变回对象 JSON.parse(json字符串)
    response.end(JSON.stringify(myJson));
});

server.listen(3000, '127.0.0.1');
console.log("服务器启动了！");
```

# 响应html
* 不要直接写html代码，而是用文件流的形式
* 新建一个html文件，写html代码
* 然后用fs模块创建文件流读取html文件
* 然后在响应时用文件流.pipe写进响应体
```
// http模块
var http = require('http');
// 优化：响应请求时将html文件以文件流的形式发送给浏览器
var fs = require('fs');

// 来个html文本
// var htmlText = 
// '<!DOCTYPE html>' +
// '<html lang="en">' +
// '<head>' +
//     '<meta charset="UTF-8">' +
//     '<meta name="viewport" content="width=device-width, initial-scale=1.0">' +
//     '<meta http-equiv="X-UA-Compatible" content="ie=edge">' +
//     '<title>响应html5代码</title>' +
// '</head>' +
// '<body>' +
//     '<h1>响应成功</h1>' +
//     '<p>恭喜你，成功解锁nodeJS.http.server技能</p>' +
// '</body>' +
// '</html>' ;

// 优化：读取文件流存储在变量中
var htmlText = fs.createReadStream(__dirname + '/html.html', 'utf-8');

var server = http.createServer(function(request, response) {
    console.log("接收请求");
    response.writeHead(200, {
        // html ： text/html
        'Content-Type': 'text/html'
    });
    // response.end(htmlText);
    // 优化：管道传递给响应
    htmlText.pipe(response);
});

server.listen(3000, '127.0.0.1');
console.log("服务器启动了！");
```

# 模块化
* 弄个module.js写源代码，然后暴露exports
```
// 引用系统模块...
// 编写自己的模块...

// 暴露
module.exports.startServer = startServer;
```
* 弄个server.js 引用 module.js，然后跑他的startServer模块方法
```
// 引用模块
var module = require('./module');
// 跑
module.startServer();
```

# 实现简单的路由
* 请求有个属性 request **.url**
* 新建一个文件夹Routes
* 新建3个html页面（假设这个webApp就3个页面）：user.html / home.html / 404.html
* 重点module.js : switch(request.url) 来跳转页面
```
// http模块
var http = require('http');
// 文件操作模块
var fs = require('fs');

function startServer() {
    var htmlText;
    var server = http.createServer(function(request, response) {
        // 先确定所有路由都响应html文件  
        response.writeHead(200, {
            // html ： text/html
            'Content-Type': 'text/html'
        });
        // 获取请求路由 request.url
        // console.log(request.url);
        // 根据请求路由不同，获得不同的html文件
        switch (request.url) {
            case '/':
            case '/home':
                htmlText = fs.createReadStream(__dirname + '/home.html', 'utf-8');
                break;
            case '/user':
                htmlText = fs.createReadStream(__dirname + '/user.html', 'utf-8');
                break;
            default:
                htmlText = fs.createReadStream(__dirname + '/404.html', 'utf-8');
                break;
        }
        htmlText.pipe(response);
    });

    // 启动服务
    server.listen(3000, '127.0.0.1');
    console.log("服务器启动了！");

}

// 可以直接exports暴露模块
exports.startServer = startServer;
```
* 还是使用模块化的方法
```
// 引用模块
var module = require('./module');
// 跑
module.startServer();
```

# 获取get / post
* module.js
```
// http模块
var http = require('http');
// 文件操作模块
var fs = require('fs');
// url操作模块
var url = require('url');

function startServer() {
    var htmlText;
    var server = http.createServer(function(request, response) {
        response.writeHead(200, {
            'Content-Type': 'text/html'
        });
        // 获取地址
        var pathname = url.parse(request.url).pathname;
        // 获取GET方式传递的参数 url true返回对象 false返回字符串
        var params = url.parse(request.url, true).query;
        // 获取POST方式传递的参数
        var data = "";
        // 错误时
        request.on(error, function() {
            console.log(error);
        // 接收时
        }).on('data', function(chunk) {
            data += chunk;
        // 接收完成后 
        }).on('end', function() {
            console.log(data);
        });

        switch (pathname) {
            case '/':
            case '/home':
                htmlText = fs.createReadStream(__dirname + '/home.html', 'utf-8');
                break;
            case '/user':
                htmlText = fs.createReadStream(__dirname + '/user.html', 'utf-8');
                break;
            default:
                htmlText = fs.createReadStream(__dirname + '/404.html', 'utf-8');
                break;
        }
        htmlText.pipe(response);
    });

    // 启动服务
    server.listen(3000, '127.0.0.1');
    console.log("服务器启动了！");

}

// 可以直接exports暴露模块
exports.startServer = startServer;
```

* 改一下
```
// 引用一下queryString模块
var queryString = require('querystring');

// ...

// 根据method方式获取参数
        var data = "";
        // 错误时
        request.on(error, function() {
            console.log(error);
        // 接收时
        }).on('data', function(chunk) {
            data += chunk;
        // 接收完成后 
        }).on('end', function() {
            // 判断method
            if(request.method ** 'POST') {
                // post 就用queryString模块.parse() 把数据变成对象
                data = queryString.parse(data);
            }else {
                // get 就用url.parse() 把数据变成对象
                data = url.parse(request.url, true).query;
            }
            console.log(data);
        });
```
# npm是个啥？
* Node Package Manager（node包管理工具）
* ≈ **php.composer**
# 安装
* 安装node.js自动就帮你装上了npm
* 查看npm版本
```
npm -v
```
* 更新 **不建议**
```
#npm 安装 自己安装自己@最新版 -g全局
npm install npm@latest -g
```
* 安装包
```
npm -install 包名
```
* 包下载好了，会新建一个node_modules的目录，包放里面的，**别动它**。
* 搞一个国内镜像 **不建议**
```
# 国内镜像
npm install -g cnpm --registry=https://registry.npm.taobao.org
# 更新cnpm
cnpm install cnpm@latest -g
```
* 搞国内镜像后就要用cnpm命令装包了
```
cnpm install 包名
```
* 系统全局安装某个包，比如webpack
```
cnpm install -g 包名
```

# package.json
* 这货就是记录某个项目安装了那些包。
* 进入项目目录，初始化项目
```
# 会问你无数个问题
npm init
# 不想回答？
npm init -y
```
* 好了，你生成了一个package.json文件
* 安装某个包并且把包和包依赖写进去
```
# 用国内镜像 安装 --包依赖写进package 安装的包叫express一个nodeJS后台框架
cnpm install --save express
```
* 还有其他的作用吗？
```
# 只要有package.json文件，不用给他node_modules文件夹，直接给执行命令，把package.json记录的所有包全部下载下来
npm install
```

# nodemon
* 直接写nodeJS web服务的时候，代码一改就要重启，很烦躁，安一个
```
cnpm install -g nodemon
```
* 用它跑入口文件
```
nodemon 入口文件名
```
* 好了，现在你改一下代码，保存的瞬间，这货帮你重启一次服务器。

# 补充
```
# install 简写为 i
cnpm i 包
```
* 记住，你要把包写进依赖管理package.json里面，必须
```
cnpm install 包 --save
```
* 干掉一个包
```
cnpm uninstall 包
```
> 这样只是干掉了依赖，并没有干掉node_modules里的包源代码。当你开发完成后，最好在生产环境部署的时候，先把node_modules**移除项目文件夹并备份**，确定package.json的依赖关系正确（只要你不乱编辑它，装包卸包都用命令），然后在生产环境服务器上用 npm install 把包下好是最好的方法

* 更新包
```
cnpm update 包
```
* 安装指定版本
```
cnpm istall 包@版本 --save
```

# npm配置
```
# 你初始化一个项目，相当于初始化一个npm包
npm init
# 开始答题
package name: 包名
version: 版本
description: 描述
git repository: git仓库地址
author：作者
license: ISC许可证
最后给你一个初始化的package.json文件内容，问你客官这样行不行，回车yes
初始化了一个package.json
```

# package.json 里的 **scripts**
```
...

"scripts": {
    "test": "echo \"Error: no test specified\" && exit 1" ,
    "yo": "echo \" yo!\" "
},
  
...
```
* 跑它
```
npm run yo
```
# webpack是啥
* webpack是一个打包js或者css代码的工具。
* 为什么要打包呢？普通的js存在缺陷：a.js依赖b.js中的某个变量或者方法，只有在a.js中将b需要的变量或方法写成全局的。（比如变量x 写成window.x，在b中也用window.x读）。非常不方便和危险（如果你用的一个扩展包也有人用window.x为变量名呢）。因此node.js出现了。
* 看代码：modules暴露，index引用modules暴露的变量即可实现上面的功能
    * modules.js
    ```
    var msg = "yo";
    // 如果不暴露，在其他文件是找不到变量b的
    var x = 1;
    
    // 暴露模块
    module.exports = {
        'msg': msg
    };
    ```
    * index.js
    ```
    var msg = require('./modules').msg;

    console.log(msg);
    ```
* 然而这个功能只能在本地（控制台）中用，浏览器其实是读不懂的。所以需要webpack把这两个.js文件编译一下，打个包（浏览器就能读懂编译后的js文件了）。

# 安装
```
# 全局安装
npm install webpack -g
# 有个小坑
#The CLI moved into a separate package: webpack-cli
#Would you like to install webpack-cli? (That will run npm install -D webpack-cli) (yes/NO)n
#It needs to be installed alongside webpack to use the CLI
#上面就是提示你 CLI这个东西 webpack自身没带 你需要装一个 才能使用webpack （webpack4更新的坑）
npm install --save-dev webpack-cli -g
```

# 打包
* 配置： 项目根新建一个 **webpack.config.js** （不能改名字）
```
module.exports = {
    // 入口文件，其他被暴露的模块不用管，webpack打包时会读require代码，它自动取找
    entry: './index.js',
    // 出口文件（打包后生成的文件）
    output: {
        // 文件名
        filename: 'pack.js',
        // 路径
        path: __dirname
    }
}
```
* 打包
```
cmd > webpack
```

# 多个入口文件
* 打包
```
module.exports = {
    // 多个入口文件写成对象的形式
    entry: {
        // key入口名称: value入口文件具体路径
        home : './js/home.js',
        user : './js/user.js',
    },
    output: {
        // 这里的[name]对应entry里的入口名称
        filename: '[name].bundle.js',
        // /dist 是惯例用法，一般npm打包的文件都放这里
        path: __dirname + '/dist',
    }
}
```
* 打包
```
# 就会在当前目录/dist/下打包home.bundle.js & user.bundle.js
webpack
```

# loader
* 新建一个项目
* 初始化项目，安装webpack 和 webpack CLI
```
#不想答题
npm init -y
#--save-dev 可以简写为 -D
npm install webpack -D
#cli 等会跑的时候会提示你安装
```
* 编辑package.json
```
// ...
"scripts": {
    // 这是默认的
    "test": "echo \"Error: no test specified\" && exit 1",
    // 这是我们添加的一条 想要执行？ npm run dopack， --watch是一条不挂断的命令，当任何源码发生改变时，都会根据package.config.js的配置重新打包
    "dopack": "node_modules/.bin/webpack --watch"
},
// ...
```
* 安装两个loader：css-loader & style-loader
```
npm install css-loader style-loader -D
```
* 新建并配置文件webpack.config.js
```
module.exports = {
    entry: './js/index.js',
    output: {
        filename: 'bundle.js',
        path: __dirname + '/dist'
    },
    // 配置module
    module: {
        // 配置rules
        rules: [
            {
                // 正则匹配 .css 后缀文件
                test: /\.css$/,
                // 使用两个loader ， 注意： 顺序从右往左
                use: ['style-loader', 'css-loader'],
            }
        ]
    }
}
```
* 再来几个文件： ./js/base.js & index.js，./css/base.css
    * base.js 假装弄个配置项目debug
    ```
    var debug = true;
    // es6可以直接这么暴露模块
    export{debug};
    ```

    * index.js来个好玩的
    ```
    // es6可以直接这么获取模块
    import {debug} from './base';
    console.log(debug);
    
    // 借用loader加载css
    import '../css/base.css';
    ```
    
    * base.css就设置下<body>的背景颜色即可“#ccc”

* 现在编译 npm run dopack （提示安装cli输入y回车等下载完即可）

* 最后生成一个文件 ./dist/bundle.js，新建一个index.html将其引入，会发现没有css的情况下，body变色了，通过浏览器调试工具发现head多了这一部分代码

```
<style type="text/css">body {
    background: #ccc;
}</style>
```
