# 一句话概括ES6 和 JS 的关系
* ES是标准 JS是实现
> ECMAScript 6.0（以下简称 ES6）是 JavaScript 语言的下一代标准，已经在 2015 年 6 月正式发布了。**它的目标，是使得 JavaScript 语言可以用来编写复杂的大型应用程序，成为企业级开发语言。**
* 日常喊法，ES ≈ JS。
* ES6现在来说泛指5.1版本后的Javascript标准。
* 从2015年开始后，越来越多的浏览器开始支持ES6标准的，当然即使如此它仍然不够普及（用户浏览器上跑的，程序员们的写法，大部分还是ES5标准）。

# var 变成了 let定义变量 和 const定义常量
* ES6以前：都用var
```
// var 和 function 存在变量提前声明
// var 只会提前声明 function既声明又定义
console.log(a); // a=undifined
console.log(getA()); // '123'
var a = 1;
function getA() {
    return 123;
}
// var 和 function 定义的变量可以重复声明重新赋值
var a = 1;
var a = 2;
function getA() {}
// 全局作用域下 var 和 function 定义的变量相当于给全局对象window增加属性window.变量名 = 值
console.log(window.a);
console.log("getA" in window);
```
* let
```
// let 变量

// console.log(a); //报错：a is not defined “a未被定义”
// 【1】let 定义的变量没有提前声明的特点
let a = 1;

// 【2】let 定义的变量无法像 var 一样重复声明
// let a = 2; //报错：'a' has already been declared “a已经被定义”

// 【3】let 定义的变量不会在全局对象window下生成新的属性
console.log(window.a);

// 越来越严谨...
```
* const
```
// const 常量

// 1、无法提前声明 2、不可以重复声明 3、全局作用域下不会给window对象增加属性（ES6通用）
let a; //let 定义变量可以不给值 值默认为undifined
console.log(a);
a = 1; //let 定义的变量不能重新定义重名变量 但可以重新赋值
console.log(a);

// const b; //会报错：Missing initializer in const declaration “忘记给const定义的常量一个初始值”
// 【4】 const定义的常量一旦声明必须赋值
const b = 1;

// 【5】const定义的常量一旦声明将无法在程序运行中重新赋值
// const b = 2; //会报错：Identifier 'b' has already been declared “b已经被定义了”
```
* {块级作用域}
```
// {块级作用域} 
{
    let a = 1;
    console.log("第1作用域a=",a);;
    var b = 1;
}
console.log("b即使在块级作用域中定义，依旧是全局的,b=",b);
{
    let a = 2;
    console.log("第2作用域a=",a);
    var b = 2;
}
console.log("b即使在块级作用域中定义，依旧是全局的,同时依旧会给window增加属性window.b",window.b);
console.log("块级作用域下，var和function声明的变量依旧是全局的。");
// console.log(a); // 报错：a is not defined “a未定义” 在块级作用域下a定义的变量只在作用域内有效

/**
 * 实际应用
 * 对象{} for(){} if(){}
 */

// ES6中，花括号想表示对象，{}不可以放在行首。（ES6以为那是一个块级作用域）
let obj = {name:"liuhaoyu",age:10};
({name:"liuhaoyu", age:10});
eval('var o = {name:"liuhaoyu", age:10}'); //eval将字符串转成对象的时候也有这个问题

// if()
if(1) {
    var x = 1;
    let y = 1;
    function getX() {
        return x;
    }
}
// {里面用var} 下面的变量会提前声明（即使if(false){} x依然等于undifined**但 function 不会:function要if条件为true的时候,先给函数声明赋值,再执行if里面的代码**） 
// {里面用var 和 function} 下面的变量会存在于全局作用域（下面两个永远存在）
console.log(x);
// console.log(y); //y is not defined , if 里面 let 定义的变量 , 只在{里面跑,跑完出不来}
console.log(getX());

for(var i=0; i<5; i++) {
    console.log("现在i=",i);
}
console.log("for循环结束后,i=",i);

for(let j=0; j<5; j++) {
    console.log("现在j=",j);
}
// console.log("for循环结束后,j=",j); //j is not defined
console.log("let定义的变量j,for循环跑完就没了");
```
* 总结：比以往更加严谨，let声明过的变量无法再重新声明，let存在作用域的概念，最显著的提升应该是for循环那一部分，跑完就释放了很好。const在程序执行时无法修改。

# 解构赋值
* 什么是解构赋值：等号左右两边结构一样（或按照一定规则类似）。左边的变量逐一取值对应右边的每个元素或属性。
* 数组解构
```
// 什么是解构赋值,数组举例:
// 先来个数组
let arr = [1, 2, 3];
// 现有变量 x,y,z想分别等于数组里的1,2,3
// ES5的写法
// let x = arr[0];
// let y = arr[1];
// let z = arr[2];
// ES6提供的解构赋值
let [x, y, z, l, m, n] = arr;
console.log("x=",x,",y=",y,",z=",z);
// 多出来的 = undifined
console.log("l=",l,",m=",m,",n=",n);

// 设默认值 x2=10,只有后面解构的值是undifined的时候,才会取默认值
let [x1, x2=10] = [1, undefined];
let [y1, y2=10] = [1]; // 不写就是undifined
console.log(x1, x2);
console.log(y1, y2);

// 省略赋值 想让m1=1,m2=3:把中间空出来即可
let [m1, , m2] = [1, 2, 3];
console.log(m1,m2);

// 不定参数赋值 n1=1,n2=2,n3将会定于后面所有的元素组成的数组[3,4,5,6,7]...
let [n1, n2, ...n3] = [1,2,3,4,5,6,7]; // ...扩展运算符
console.log(n3);
```
* 对象解构
```
// 对象解构赋值 要求变量名和属性名是一致的(name=obj.name)
// let {name, age} = {name:'liuhaoyu', age:10};
// console.log(name);

// 设默认值
let {name, age=100} = {name:'liuhaoyu'};
console.log(age);

// 先定义再赋值
let x1,x2;
// {x1, x2} = {x1:1, x2:2}; //块作用域的问题
({x1, x2} = {x1:1, x2:2}); //这么写就好了 {}不再行首即可
```
* 数组对象嵌套解构（只要左边结构和右边一样即可）
```
let obj = {
    name: 'liuhaoyu',
    age: undefined,
    // 嵌套个数组
    array: [
        'a1',
        'a2',
        'a3',
        'a4',
        // 数组里嵌套个对象
        {
            o1: 'o1',
            o2: 'o2',
            o3: 'o3'
        }
    ],
}

// 如何解构:照着结构写就行了,注意数组后面有个:
let {name, age, array:[a1,a2,a3,a4,{o1, o2,o3}]} = obj;
console.log(name,age,a1,o1);
```

* 数据类型不对应的结构
```
// 使用数组解构字符串:会将右边字符串转换为数组
let [x, y] = "123";
// let [m, n] = 1; //会报错:1 is not iterable 要求必须右边是一个有length属性的东西(类数组:比如dom操作获取一组元素,普通数组,Set对象,Map等)
console.log(x,y);

// 使用对象解构 : 如果右边不是对象,会转为对象,再进行解构赋值
// let {__proto__:a} = 1; 这句话的意思是,将 1这个对象中的proto属性用变量a接收.
let {__proto__:a} = 1;
console.log(Object(1)); //看看1转为对象是什么样子
console.log(a); // undefined

let {length:objLen} = "1234";
console.log(objLen); 
```

* 在参数列表里解构
```
// 在参数列表中解构数组
// 要求a等于数组第一个元素,b等于数组第二个元素,c等于剩下所有元素组成的数组
function getA([a,b,...c]) {
    console.log(a);
    console.log(b);
    console.log(c);
}
let arr = [1,2,3,4,5,6,7,8,9];
getA(arr);

// 在参数列表中解构对象 还能给默认值
function getO({name, age=100}) {
    console.log(name);
    console.log(age);
}
let obj = {name:'liuhaoyu', age:undefined};
getO(obj);

// 在参数列表中解构对象 给默认值,要求不传任何参数不报错依然可以打印name和age
function getObj1({name='liuhaoyu', age='100'} = {}) {
    console.log(name);
    console.log(age);
}
// 看看这种写法
function getObj2({name, age} = {name:'liuhaoyu', age:100});
getObj1(); 
getObj2(); //不写都一样
getObj1({}); // 传了一个空对象.默认值为空对象,没问题
getObj2({}); // 传了一个空对象,默认值为{name:'liuhaoyu', age:100},现在值为{},所以会出现name,age = undefined
```

# 字符串方法扩展
```
// 看看字符串原型有哪些方法和属性
// console.log(String.prototype);

let str = "abcdefg12345";

// 1 includes("指定字符串string", 开始查找的位置int默认0) 返回 true|false
console.log(str.includes('cdef', 3));

// 2 startsWiths("指定字符串string", "开始查找的位置int默认0") | endsWith, 判断字符串是否以指定字符 开头 | 结尾
console.log(str.startsWith('bcd', 1));
console.log(str.endsWith('345',3)); //endsWith第二个参数是判断:前几个字符组成的字符串是否以第一个参数结尾

// 3 repeat(重复多少次)
console.log(str.repeat(3));

// 4 ES7新增 : padStart(最终长度int, "指定字符string") padEnd 按照指定字符,补全字符串的长度
let str1 = 'ab';
console.log(str1.padStart(5,"cde")); //补在前面
console.log(str1.padEnd(3,"cde"));  //补在后面 , 长度超了自动截取 
```

# **模板字符串**
```
// `` 包起来,跟普通字符串一样
let str = `哈哈`;
// 但是可以添加变量 ${}
let strMid = 'ha';
str = `哈 ${strMid} 哈`;
console.log(str);

// 实际应用: js动态渲染网页
var content = "我是要被渲染进去的内容";
// ES5中
var htmlText = 
    '<div>' +
        '<span>' + content + '</span>';
    '</div>';
// 插入 $("body").innerHTML(htmlText);

// ES6中
let htmlText1 = `
    <div>
        <span> ${content} </span>
    </div>
`; 

```

# 数组方法扩展
```
/**
 * 数组的空位 : 数组某个索引位置没有任何值empty *undefined不是空位*
 */
let arr = [,undefined,,,]; //一个 , 一个空位
console.log(arr.length);
// in: 判断数组索引位置上有没有值 undefined不是空位 返回值为true
console.log(1 in arr);

// 在ES5中 数组方法对空位的处理不一致 一般直接跳过空位
let arr1 = [1,2,,,,3];
arr1.filter(function(item) { // filter是ES5的方法
    console.log(item); //跳过空位
});

// 在ES6中 将空位处理为undefined 
arr1.find(function(item) {
    console.log(item);
});
for(let item of arr1) {
    console.log(item);
}

// 面试题 得到一个有7个1的数组
console.log(Array(7).fill(1));
```

# 对象扩展
* 更简洁的表达方式
```
let name = "liuhaoyu", age = 100;
let person = {name, age};
// let person = {name:name, age:age};
let str = 'name';
let obj = {
    fn(){},
    // fn:function(){}
    // 属性名是字符串
    str: name, //这个是把name 赋值给了 obj.str
    // [变量]: 值, //这样可以把变量的值当作属性名key.
    [str]: name, //这个是把name 赋值给了 obj.name
    ["my"+str]: name, //还可以拼接
}
console.log(obj.name);
```
* set和get
```
// 普通set
// obj.name = 'liuhaoyu';
// 普通get
// obj.name
let obj = {
    get name() {
        // 这个函数只要调用 obj.name时 就会触发
        // 可以通过return 返回值
        return 'liuhaoyu';
    },
    // 必须有1个参数
    // set name(value) {
        // this.name = value;
        // 不能这么写,会一直调用自己
    // }
}
console.log(obj.name);
```
* 对象的扩展方法
```
// Object(); //将参数变成对象
// console.log(Object(1));

// is 判断两个值是否相等
// ES5 "**=" 判断的问题 : NaN !** NaN (NaN和任何值都不等) | -0 **= 0 (-0和0又相等)
console.log((NaN **= NaN)); //false
console.log((-0 **= 0)); //true
// 用Object.js判断
console.log(Object.is(NaN, NaN)); //true
console.log(Object.is(-0, 0)); //false

// assign 合并对象
let obj1 = {name:'liuhaoyu'};
let obj2 = {age:10};
Object.assign(obj1, obj2); //将第个对象的所有属性 合并到第一个对象
console.log(obj1); //第一个会变
console.log(obj2); //第二个不变
obj1 = {name:'liu'};
obj2 = {name:'liuhaoyu', age:20};
Object.assign(obj1, obj2);
console.log(obj1); //如果两个属性名称重复 那么第二个会覆盖第一个的值
console.log(obj2);

// ES7中提供的 对象的扩展运算符 ... 合并对象
let o1 = {name:'liu'};
let o2 = {name:'hao'};
let me = {...o1, ...o2}; // 这样合并为一个新对象,原对象o1 o2不变
console.log(o1);
console.log(o2);
console.log(me);

// getOwnPropertyDescriptor 获取一个对象中的某个属性的描述
let desc = Object.getOwnPropertyDescriptor("123", length); // 获取对字符串对象的 length的描述
console.log(desc);
// 具体打印的属性
// configurable: false #不可配置 (是否可以删除这个属性)
// enumerable: true #可枚举
// value: "1" #具体值
// writable: false #不可修改

// keys() 返回值 数组 [所有可枚举的属性]
console.log(Object.keys(me));

// values() 返回值 数组 [所有可枚举属性的键值]
console.log(Object.values(me));

// entries() 返回值 数组 [[键], [值]]
console.log(Object.entries(me));
```

* proxy
```
let user = {}; // 有一个人
user.fname = 'Horry'; //名hoory
user.lname = 'yoo'; //姓yoo
// user.name = user.fname + '.' + user.lname; //他的全名就是Horry.Yoo
console.log(user.name);
// 每次算name很麻烦
user = {
    fname: 'Horry',
    lname: 'Yoo',
    name: function() {
        return this.fname + '.' + this.lname;
    }
};
// 想要获得全名name()
console.log(user.name());
// 这个()看着很刺眼,我希望全名是个属性而不是个方法
let newUser = new Proxy({}, {
    get: function(obj, prop) {
        if(prop **= 'name') {
            return obj.fname + '.' + obj.lname;
        }
    }
});
/**
 * new Proxy({原对象}, {
 *  // 代理对象
 *  get: function(obj表示原对象, prop表示叫的属性名) {
 *   // 判断prop的值 返回叫的指定属性的值
 *  }
 * })
 */
newUser.fname = 'haoyu';
newUser.lname = 'Liu';
console.log(newUser.name);
```

# 新的数据类型和对象
* Symbol
```
/**
 * Symbol 是ES6新增的一个基本数据类型
 */
// 定义 Symbol() 
let sym1 = Symbol();
let sym2 = Symbol();
console.log(typeof(sym1));
console.log(typeof(sym2));
console.log(sym1 **= sym2); // 不全等
// Symbol数据类型的特点 : 跟字符串差不多 但是使用Symbol()函数得到一个数据 每一个都是完全不同的
// 可以接收一个参数('描述这个Symbol')
// 即使描述一样 值也是不一样的
sym1 = Symbol('foo');
sym2 = Symbol('foo');
console.log(sym1);
console.log(sym2);

// 具体作用: 作对象属性的名称,防止对象属性被重写
let person = {
    name: 'haoyu',
}
person.name  = 'horry'; //加入我们引用了一个person对象,但不知道person里有name属性,我们大胆地把它改了
//这样做很危险,可能有其他的代码依赖这person.name这个属性,我们把它改了可能直接导致整个程序崩掉
console.log(person);
// ES6中解决它:
let name = Symbol('name');
person = {
    [name] : 'haoyu',
}
person.name = 'horry';
console.log(person);
// 再看个例子
// file1.js
let p;
{
    let n = Symbol('name');
    p = {
        [n] : 'file1',
    };
}
// file2.js
{   
    let n = Symbol('name');
    p[n] = 'file2';
}
console.log(p);
// 你会发现p对象里有两个
// Symbol(name): "file1"
// Symbol(name): "file2"
// 即在不同的作用域, Symbol的值无法改变
```

* Set
```
/**
 * Set 
 * 可以理解为一个元素的值绝对不会重复的强数据类型数组
 */

// 先来个数组
let arr = [1, 2, 3, 3]; //可以写2个3
// 定义set : new Set(传个数组进来)
let set = new Set(arr); 
console.log(set); //你会发现3只出现了一次
set = new Set([1,'1', 2, '2', 3, '3']);
console.log(set); //在set里 1!='1' 即值没有重复

// Set的一些常用属性和方法
// Set.size ≈ Array.length
console.log('size中共有元素:', set.size);

// 添加和删除 元素
set.add(4); //添加元素
set.add(4); //添加元素
// 添加两次也不会报错,只是依然只有一个数字4存在Set中
set.delete('2'); //删除元素
set.delete('2'); //删除元素
// 删除同理,删了再删,不会报错

// 看看有没有某个元素 有true 没有false
console.log(set.has(5));

// 清空Set
set.clear();
console.log(set);
```

* Map
```
// 创建一个Map
// new Map([[key, value], [key, value], [key, value]]); 
// 参数要求 传进来的是一个二维数组 一维数组必须是[第一个元素当key, 第二个元素当value];
let map1 = new Map([[1,'liu'], [2, 'hao'], [3, 'yu']]);
console.log(map1);
// 一个对象 属性名必须是字符串,如果你写的不是字符串也默认转换为字符

let o = {};
let a = [];
let obj = {
    true: 'true', //前面的true 就不是关键字"真"了 而是可以通过obj.true调用的一个属性的属性名
    1: 1, //同理前面的1也不是常量1,而是obj中的一个属性的属性名
    [a]: [],
    [o]: {}, // 即使我们在对象外面去定义变量然后用[变量名]这样的方式创建对象的属性,属性名依然会是一个字符串
}
// console.log(obj); //也就是说无论如何, 属性名一定是个字符串

// Map解决了这样一个问题: 属性名(key)可以是任意数据类型
let map2 = new Map([['name','liuhaoyu'], [true, 'true'], [{age:12}, {age:12}], [[1,2,3], [1,2,3]]]);
console.log(map2); //上面的Map , 四个键值对的key 分别是 字符串, 布尔, 对象, 数组
// 当然对象和数组这种可以给它一个第一参数[暂时叫做代号吧, key, value]
map2 = new Map([['name','liuhaoyu'], [true, 'true'], [o, {age:12}, {age:12}], [a, [1,2,3], [1,2,3]]]);

// Map常用的属性和方法
// size 键值对的个数
console.log(map2.size);

// get 通过key或代号获取value
console.log(map2.get('name'));
console.log(map2.get(true));
console.log(map2.get(o));
console.log(map2.get(a));

// set 通过key或代号新增或修改value
map2.set('age', 20); //没有age 新增age
map2.set('name','horryyoo'); //有name 修改name
console.log(map2);

// has 通过key判断有没有对应的value值
console.log(map2.has('age'));

// delete 通过key或代号干掉一个属性 找到元素并删除成功为true 找不到或删不掉为false
console.log(map2.delete('age'));
console.log(map2);

// 清空 没有返回值
// map2.clear();
// console.log(map2.size);

// 遍历forEach(顺序必须是 参数1是值, 参数2是键, 参数3是实例)
map2.forEach((value, key, instance)=> {
    console.log(value); //value
    console.log(key); //key
    console.log(instance); //实例
});

// for of + Map.keys() 遍历所有的key
for (let key of map2.keys()) {
    console.log(key);
}

// for of + Map.values() 遍历所有的value
for (let value of map2.values()) {
    console.log(value);
}

// 练习:给你一个数组变成Map实例
let ary = ['liuhaoyu', 'JS', 'ES6', 'AngularJS2+', 'Laravel'];
let map3 = new Map();
for (let [index, item] of ary.entries()) {
    map3.set(index, item);
}
console.log(map3);
```

# oop => 类的扩展
# class关键字 和constructor()构造函数
```
// ES5 通常是这样弄个对象出来的
// 类定义
function Fn() {
    this.x = 10;
}
// 实例化
var f = new Fn();

// ES6 有 class 了
class A {
    // 同时有构造函数了
    constructor(x, y) {
        this.x = x; // 添加私有属性
        this.y = y;
    }
}
let a = new A(1,2);
console.log(a);
```

# 类的“立即执行” 和 变量提前声明
```
// 立即执行 let 变量 = new class {构造函数(){}}("传入参数立即执行");
let aa = new class {
    constructor(name) {
        this.name = "name";
    }
}("liuhaoyu");
console.log(aa);

// ES5 老方法定义类和实例化时，变量会提前声明
let ff = new FF();
console.log(ff);
function FF() {
    this.f = "ff";
}
// ES6
// let c = new C(); //ES6中没有变量提声：这里会报错找不到C
class C {
    constructor() {
        this.c = 'c';
    }
}
let c = new C(); //ES必须写在后面
```

# 继承 extends
```
class A {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }
    fn1() {
        console.log('我是A.fn1()');
    }
    static fn2() {
        console.log('我是A.fn2()');
    }
}

// 继承extends
class B extends A {
    constructor(x, y) {
        // this.y = y; // 报错 ust call super constructor in derived class before accessing 'this' or returning from derived constructor
        // console.log(this); // 子类没有 "this" 它的this是父类，因此需要调用super()方法
        super(x, y);
        // 在super()执行完成后，就可以使用this了
        alert(this.y);
    }
    // 当然 子类的constructor()构造函数是可以省略的，它会自动继承父类的constructor()方法
    // 所以上面所有代码都在“脱了裤子放屁”
    // 方法可以重写
    fn1() {
        console.log('我是B.fn1()');
    }
    static fn2() {
        console.log('我是B.fn2()');
    }
}
let b = new B(1,2);
b.fn1();
A.fn2();
B.fn2();
console.log(b);
```

# 静态方法
```
// static 定义静态方法 （比如Array.of) 
class AA {
    static myStaticFn() {
        alert('这是一个静态方法，只有类本身才能调用。');
    }
}
AA.myStaticFn();

// 静态方法无法被实例拿到
// let aa = new AA();
// aa.myStaticFn(); // 报错 aa.myStaticFn is not a function “对象是继承不到静态方法的”

// 静态方法也无法被继承
class B1 {
    static myStaticFn() {
        console.log('我是b1的静态方法');
    }
}
class B2 extends B1 {}
// console.log(B2.myStaticFn()); //undefined
// 但是我非要拿到B1的静态方法怎么办呢？
class B3 extends B1 {
    // 1、再定义一个静态方法
    static myStaticFn2() {
        // 2、这个方法执行调用 super这里相当于父类B1.myStaticFn()父类的静态方法
        super.myStaticFn();
    }
}
// let b3 = new B3();
// console.log(b3.myStaticFn2());
```

# 函数方面的扩展
* **默认值问题**
```
/**
 * 参数默认值问题
 */
// function fn(x,y) {
//     // ES5我们这样设置默认值 如果 x为真 函数内的x = x 否则 = 我们设置的默认值'liuhaoyu'
//     x = x || 'liuhaoyu';
//     y = y || 'ES6';
// }
// // 问题来了
// fn(0,0);    //这样传进去 0被读成false 则函数内x = 'liuhaoyu'
// ES6支持参数列表中设置默认值
function fn(x='liuhaoyu', y='ES6'){};
// ES6参数列表还支持解构赋值
function fn1({name="liuhaoyu", age="100"}={}) {
    console.log(name);
    console.log(age);
}
fn1();

/**
 * function.length 属性 没有默认值的形参的个数
 */
function fn2(x, y) {

}
fn2(1,2);
console.log(fn2.length);

/**
 * 参数默认值位置
 * 一般参数的默认值都放在最后面
 */
// function fn3(x=10, y=20) {}

// arguments 类数组 实参集合
function fn4(...arg) {
    // ES5 获取实参 集合
    console.log(arguments); //对象
    // ES6 获取实参集合 参数列表中解构取值
    console.log(arg); // 数组
}
fn4(1,2,3,4,5);

// 参数作用域
let x = 100;
function fn5(x, y=x) {
    console.log(y); //为什么y=1 而!= 100呢
}
fn5(1);
/**
 * 解释: 参数列表作用域是这么找值的
 * x=1 进来了 y=x 这里的这个x是先找函数里面的x,没有,才会找函数外面的x(上面的x=100)
 */
```

* 函数名问题
```
function fn() {

}

// 获取当前函数名
console.log(fn.name);
// 匿名函数名
console.log((function(){}).name);

// 特殊情况
// 1. 通过bind方法得到一个新的函数 name是 "bound 原来函数名字"
let fn1 = fn.bind(null);
console.log(fn1.name);
// 2. 通过构造函数创建一个函数 new Function("形参", "函数体") name 是 "anonymous" 即匿名
let fn2 = new Function("x,y", "console.log(x,y); return x+y;");
console.log(fn2(10,100));
console.log(fn2.name); 

// 面试题 禁止使用eval() 使用new Function的方法 取得str 的json字符串
let str = '[{"name": "珠峰"}, {"age":100}]';
let arr = (new Function("return" + str))();
// console.log(arr);
// arr **> [{"name": "珠峰"}, {"age":100}]
```

* 扩展运算符：更多的是操作数组
```
/**
 * 扩展运算符 ...
 */
// 将非数组变成数组(类数组 length) [...]
let str = "123";
console.log([...str]);
function fn(str) {
    console.log([...arguments]);
}
fn(str);

// 将数组变成非数组
let arr1 = [1,2,3,4];
let arr2 = [10,20,30,40];
// 合并他们
newArr1 = arr1.concat(arr2); //可以用Array.concat()函数
newArr2 = [...arr1,...arr2]; //也可以使用扩展运算符把他们连起来
console.log(newArr1);
console.log(newArr2);

/**
 * 实际应用
 */
// 求数组最大值
let ary = [1,23,12,45,242,132];
// Math.max(ary); //×
// Math.max.apply(null, ary); //√
let max = Math.max(...ary); //把数组展开
console.log(max);

// 把数组展开传进参数列表
function fn1(...ary) {

}

```

* 箭头函数
```
function fn(x, y) {

}
// 箭头函数都是匿名函数
// let 函数名 = (参数列表) => {函数体}
let fn1 = () => {};
// 参数列表只有一个参数 函数体只有一段代码 可以省略参数列表 函数体可以省略{} 和 return
let fn2 = x => x+1;
// 通常函数当作参数的时候使用箭头函数
let ary = ['liu', 1, 2, 3, 'haoyu'];
let newAry = ary.filter(item => typeof item **= "number");
console.log(newAry);

/**
 * 特点
 */
// 1. 箭头函数没有this指向 里面的this是上一级的作用域
let obj = {
    fn: function() {
        let f=()=> { 
            console.log(this); // 这里的this得是上一级
        }
        f();
    },
}
obj.fn();

// 2. 箭头函数没有arguments
let f1 = () => {
    console.log(arguments);
}
// f1(123);
// 我非要拿到实参集合
let f2 = (...arg) => {
    console.log(arg);
}
f2(1,2,3);

// 3. 箭头函数不可以用作构造函数 因为不可以使用new 执行
function FF () {
}
console.log(new FF);

let F=()=>{};
console.log(new F); // F is not a constructor
```

* async 异步函数
```
console.log("这是同步代码开始1");

// 2
async function fn() {
    return "执行fn()方法成功2";
}
console.log("这是定义了一个一步方法fn()3");

fn().then(function(res){
    console.log(res);
}).catch(function(e) {
    console.log(e);
});
console.log("我已经调用了方法fn()，并且给他“绑定”了then()执行成功回调和catch()执行错误回调4");

/**
 * 执行顺序：1 3 4 2
 * 原因：我们定义的是一个非阻塞的异步方法 anync function () {}
 * 好处：异步执行：程序跑到2的时候，开个任务，继续往下执行，最后跑回去执行2。（类似node.js）
 * 调用方法： 1调用函数fn().2调用then(res) {//这里的res就是fn()执行完成后return回来的结果}.3调用catch(e) {//这里的e就是错误信息}
 */
```  

# ES 的 “模块化”
* 定义模块 和 暴露模块 **e.js**
```
// 定义
var firstName = 'Michael';
var lastName = 'Jackson';
var year = 1958;

// 暴露
export {firstName, lastName, year};
```
* 引用模块 和 使用它内部的元素（变量、函数等） **i.js**
```
// 引用
// 全部引用
// import * as mj from './e';
// 调用
// console.log(mj.firstName);
// 解构引用
import {firstName, lastName} from './e';
console.log(firstName);
```

* 在html文档中调用模块化的js文件时
```
<!-- 写上type="module" -->
<script type="module" src="./modules/i.js"></script>
```

# TS
* 大白话：改进了JS。
* ES6语法写，可以编译为支持ES5及以下的版本。

# 开发环境搭建
* npm安装typescript
```
# 先装cnpm
npm install -g cnpm --registry=https://registry.npm.taobao.org
# 再用cnpm 装 typescript
cnpm install -g typescript
```
* 编译 tsc 命令编译 .ts 文件
```
tsc name.ts
```
会生成一个编译后可以放在浏览器里执行的.js文件

# 多行字符串
* 这个好像是ES6提供的吧【``】
```
# ES5-
var htmlText = "aaa" +
    "bbb" + 
    "ccc"; //无法换行 换行需要用+连接

# ES6 / TS
var htmlText = `aaa
bbb
ccc
`;
```

# 模板字符串
```
var htmlText = `
<div>
 <span> ${变量|方法()} </span>
</div>
`
```

# 自动拆分字符串
```
// 定义一个方法
function test(template, name, age) {
    console.log(template);
    console.log(name);
    console.log(age);
}

// 定义参数
var myName = 'liuhaoyu';
var getAge = function() {
    return 500;
}

// 调用方法 test`整个对应参数template,  ${myName}对应参数name, ${myAge}对应参数age`
test`hellow my name is ${myName}, I'm ${ageAge()}`
```

# 函数默认参数
```
/**
 * 默认参数值
 */
// 调用test()时，我如果只写两个参数，那么c默认值'liuhaoyu' 
// 有默认值的参数最好写在最后面
function test(a:string, b:string, c:string='liuhaoyu') {
    console.log(a);
    console.log(b);
    console.log(c);
}

test('a', 'b');
```

# **函数可选参数**
```
/**
 * 可选参数
 */
// 声明可选参数 b?
// 可选参数必须写在必选参数后面
// 顺序： (必选参数， 可选参数？， 带默认值的参数=默认值)
function test(a:string, b?:string, c:string='liuhaoyu') {
    // 如果你声明了可选参数，在有必要的情况下，请在函数内部处理当可选参数没填时候的情况
}

test('hahaha');
```

# 解构赋值(ES6)
```
function getStock() {
    return {
        code: 'ibm',
        price: 100,
    }
}
// 跟ES6解构赋值一样
var {code, price} = getStock();
```

# 箭头函数(ES6)
```
// 箭头函数: (参数列表)=>{函数体}
// var fn = () => {};

// 箭头函数好处
// ES5-
function getStock(name) {
    this.name = name;
    setInterval(function() {
        console.log('name is', this.name);
    }, 1000);
}
var stock = new getStock("IBM"); // 无法读得到this.name

// ES6 TS:
function getStock2(name:string) {
    this.name = name;
    // 不仅代码更少，而且this关键字可用
    setInterval(()=>{
        console.log('name is', this.name);
    }, 1000);
}
var stock = new getStock2("IBM2"); //可以读取this.name = ibm2
```

# 扩展操作符... (ES6)
```
// 无限参数列表的函数
function fn1(...args) {
    // 传进去的其实是一个数组
    // 原理是编译后js遍历Function.arguments属性
    args.forEach(function(arg) {
        console.log(arg);
    })
}

function fn2(a, b, c) {
    console.log(a);
    console.log(b);
    console.log(c);
}
// 传实参的时候写...args
var args = [1, 2, 3, 4, 5];
// fn2(...args); // 编辑器会提示语法错误，但是可以编译
```

# **generator函数**
```
// function* 函数名() {} 进行定义
function* doSomething() {
    console.log("start");

    yield; // 可以理解为调试的时候打断点

    console.log("finish");
}

// 这样的函数必须要用变量存储起来
var fn1 = doSomething();
// 使用 .next() 函数执行下一步操作（只会跑到yield就停下）再次调用才会接着跑
fn1.next();
```

# generator实际应用
```
/**generator函数实际应用
 * 模拟买股票
 */
// 定义函数
function* getStockPrice(stock) {
    // 无限循环
    while(true) {
        // .next()被调用时会一直生成 0~100之间的随机数
        yield Math.random()*100; // 假装在获取当前股票价格
    }
}

// 获取函数
var priceGenerator = getStockPrice("IBM");
// 定义最低价
var limitPrice = 15;
// 定义当前价
var price = 100;

// 当当前价 > 最低价的时候
while(price > limitPrice) {
    // 调用函数获取当前股票值并将值赋给price。再次进入循环
    price = priceGenerator.next().value;
    // 获取一定时间内的大于15每股的股票价格
    console.log(`the gererator return ${price}`);
}
```

# 各种“for”循环
```
/**
 * for of循环
 */
var arr = [1,2,3,4];

// forEach(闭包(){})
arr.forEach((element)=> {
    console.log(element);
});

// for(index对应索引 in 数组)
for(var index in arr) {
    console.log(index); //读索引
    console.log(arr[index]); //读值
}

// for(value of arr)
for(var value in arr) {
    // if(value > 1) break; //for of 可以在循环中break终止循环
    console.log(value); //读值
}
```

# 类
```
// 类定义
class Person {
    name; //属性
    eat(){
        console.log('我吃');
    } //方法
}

// 实例化
var p1 = new Person();
p1.name = 'liuhaoyu';
console.log(p1.name);
```

# 类 访问权限控制
```
/**
 * 访问权限控制符
 */
class Person {
    public name;//默认公开          对象√ 子类√ 自身√

    protected address;//保护的  对象× 子类√ 自身√

    private age;//隐私的        对象× 子类× 自身√
}s
```

# 类 构造函数 (默认private)
```
class Person {
    // name;
    // age;
    // 构造函数
    // constructor(name:string, age:number) {
    //     this.name = name;
    //     this.age = age;
    //     console.log("实例化成功");
    // }

    // 可以简写
    constructor(public name:string, public age:number) {
        // 参数列表里定义带访问控制符的属性，直接可以在实例化时创建属性
    }
}

// 实例化
var p1 = new Person('liuhaoyu', 500);
```

# 继承
```
// 爹
class Father {
    public lastName;
    protected money;
    private firstName;
}

// 儿子
class Son extends Father {
    // 儿子 可以继承姓public 可以继承钱money 继承不了名name
}

// 真实情况
var son = new Son; // 而真是的情况是，实例化的儿子是拿不出来钱的（都放爹那了）【对象不能继承protected 的 money】

```

# 继承中的super
```
class Father {
    name;
    money;
    constructor(name, money) {
        this.name = name;
        this.money = money;
    }
    eat() {
        console.log('我能吃');
    }
}

// super()的作用
class Son extends Father {
    age;
    // 1、在子类的构造函数中充当父类的构造函数，实例化后使子类的this关键字生效
    constructor(name, money, age) {
        // this.age = age; //错误必须先实例化父类
        super(name, money); //先有爹后有儿子：先实例化爹：这里super()相当于调父类的构造函数
        this.age = age; //super()的作用：让this关键字生效
    }

    // 2、调用父类的其他方法
    work() {
        super.eat(); //这里super充当了父类
    }
}
```

# **接口：约束类定义**
```
/**
 * 接口的作用：代码约定，是的其他开发者开调用某个方法或者创建新的类的时候必须遵循接口所定义的规定
 * “插线板三角插头只能查三角的，不带地线的只能插进两脚插孔”
 */
// 声明接口 属性约束
interface Life {
    age: number; //这个属性一旦声明，再实现的时候，是必须的
}
// 采用接口定义的属性约束类属性
class Person {
    constructor(public config:Life) {

    }
}
// var p1 = new Person('500'); //错误
// var p1 = new Person(500); //错误
var p2 = new Person({
    age: 500,           
}); //参数得这样写： ({接口要求必须的属性:赋值})

// 声明接口 必须实现的方法
interface Animal {
    eat();
}

// 实现 implements
class Sheep implements Animal {
    // 必须写一个eat()方法不然会报错
    eat() {
        console.log('我吃草');
    }
}
class Tiger implements Animal {
    eat() {
        console.log('我吃肉');
    }
}
```

# generic 泛型约束
```
/**
 * 泛型 generic
 */
class Person{
    name;
    age;
}

class Student extends Person {
    school;
}

// people:数据限制Array<泛型限制Person> ： 一个只能放Person对象的数组
var people: Array<Person> = [];
people[0] = new Person;//父类可以放进去
people[1] = new Student;//子类可以放进去
// people[2] = 1;  //这个就不行了
```

# 模块
* 定义和暴露
```
/**
 * 模块：
 * 帮助开发者将代码分割为可重用的单元。（提高代码的复用性，方便管理）
 * 开发者在模块定义中决定将模块中的哪些资源（类、方法、变量） 暴露出去供外部使用
 * 哪些资源只能在模块能使用（不暴露）
 */

// 定义模块
export var prop1;
var prop2; //不写export就不暴露模块内的资源

export function fn1() {

}
function fn2() {

}

export class C1 {

}
class C2 {

}

// 现在一次把暴露所有之前未暴露的资源
export{prop2, fn2, C2};
```
* 引入和使用
```
// 引入 {资源名称} from "模块相对路径";
import { prop1 } from "./module_export";
// 引入 *所有资源 叫 Modules from "模块相对路径"
import * as Modules from "./module_export";

console.log(prop1);
Modules.fn2();
```

# **模块注解**
```
// angularJS 中的模块注解
// import ( {Component} ) from '@angular/core';

// 这里就是注解
// @Component({
//     selecor: '选择器',
//     templateUrl: '模板地址.html',
//     StyleUrls: ['样式地址1.css', '样式地址2.css']
// })

// 这样定义一个组件
// export class Component {
//     // 当实例化这个组件的时候，会通过上面的注解，去找对应的模板地址和对应的样式地址
//     titile = 'app Works!';
// }
```

# **类型定义文件 .d.ts**
```
/**
 * 只能白话解释：
 * 比如我想在angular JS 里面用 jquery 
 * 直接写$符 ts是不懂得
 * 所以需要下载一个 jquery.d.ts （所有类型定义文件都以.d.ts结尾）
 * 下载地址是 gitHub上得 DefinetelyTyped（里面托管了几乎所有的.d.ts）
 * 也可以用 typings (npm install -g typings) **推荐**
 * typings seach 你要的类型定义文件
 * 在项目里安装 .d.ts文件 （typings install 文件名 --save） 通常框架里已经写好了package.json所以我们不用管--save写没写依赖关系进去
 */
```
