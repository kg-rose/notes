# 这次尝试了在微信小程序中集成 echarts
* [demo](https://github.com/ecomfe/echarts-for-weixin)
* 使用方法：
    * 把 demo 中的 ec-canvas/ 拷进项目中
    * 在需要用到画图的页面，在其配置文件 (pages/example/example.json) 中声明要使用该组件
    ```
    {
        "usingComponents": {
            "ec-canvas": "../../ec-canvas/ec-canvas"
        }
    }
    ```
    * 然后需要在 .js 文件中引用它，获取数据，在外面写实例化方法，在data中直接赋值
    ```
    import * as echarts from '../../ec-canvas/echarts'; //引用包

    function getBarOption() { //这里是对绘图对象的 option 的配置
        return {
            title: {
                text: '本月收支对比',
                textStyle: {
                    fontWeight: 'bold',
                }
            },
            xAxis: {
                type: 'category',
                data: ['收入', '支出'],
                color: '#000',
            },
            yAxis: {
                show: false
            },
            series: [{

                data: [100, 200], // 对简单的柱状图来说我们只用算这里

                type: 'bar',
                barWidth: 30,
                //配置样式
                itemStyle: {
                    normal: {
                        color: function(params) {
                            var colorList = ['#fed955', '#888'];
                            return colorList[params.dataIndex];
                        }
                    },
                },
                // 显示数据
                label: {
                    normal: {
                        show: true,
                        position: 'top',
                        textStyle: {
                            color: 'black'
                        }
                    }
                }
            }]
        };
    }

    Page({
        data: {
            ecBar: { //这是绘图对象
                onInit: function(canvas, width, height) { //这里调一个匿名函数实例化它
                    const barChart = echarts.init(canvas, null, {
                        width: width,
                        height: height
                    });
                    canvas.setChart(barChart);
                    barChart.setOption(getBarOption()); //这里调用外面的方法把 option 算出来

                    return barChart;
                }
            },
        }
    ```

    * 最后在 .wxml 页面上显示
    ```
    <view class="container">
        <ec-canvas id="mychart-dom-multi-bar" canvas-id="mychart-multi-bar" ec="{{ ecBar }}"></ec-canvas>
    </view>

    # 需要注意： view 必须给定位， ec-canvas 必须给宽高和定位
    ```

> 缺陷：因为算 option 的方法是写在外面的，而且不能在 page() 里面再给 ecBar 这个绘图对象用 `this.setData` 再赋一次值，所以整个绘图功能实际上只有载入的那一次生效，而想要再次生效需要重启整个小程序（我暂时还没有找到解决方法）