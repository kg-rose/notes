# 41 css 动画
* 使用 [animate.css](https://daneden.github.io/animate.css/)
* 代码
```
<!-- 1、引用 animate.css -->
<link rel="stylesheet" href="animate.css">

 <div id="app">
    <button @click="flag=!flag">切换</button>
    <!-- 2、用一个 <transition> 标签把动画影响的元素包裹起来 -->
    <!-- 3、给 <transition> 属性 enter-active-class="指定元素显示时的动画" leave-active-class="元素隐藏时的动画" -->
    <transition enter-active-class="animated fadeIn" leave-active-class="animated fadeOut">
        <h1 v-if="flag">测试</h1>
    </transition>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            flag: true,
        },
    });
</script>
```
* 使用 `<transition>` 标签将要受到动画影响的元素包起来
* 在 `<transition>` 标签中指定属性 `enter-active-class="显示过程动画样式类"`...
* 一共有这么几个属性：
```
# enter="开始进入的样式"
# enter-active="进入过程中"
# enter-to="进完成后"
# leave-active="离开过程中"
# leave="完全离开后"
```

# 42 directive 自定义指令
* 代码
```
<div id="app">
    <!-- <span v-star>测试</span> -->
    <input type="text" v-model="title" v-bindandupdate.focus="title">
</div>

<script>
    // Vue.directive('star', {
    //     // 绑定
    //     bind(el, bind) {
    //         console.log(bind);
    //     }
    // })
    // Vue.directive('test', {
    //     // 更新
    //     update(el, bind) {
    //         console.log(bind);
    //     }
    // })
    Vue.directive('bindandupdate', function(el, bind) {
        console.log(bind);
    })
    var app = new Vue({
        el: '#app',
        data: {
            title: "ceshi",
        },
    });
</script>
```

* 使用 `Vue.directive('指令名称', { 动作() { //触发时执行的代码 } })`
* 在标签上使用 ` v-指令名称` 来将指令绑定到元素上
* `bind()` 动作是指元素被载入后就触发的动作
* `update()` 动作是指元素被更新时触发的操作
* 通常我们就使用 bind 和 update 两个动作，所有很多时候我们可以简写为 `Vue.directive('指令名称', function() { //同时绑定update 和 bind 动作 })`

# 43 在组件中使用指令
* 代码
```
<div id="app">
    <h1 v-test="color">字</h1>
    <input type="text" v-model="color" v-focus>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            color: 'red',
        },
        directives: {
            // 这个相当于 bind() + update() 动作
            test(el, bind) {
                var color = bind.value ? bind.value : red;
                el.style.cssText = "color:" + color;
            },
            // 这个相当于其他动作
            focus: {
                // 比如 inserted : 当子元素插入到父元素的时候调用
                inserted(el, bind) {
                    el.focus();
                }
            }
        }
    });
</script>
```

* 指令都写在 `directives` 属性中
* 同样，直接写`指令() {}` 是直接绑定 bind 和 update 两个动作
* 如果要用其他动作，则可以使用 `指令: { 动作() { //代码 } }`
