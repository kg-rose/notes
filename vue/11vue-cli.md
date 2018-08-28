# 父组件给子组件传值
* 父组件在模板上调用子组件时，在标签上给子组件绑定属性。 `<GrandSonComponent :msg="msg"></GrandSonComponent>`
* 子组件在脚本里用 **props** 接收： 
```
export default {
    props: {
        // 接收数据的名称: 数据类型,
        msg: String,
    }
}
```
> 此时子组件即可调用 msg 的值了。

> 还可以传递方法，注意这样接收 `run: Function,` ，甚至父组件自身传给子组件: `<子组件标签 :parent="this"></子组件标签>`（这个用 Object 类型接收）。

# 父组件主动获取子组件的数据和方法
* 父组件在模板中引用子组件时，给子组件一个 **ref** `<GrandSonComponent ref="GrandSon"></GrandSonComponent>`
* 通过 `this.$refs.ref标识.属性` 或者 ``this.$refs.ref标识.方法()` 直接调用子组件的属性和方法。

# 子组件主动获取父组件的数据和方法
* 直接使用 `this.$parent.父组件的属性或方法` 调用。

# 非父子组件（同级组件）传值
1. 可以利用父组件传值 => 子组件1 和 子组件2 通过他们共同的父组件交换数据。
2. **利用空的Vue实力广播** : 
    * 首先，需要新建一个空的Vue势力，我们创建在 /src/modules/ 下，取名 exchangeData.js
    ```
    /**
    * 监听同级组件数据交换
    */
    import Vue from 'vue'

    var exchangeData = new Vue();

    export default exchangeData;
    ```
    * 之后，我们定义发起数据的子组件： **Vue实例.$emit**
    ```
    # 模板
    <button @click="exchangeDataToDaughter()">点击传值给女儿组件</button>

    # 脚本
    // 引用 exchangeData
    import exchangeData from "../modules/exchangeData.js"
    ...
    methods: {
        exchangeDataToDaughter() {
            // 广播数据 $emit('数据名', 值);
            exchangeData.$emit('toDaughter', this.msg);
        }
    }
    ```
    * 最后，在接收数据的女儿组件中： **Vue实例.$on**
    ```
    # 我们直接用挂载完成的生命周期函数接收
    mounted() {
        // 接收数据 $on('数据名称', 回调函数(data为接收的数据) => { //... });
        exchangeData.$on('toDaughter', (data) => {
            alert(data);
        });
    }
    ```

> 总结： 1,建立一个空的Vue实例。 2，发送数组和接收数据的组件都需要引用该空实例。 3，发起使用 `Vue实例.$emit('key', value)` 4，接收数据使用 `Vue实例.$on('key', (data) => { //data就是接收到的数据 })`
