# vue-resource 请求数据
* 安装 `cnpm install vue-resource --save` => 安装 vue-resource 并且写入依赖关系到 package.json
* 在 /src/main.js 中引用该插件
```
// 引用 VueResource
import VueResource from 'vue-resource'
// 使用 
Vue.use(VueResource);
```
* 在组件中使用 vue-resource 来请求数据
```
# 模板
<button @click="getData()">请求数据</button>

# 脚本示例
this.$http.action('/请求api地址', [参数列表]).then((response) => {
    // 响应成功回调
}, 
(error) => {
    // 响应错误回调
});

# 具体脚本
// 这里牵扯到了跨域的问题，所以不使用 get 使用 jsonp 请求一个小白接口
this.$http.jsonp("http://api.okayapi.com/?app_key=2C1C0E2CB68FC1E770C8548EDC559FE9").then(
    (response) => {
        console.log(response); //控制台打印请求成功后收到的响应结果
        this.data = response.body.data;
    },
    (error) => {
        console.log(error);
        alert("请求数据失败");
    }
);
```

> 一共3步： 下载 => main.js中引用 => 组件 script 中使用 `this.$http.action('api地址', [参数列表]).then(成功回调 , 失败回调)`。


# [axios](https://github.com/axios/axios)
* 安装 `cnpm install axios --save`
* 在组件中使用
```
// 引入axios
import Axios from "axios"

// 使用axios
/*
Axios.get("地址")
// 成功回调
.then((response) => {
    console.log(response);
})
// 失败回调
.catch((error) => {
    console.log(error);
})
*/
```
