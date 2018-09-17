// Page module
define(["app", "controllers/base/page"],

    function (app, BasePage, VideoPlayer) {

        var Page = {};

        Page.View = BasePage.View.extend({

            page : 0,
            maxPage : 1,
            newsType : '',
            isLoading : false,
            loopNewsId : [],


            isMobile : false,



            beforeRender: function () {

                var done = this.async();
                done();

                console.log("beforeRender: " + this.$el.attr("module"));

            },


            afterRender: function () {

                console.log("afterRender: " + this.$el.attr("module"));

            },

            afterAllRender: function () {

                console.log("afterAllRender: " + this.$el.attr("module"));
                var url = window.location.href;
                $.ajax({
                    url: '/wechat/auth/wx-share',
                    type: "POST",
                    dataType: "json",
                    data: {
                        url: url
                    },
                    success: function(response) {
                        console.log(response);
                        wxshare(response);
                        console.log("cherry");
                    }
                });

                function wxshare(response) {
                    wx.config({
                        debug: false,
                        appId: response.appId,
                        timestamp:response.timestamp,
                        nonceStr: response.nonceStr,
                        signature: response.signature,
                        jsApiList: [
                            'addCard'
                        ]
                    });

                    wx.ready(function(){
                        //添加卡券
                        document.querySelector('#add-card').onclick = function () {

                            var cardId = $(this).data('coupon');
                            console.log(cardId);

                            $.ajax({
                                url: '/wechat/auth/wx-card',
                                type: "POST",
                                dataType: "json",
                                data: {
                                    cardId: cardId
                                },
                                success: function(data) {

                                    wx.addCard({
                                        cardList: [
                                            {
                                                cardId: cardId,
                                                cardExt: '{"code": "' + data["code"] + '", "nonce_str":"' + data["nonceStr"] + '","timestamp": "' + data["timestamp"] + '", "signature":"' + data["signature"] + '"}'
                                            }
                                        ],
                                        success: function (res) {
                                            //更改卡券状态
                                            /*$.post("/wechat-api/update-coupon-status",{cardId: cardId},function(data) {
                                                location.reload();
                                            });*/

                                        }
                                    });
                                }
                            });
                        };

                    });


                }

            },

            resize: function (ww, wh) {


            },

        });

        // Return the module for AMD compliance.
        return Page;

    });
