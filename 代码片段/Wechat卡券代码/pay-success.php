<div class="page-pay-success" module="page-pay-success" module-data="">
	<?php if($verification == 'valid'){?>
    <div class="close">
        <a href="<?= $url.'orders'?>">
        <img src="/assets/images/icons/icon-nav-close.png">
        </a>
    </div>
	<div class="word-part">
		<span>ETRO感谢您的支持与喜爱！</span>
		<span>恭喜您获得一张价值500元的代金券，</span>
		<span>可在下次微信商城或线下门店购物时使用。</span>
	</div>
	<div class="img-part" id="add-card" data-coupon="<?=$coupon->coupon_id?>">
		<img src="/assets/images/crm_mobile/500soupon.png">
	</div>
    <?php }else{?>
    	<div class="close">
            <a href="<?= $url?>">
                <img src="/assets/images/icons/icon-nav-close.png">
            </a>
        </div>
		<div class="word-part">
			<span>该优惠券限本人领取</span>
            <span>ETRO感谢您的支持与喜爱！</span>
        </div>
		<div class="img-part">
	        <button class="btn">
	            <a href="<?= $url?>">进入ETRO微信官方商城</a>
	        </button>
    	</div>
    <?php }?>
</div>