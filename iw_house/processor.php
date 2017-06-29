<?php
/**
 * 乐居房产模块处理程序
 *
 * @author 哎喔科技(北京)有限公司
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Iw_houseModuleProcessor extends WeModuleProcessor {
	// public function respond() {
	// 	$content = $this->message;
	// 	//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		
	// 	return $this->respText(json_encode($content));
	// }
	
	 public function respond() {
	 	global $_W;
	 	return $this->respText('欢迎教您：'.json_encode($this->message));
	    /*$account_api = WeAccount::create();
	    $info = $account_api->fansQueryInfo($this->message['from']);
	    $nickname = $info["nickname"];
	    
	    //查取信息
	    $result = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE openid = :openid",array("openid"=>$info['openid']));
	    //判断数据库是否存在此用户
	    if($result){

	    	//判断是否有头像
	    	if($result['avatar'] == '' && $info['headimgurl'] !== ''){

	    		//修改头像
	    		$data['avatar'] = $info['headimgurl'];
	    		pdo_update('leju_users',$data, array('openid' =>$info['openid']));
	    	}
	    	
	    }else{

	    	//设置注册人的佣金
	    	$money['uniacid'] = $_W['uniacid'];
	    	$money['uid'] = $_W['member']['uid'];
	    	$money['addtime'] = date('Y-m-d H:i:s',time());
	    	$result2 = pdo_insert("leju_commission",$money);
	    	$mid = pdo_insertid();//可以
	    	//设置等级的数据
	    	$rank['uniacid'] = $_W['uniacid'];
	    	$rank['uid'] = $_W['member']['uid'];
	    	$rank['type'] = 3;
	    	$rank['money'] = 0;
	    	$result3 = pdo_insert("leju_rank",$rank);
	    	$rankid = pdo_insertid();//可以
	    	//设置提现表的的信息
	    	$carray['uniacid'] = $_W['uniacid'];
	    	$carray['uid'] = $_W['member']['uid'];
	    	$carray['money'] = 0;
	    	$carray['status'] = 0;
	    	$carray['commission_id'] = $mid;
	    	$result5 = pdo_insert("leju_carray",$carray);
	    	$carrayid = pdo_insertid();
	    	//设置用户表信息
	    	$data['openid'] = $info['openid'];
	    	$data['grade_id'] = $rankid;
	    	$data['withdraw_id'] = $carrayid;
	    	$data['commission'] = $mid;
	    	$data['nickname'] = $info['nickname'];
	    	$data['avatar'] = $info['headimgurl'];
	    	$data['createtime'] = date('Y-m-d H:i:s',time());
	    	$data['type'] = 1;
	    	$data['uniacid'] = $_W['uniacid'];
	    	$data['uid'] = $_W['member']['uid'];

	    	//加入到数据库
	    	$result1 = pdo_insert("leju_users",$data);
	    	
	    }
	    return $this->respText('欢迎教您：'.$nickname);*/
    }
}
