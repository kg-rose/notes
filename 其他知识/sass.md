# Sass
* 大白话解释：页面复杂了，CSS写起来很麻烦（代码量很大），SASS写起来简单（代码量很少但效果一样），写完了编译成CSS。
* 最早这个东西不是很受欢迎（因为语法完全不像CSS，所以很多程序员觉得等于学了门新语言，因此选择不用它）。现在比较流行了，不过换了个名字叫 **Scss**，比较像原生的CSS。
* 两个版本：一种是.sass后缀的，老版本的（不像CSS那个版本的），一种是.scss后缀的，新的（更像CSS）
* 举个例子解释下具体作用
```
# css为啥难写，就是因为它只有“常量”
body {
    background: #f5f5f5;
}
    # 好了，思考一下，我现在有N个元素都要这个背景颜色，我是不是得
body, element1, element2 {
    background: #f5f5f5;
}

# sass(scss)怎么弄
$background-color : #f5f5f5; //定义变量
body {
    background: $background-color;
}
```

# 安装
* 装Ruby（sass是ruby写出来的，需要ruby编译成css）[下载地址](http://www.ruby-lang.org/en/downloads/)
* 把 **/ruby/bin/** 放进环境变量Path
* 装Sass
```
# Ruby装好了，自带gem（类似于php.composer） 看看版本
ruby -v
gem -v
# 安装中国gem源
gem sources --add https://gems.ruby-china.org/ --remove https://rubygems.org/
# 看看gem源列表
gem sources -l
# 保证是国内的源，删掉默认的源
gem sources --remove https://rubygems.org/
# 安装compass compass相当于sass的一个扩展库 安装compass直接就装好了sass
gem install compass
# 如果不想装compass 只想装sass
gem install sass
# 查看版本
sass -v
```

# VScode装Sass环境
* 插件库搜一Sass 装上即可

* 我们不学老Sass语法，我们用scss语法。（后缀名必须是.scss）因为它更受欢迎，更像css。
* 编译sass
```
cmd > sass 源文件.scss:新文件.css --style compressed
# --style可以有4个值：
# nested 嵌套输出
# expanded 展开输出
# compact 紧凑输出
# compressed 压缩 （推荐这种）
```
* 上面的方法，编译的sass，源文件和最终生成的css文件在一起，很没有逼格（其实是不方便管理）。我们既然下了compass，就用compass来创建工程，管理代码
```
cmd > cd 到项目路径
compass create
```
* 现在会生成 **/.sass-cache** **/sass** **/stylesheets** 和 **config.rb**
* 配置config.rb
```
# 网页文件路径
http_path = "/"
# css路径
css_dir = "stylesheets"
# sass文件路径
sass_dir = "sass"
# 图片路径
images_dir = "images"
# js路径
javascripts_dir = "javascripts"
```
* 我们就用默认的。在 **/sass**里面写.scss开发，开发完了之后，用命令生成最终的css文件
```
compass complie
```
* 编辑一次.scss， 编译成一次.css 再在浏览器打开，很麻烦。监听 /sass，一旦改动自动编译，我们在浏览器中刷新就可以看到编译后的样式表提供的样式，用这个命令
```
compass watch
```

# 基础
* base.scss
```
// 必须以_下划线开头命名：否则会sass会编译这个文件
body {
    margin: 0;
    padding: 0;
}
```
* style.scss
```
// 引用scss
@import "base";

// 注释
/*
 * 多行注释会保留(除非你压缩编译)
*/
// 单行注释不会被保留
/*!
 * 强制注释无论如何都会存在于编译的css文件中
*/

// 变量定义
$primary-color: #1269b5;
$primary-border: 1px solid $primary-color; // 变量也能引用变量
div.box {
    // 引用变量
    background: $primary-color;
}
h1.page-header{
    border: $primary-border;
}

// 嵌套写法
.nav {
    height: 100px;
    // 嵌套 .nav ul
    ul {
        margin: 0;
        // 嵌套 .nav ul li
        li {
            float: left;
            list-style: none;
        }
        a {
            display: block;
            color: $primary-color;
            padding: 5px;
            // 父选择器：经常用于选择伪类（如果不写会编译成 ul li a :hover【多一个空格，不要空格就必须写上&:hover】）
            &:hover {
                background: $primary-color;
                color: $primary-color;
            }
        }
    }
    // 这里& 就指的是“.nav” &-text就指的 .nav-text 
    & &-text {
        font-size: 50px;
    }
}

// 嵌套用法用于属性
body {
    // font-family, font-size, font-weight前面都是font，把font提出来
    font: {
        family: "微软雅黑";
        size: 15px;
        weight: bold;
    }
    // 用于边框
    .box {
        border: 1px solid #000 {
            left: 0;
            right: 0;
        }
    }
}

// 混合mixin（类似函数）
/*
@ mixin 名字 (参数列表，没参数可以省略) {
    // “函数体”
}
*/
// 定义混合
@mixin example {
    color: #8a6d3b;
    background: #fcf8e3;
    // 混合可以嵌套
    a {
        color: #8a6b2d;
    }
}
// 调用混合
.example {
    // 在调用时，混合中的嵌套会选择调用该混合的元素作为父选择器，写出嵌套的样式代码
    @include example;
}
// 带参数的混合
@mixin example1($text-color, $background-color) {
    color: $text-color;
    background: $background-color;
    a {
        // 调用darken()加深10%的字体颜色
        color: darken($text-color, 10%);
    }
}
$ex-color : #f5f5f5;
.example1 {
    // 调用有参数的混合时传参可以传变量 也可以传“常量”
    @include example1($ex-color, #d5d5d5);
}

// extend
.extend {
    padding: 15px;
}

.extend a{
    font-weight: bold;
}

.extend-1 {
    // 继承.extend和以.extend为父选择器所写的样式里所有的样式
    @extend .extend;
    background: #dedede;
}

```
# 解释
* _name.scss文件和@import：_name.scss不会被编译，但是可以在其他的.scss文件中用@import引入。（相当于多了这一段代码）
* 三种注释： **//这种在编译后不保留** **/*这种保留，压缩编译就不保留*/**，**/*!这种一直存在*/**。
* 亲切的$符号定义变量。但是不亲切的是，赋值用“:”
* 嵌套写法就是{样式里面写选择器{再写样式可以再嵌套选择器和{样式}}}
* 混合就像是个“函数”，定义用 **@mixin 混合名(参数列表) {//具体样式}** 调用的时候用 **@include 混合名(参数列表)**
* 样式可以被继承，用 **@extend 选择器** 会继承该选择器的样式和以该选择器为父选择器的所有其他样式。

# Sass中的数据类型
* 使用命令行学习
```
# 输入 sass -i 命令后就可以进入一个类似调试模式的状态
sass -i
# 输入 type-of(参数) 函数来看看所输入的数据是什么数据类型的
type-of(5) #number 数字
type-of(5px) #number 带px em这些单位的也是数字
type-of(hello) #string
type-of('hello') #string
type-of(10px 5px) #list “列表类型”
type-of(1px solid #fff) #list 同样是，像css某些属性有需要多个值的那种都是列表属性
type-of(red) #color string中可以被css识别为颜色的是color
type-of(#fff) #color 
type-of(rgba(255,255,255,.1)) #color
```

# 数据可以根据自身的类型不同调用一些函数和运算符进行计算
* sass -i 进入“调试模式”
* number 进行运算
```
2 + 8
10 #加法
2 - 8
-6 #减法
2 * 8
16 #乘法
8 / 2
8/2 #无法生效：因为css中有的要用/符号，但是是其他意义比如： font: 16px/1.8 serif
(8 / 2) 
4 #除法 需要用括号包起来
5px + 5px
10px #带单位的也是number
5px - 1px
4px
5px * 2px
10px*px #这里很明显不对，应该写成
5px * 2
10px
(5px / 5)
1px #乘除法 乘的倍数不能带单位
3+2 * 5px
13px #先乘除 再加减
(3+2) * 5px
25px 
```
* number 类型可以用函数
```
# abs() 求绝对值
abs(-10px)
10px

# round() 四舍五入取整
round(3.2px)
3px
# ceil() 向上取整
round(3.1px)
4px
# floor() 向下取整
floor(3.9px)
3px

# percentage() 求百分比
percentage(65px / 100px)
65%

# min() 求最小值
min(1px, 3px, 8px)
1px
# max() 求最大值
max(1px, 3px, 8px)
8px
```

* string 运算
```
# 注意 没有引号的string不能有空格 "有引号的 可以有空格"

# +类似于字符串连接符
liu+"haoyu"
"liluhaoyu" # 但是注意：在CSS中会被编译成 liuhaoyu 没有引号（第一个字符没引号，那么结果没引号）
"liu"+haoyu
"liuhaoyu" #在css中会被编译成带引号的
liuhaoyu-haoyu

# -和/ 也是连接符不过会在中间带上他们
"liuhaoyu-haoyu" # -也是类似于连接符，不过会在两个字符中间加上-
liuhaoyu/haoyu
"liuhaoyu/haoyu" #同理

# 没有*乘法
```

* string 函数
```
# 全转小写
to-lower-case($string)
# 全转大写
to-upper-case($string)
# 求长度
str-length($string)
# 求某个字符或字符串的起始位置索引
str-index($string, "要找的字符串")
# 插入字符串
str-insert($string, "要插入的字符串", 插入位置索引int)
```

* 颜色属性在css中的表达方式
    * 直接写red / green / blue
    * rgb(红,绿,蓝)
    * rgba(,,,透明度)
    * hsl(h色相,s饱和度%,l明度%)
    * hsla(,,,透明度)

* 颜色常用函数
```
# 变亮/暗
lighten($color, x%)
darken($color, x$)
```

* list 函数
```
# 把list当成数组
length($list) #取长度
nth($list, 1) #根据索引取值
index($list, 5px) #根据值取索引
append($list, solid) #在末尾插入值
join($list1, $list2) #合并list
```

* map 函数
```
# 把map当成key-value类型的键值对数组
$colors:(light:#fff, dark:#000) #定义一个map(key:value, key:value)

# 取长度
length($colors)
# 看key名
map-keys($colors)
# 看value值
map-values($colors)
# 根据key判断有没有value
map-has-key($colors, $key)
# 根据key调用value
map-get($colors, $key)
# 合并多个map
$colors:map-merge($colors, (red: #f00))
```

* 比较运算符和boolean值
```
5px>3px true
5px>=5px true
5px>10px false
5px**10px false

# 与and&
(5px>=5px) and (5px>3px)
true

# 或or|
(5px>=5px) or (5px>10px)
true

# 非not!
not(5px > 10px)
true
```

* interpolation
```
// interpolation的使用
// #{变量名不要$}

// 用于注释
$version: "0.0.1";
/*!
 当前版本: #{$version}
*/

// 用于简写
$name: "info";
$attr: "border";
.test-#{name} {
    #{attr}-color: #ccc;
}
```

# 判断、循环、遍历
```
// if的使用
$use-prefixes: true; //使用浏览器前缀？ 默认否
$theme: "dark";
.rounded {
    // 判断一下
    @if ($use-prefixes) {
        -webkit-border-radius: 5px; //Chrome Safari等用webkit引擎的浏览器 
        -moz-border-radius: 5px; //火狐和用Mozilla引擎的浏览器
        -ms-border-radius: 5px; //Ie和微软的一些浏览器
        -o-border-radius: 5px; //Opera
    }
    border-radius: 5px;
    // @if @else if @else
    @if ($theme ** dark) {
        background-color: black;
    }@else if ($theme ** light) {
        background-color: white;
    }@else {
        background: #ccc;
    }
}

// for的使用
// @for $value from <开始值> through <结束值> {}
$columns: 12;
@for $i from 1 through $columns {
    .col-#{$i} {
        width: 100% / $columns * $i;
    }
} //生成类似bootstrap 格栅系统的css代码

// each的使用: 遍历list
$icons: success error waring; //定义list
// @each $自定义的元素变量 in $列表变量
@each $icon in $icons {
    .icon-#{$icon} {
        background: url(../img/#{$icon}.png);
    }
} //一段代码定义多个背景图片样式

// while 条件 {}
$i: 0;
@while $i < 10 {
    .item-#{$i} {
        width: 5px * $i;
    }
    // 注意 $i++ 没法用
    $i: $i+2;
}
```

# 自定义函数
```
// @function 函数名 (参数列表) {}
$colors: (light: #fff, dark: #000);

@function color($key) {
    @if (not(map-has-key($map: $colors, $key: $key))) {
        // @warn 提示信息（编译的时候更好的报错提示）
        @warn "在 $colors 里没有找到 #{$key} 这个键";
    }
    // @return 返回值
    @return map-get($colors, $key);
}

body {
    // 调用函数 属性: 函数(参数);
    background-color: color(故意错);
}
```

* bootstrap 中的 .btn .btn-success/danger/warning/info这些怎么来的？
```
// 定义混合
@mixin theme-color ($element, $sbg, $ibg, $wbg, $ebg, $sbd, $ibd, $wbd, $ebd) {
    .#{$element}-success {background-color: $sbg; border-color: $sbd}
    .#{$element}-info {background-color: $ibg; border-color: $ibd}
    .#{$element}-warning {background-color: $wbg; border-color: $wbd}
    .#{$element}-error {background-color: $ebg; border-color: $ebd}
}

// 定义主选择器
.btn {
    // 字体颜色
    color: #fff;
    // 嵌套中调用混合
    @include theme-color("btn", "green", "blue", "yellow", "red", "green", "blue", "yellow", "red");
}
```
