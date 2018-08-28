# UI框架介绍
> 使用 “饿了么” 公司对外提供的开源的两款 UI 框架：
* [Element UI](https://element.faas.ele.me/#/zh-CN) => 针对 PC 端
* [Mint UI](http://mint-ui.github.io/#!/zh-cn) => 针对移动端

# mint UI 使用
* 安装：通过 npm 命令 `npm install mint-ui --save`
* 投入使用：在 main.js 中引用
```
// 引用 MintUI 核心库 和 样式表
import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'

...

// 引用后需要声明使用
Vue.use(MintUI) //声明使用全部类
```
* 举例: action Sheet
```
# template
<div>
    <h3> action Sheet</h3>

    <!-- 这里是开关 -->
    <div class="page-actionsheet-wrapper">
        <mt-button @click.native="sheetVisible = true" size="large">点击上拉 action sheet</mt-button>
        <mt-button @click.native="sheetVisible2 = true" size="large">不带取消按钮的 action sheet</mt-button>
    </div>

    <!-- 这里是弹出层 -->
    <mt-actionsheet :actions="actions" v-model="sheetVisible"></mt-actionsheet>
    <mt-actionsheet :actions="actions2" v-model="sheetVisible2" cancel-text=""></mt-actionsheet>
</div>

# script
data() {
    return {
        // 开关
        sheetVisible: false,
        sheetVisible2: false,

        // 方法集合，这里为空，我们在 mounted() 中绑定方法
        actions: [],
        actions2: []
    }
},

// 配置可以调用的方法
methods: {
    takePhoto() {
        console.log('拍照');
    },

    openAlbum() {
        console.log('从相册中选择');
    },

    goBack() {
        history.go(-1);
    }
},

// 在 “挂载完成后即触发” 的生命周期函数上绑定方法
mounted() {
    // 方法集合 = [{ name: "按钮内容", method:"点击调用的方法来自 methods "  }, { //... }]
    this.actions = [{
        name: '拍照',
        method: this.takePhoto
    }, {
        name: '从相册中选择',
        method: this.openAlbum
    }];
    this.actions2 = [{
        name: '确定'
    }, {
        name: '返回上一步',
        method: this.goBack
    }];
}
```
* 在 template 按钮开关部分 `<mt-button @click.native="sheetVisible = true" size="large">点击上拉 action sheet</mt-button>` 使用 `@click.native=" 将 data 中的 sheetVisible 设置为 true "`，同时在弹出层部分，`<mt-actionsheet :actions="actions" v-model="sheetVisible"></mt-actionsheet>` 通过 `v-model="一个布尔值取值来源于 sheetVisibel"` 来实现弹出层的 弹出和隐藏，同时通过 `:actions="actions"` 来绑定弹出层可以调用的方法

* 弹出层可以调用的方法即可 data 中的 actions 属性是来自于
```
# 1、在 data 中定义一个空数组
data() {
    ...

    actions = [],
}

# 2、在 methods 中配置一些方法
methods: {
    somefunction() {
        // ...执行内容
    }
}

# 3、在 mounted() 声明周期函数中挂载这些方法到空数组 actions 中（给 actions 赋值）
// 可用动作集合 = [{ //动作1... }, { //动作22... }]
this.actions = [
    // { name: '按钮标题', methods: 绑定一个 methods 中定义的函数 }
    {
        name: '拍照',
        method: this.takePhoto
    },
    // 动作2s
    {
        name: '从相册中选择',
        method: this.openAlbum
    }
];
```

> 在 Mint UI 的标签上调用点击事件需要 `@click.native="触发的函数() 或者 要执行的代码"` 

> 所有 Demo 都可以在 [GitHub](https://github.com/ElemeFE/mint-ui) 上的 example/ 里找到