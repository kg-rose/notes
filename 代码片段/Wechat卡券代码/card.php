<?php

class card{
	public function actionWxCard(){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$card = isset($_REQUEST['cardId']) ? $_REQUEST['cardId'] : '';
		$myWeChat = Fuc::getWeChatObj();
		$result = $myWeChat->getCardSignPackage($card);
		return $result;
	}

	public function getCardSignPackage($card) {
		$this->getJsApiTicketForCard();
		$timestamp = time();
		$card_api_ticket_obj = array();

		$card_api_ticket_obj["api_ticket"] = $this->jsapi_ticket_for_card;
		$card_api_ticket_obj["timestamp"] = $timestamp;
		$card_api_ticket_obj["nonceStr"] = $this->generateNonceStr();
		$card_api_ticket_obj["card_id"] = $card;
		$card_api_ticket_obj["code"] = "";

		$arr = array($card_api_ticket_obj["api_ticket"], $card_api_ticket_obj["code"], $card_api_ticket_obj['timestamp'], $card_api_ticket_obj['nonceStr'], $card_api_ticket_obj['card_id']);
		sort($arr, SORT_STRING);


		$card_api_ticket_obj["signature"] = sha1(implode($arr));

		return $card_api_ticket_obj;
	}

	public function getJsApiTicketForCard() {

		if (!$this->access_token && !$this->checkAuth())
			return false;
		if ($rs = $this->getCache(self::CACHE_JS_CARD_NAME)) {

			$this->jsapi_ticket_for_card = $rs;
			return $rs;
		}

		$result = $this->http_get(self::JSAPI_URL . 'access_token=' . $this->access_token . '&type=wx_card');
		if ($result) {
			$json = json_decode($result, true);
			if (!$json || (isset($json['errcode']) && $json['errcode'] != '0')) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];

				return false;
			}
			$this->jsapi_ticket_for_card = $json['ticket'];
			$this->access_token_expires = microtime(true) + ($json['expires_in'] ? intval($json['expires_in']) - 300 : 3600);
			//$this->access_token_refresh = $json['refresh_token'] ? $json['refresh_token'] : '';

			$expire = $json['expires_in'] ? intval($json['expires_in']) - 300 : 3600;
			$this->setCache(self::CACHE_JS_CARD_NAME, $this->jsapi_ticket_for_card, $expire);
			return $this->jsapi_ticket_for_card;
		}

		return false;
	}
}