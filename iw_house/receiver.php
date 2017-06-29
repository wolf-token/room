<?php
/**
 * 乐居房产模块订阅器
 *
 * @author 哎喔科技(北京)有限公司
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Iw_houseModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微擎文档来编写你的代码
		return $this->respText('欢迎教您：'.json_encode($this->message));
	}
}