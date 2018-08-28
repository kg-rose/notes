# 绑定属性
* `v-bind:属性="data中定义的数据"`
* `v-bind:属性="data"` 可以简写为 `:属性="data"`

# 宿主元素上绑定 纯文本 / html 
* 假设在 data() 中定义这么一个数据
```
<script>
...
    data() {
        return {
            // html代码
            h2: "<h2>我其实是一个h2元素</h2>",
        }
    }
...
</script>
```
* 然后在 `<template>` 中显示：
    * `<宿主元素 v-text="h2"></宿主元素>` => 解析为纯文本。
    * `<宿主元素 v-html="h2"></宿主元素>` => 解析为 html 代码。

# 绑定style
* 模板template
```
<!-- 绑定style -->
<p :style="myStyle">我是一个普通的p标签</p>
<p :style="{color: myColor, fontSize: myFontSize + 'px'}">我是一个普通的p标签</p>
```

* 脚本script
```
data() {
    return {
        // 定义1个样式
        myStyle: {
            color: 'red',
            fontsize: '15px',
        },

        // 定义2个单独的css属性
        myColor: 'blue',
        myFontSize: 30,
    }
}
```

> 只需要注意，在模板中，宿主元素上，绑定一些css属性中间有 “-” 连接起来的属性，使用驼峰命名风格即可，比如 `font-size` => `fontSize`。

# v-model 实现双向数据绑定
> Vue 是一个 MVVM 框架： Model 改变 View , View 也可以改变 Model 。

* 代码
```
# template
<!-- v-model数据绑定 -->
<input type="text" v-model="myText">
{{ myText }}

# script
data() {
    return {
        // 定义一个普通的字符串
        myText: "Hello World!",
    }
}
```

* 在页面上会显示一个输入框内容即为 "Hello World!"， 如果改变输入框的内容，那么其实 data 中的 myText 也会改变

# 使用 ref 标识元素并定义 methods 操作元素样式
* 代码
```
# template
<!-- ref标识 dom 节点并对齐进行相应操作-->
<div :style="myBox" ref="myBox" @click="changeColor()"></div>

# script
...
data() {
    return {
        myBox: {
        width: '100px',
        height: '100px',
        background: 'blue',
        margin: '10px auto',
      }
    }
},
methods: {
    // 改变 div 的背景颜色
    changeColor() {
        // 一旦元素被标注上 ref 属性，则可以使用 this.$refs.标注的 ref 名称，来获取该 DOM 节点。
        var myDivDom = this.$refs.myBox;
        console.log(myDivDom);
        // 通过 节点.style.css属性 来获取该元素的某个 css属性。
        if (myDivDom.style.background ** 'red') {
        myDivDom.style.background = 'blue';
        } else {
        myDivDom.style.background = 'red';
        }
    }
}
```

* 在 template 上可以使用 `<元素 ref="标识元素给它取个名字，相当于id属性">` 来标示一个元素。
* 在 script 上则可以使用 `this.$refs.标识的名字` 来获取元素，类似于 `document.getElementBy“Ref”('标识的ref')` (当然没有这种写法)
* 同时调用被获取元素的 `元素.style.css属性` 可以更改他们的css样式。
* 最后所有的函数得必须写在 script 中的 `methods` 属性里： `methods: { 方法名() { //调用函数后具体执行的内容 } }`
* 在元素上可以绑定某些事件调用 methods 中定义好的函数，比如 `<元素 @click="changeColor()">` 就是 “点击事件，触发 changeColor() 函数”。


# 绑定class 实现颜色变化
* `<template>`
```
<!-- 实现颜色变化 -->
<p v-bind:class="{'active': mySwitch}">我就是一段普通的文字</p> <!-- 通过 v-bind:class="{'style中定义的类名': 布尔值为真则为宿主元素添加该类}" -->
<input type="checkbox" v-model="mySwitch"> 当前是否打开？：{{mySwitch}}
```
* `<script>`
```
data() {
    return {
        // 定义一个开关，默认关闭
        mySwtich: false,
    }
}
```
* `<style>`
```
.active {
  color: red;
}
```

> 在宿主元素上使用 `:class="{'style中定义的css类': 布尔值}"` 来绑定css类，当后面的布尔值为真时，则添加该类到宿主元素的class属性上。

> 在data中定义一个“开关”，默认为布尔值假 false 。

> 在模板上定义一个复选框当开关，使用 `v-model="开关变量名称"` 来绑定开关，当勾选时，自动修改“开关”的值为真。
