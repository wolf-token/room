<?php

	global $_GPC, $_W;

	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$flase=false;
	//判断数据传输类型
	$option = !empty($_GPC['op']) ? $_GPC['op'] : "agreement";
	if($option == "agreement"){

		//查取信息
		$agreement = pdo_fetch("SELECT * FROM ".tablename("leju_agreement")." WHERE id=:id",array("id"=>1));
		include $this->template('agreement');

	}elseif($option == "mobile"){

		//手机号
		if(!empty($_GPC['mobile']) && $_W['ispost']){

			//接收数据
			$result = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE mobile=:mobile",array("mobile"=>$_GPC['mobile']));

			//判断是否存在
			if(empty($result)){
				$flase=true;
				//不存在
				echo "yes";

			}else{

				//存在
				echo "no";
			}

		}

	}elseif($option == "card"){

		//身份证号
		if(!empty($_GPC['card']) && $_W['ispost']){

			//接收数据
			$result = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE Idcard=:card",array("card"=>$_GPC['card']));

			//判断是否存在
			if(empty($result)){

				//不存在
				echo "yes";

			}else{

				//存在
				echo "no";
			}

		}

	}else if($option=='yzm'){
		session_start();//开启session
		$tell=intval($_GPC['tell']);
		if($tell){

			$num=rand(1000,9999);
			$_SESSION['num']=$num;
 				//创蓝接口参数
        			$postArr = array (
                          'un' => '',//创蓝账号 替换成你自己的账号
                          'pw' => '',//创蓝密码 替换成你自己的密码
                          'msg' => "您好，您的验证码是：".$num."，验证码在3分钟内有效，如不是本人操作，请忽略。",
                          'phone' => $tell,
                          'rd' => 1
                     );


			        $postFields = http_build_query($postArr);
			        $ch = curl_init ();
			        curl_setopt ( $ch, CURLOPT_POST, 1 );
			        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
			        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			        curl_setopt ( $ch, CURLOPT_URL, 'http://sms.253.com/msg/send');
			        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
			        $result = curl_exec ( $ch );
			        curl_close ( $ch );
			        // echo json_encode($result);die;
			       $res=explode(',', $result);
			       echo json_encode(substr($res[1], 0,1));
		}





	}else if($option == "postRegist"){


		//验证是否为post
		if($_W['ispost']){


			if($_SESSION['num']!=$_GPC['cell']){


				message("对不起验证码错误，请重新获取",$this->createMobileUrl("index"));
			}

			//判断是否存在用户的uid
			if(!empty($_GPC['uid'])){

			//判断是否存在用户
			$lol = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));

				//判断是否存在用户信息
				if(empty($lol)){


			$recommend_id = pdo_fetch("SELECT * FROM ".tablename("qrcode_stat")." WHERE openid=:openid ORDER BY id DESC LIMIT 1",array("openid"=>$openid));
			$scene_str = explode(":",$recommend_id['scene_str']);
			$recommend = $scene_str[3];
			// var_dump($_GPC);die;
	    	//设置注册人的佣金
	    	$money['uniacid'] = $_W['uniacid'];
	    	$money['uid'] =$uid;
	    	$money['addtime'] = date('Y-m-d H:i:s',time());
	    	$result2 = pdo_insert("leju_commission",$money);
	    	$mid = pdo_insertid();//可以
	    	//设置等级的数据
	    	$rank['uniacid'] = $_W['uniacid'];
	    	$rank['uid'] = $uid;
	    	$rank['type'] = 3;
	    	$rank['money'] = 0;
	    	$result3 = pdo_insert("leju_rank",$rank);
	    	$rankid = pdo_insertid();//可以
	    	//设置提现表的的信息
	    	$carray['uniacid'] = $_W['uniacid'];
	    	$carray['uid'] =$uid;
	    	$carray['money'] = 0;
	    	$carray['status'] = 0;
	    	$carray['commission_id'] = $mid;
	    	$result5 = pdo_insert("leju_carray",$carray);
	    	$carrayid = pdo_insertid();
	    	//设置用户表信息
	    	$data['openid'] = $openid;
	    	$data['recommend_id'] = $recommend;
	    	$data['grade_id'] = $rankid;
	    	$data['withdraw_id'] = $carrayid;
	    	$data['commission'] = $mid;
	    	$data['nickname'] = $_W['fans']['tag']['nickname'];
	    	$data['avatar'] = $avatar;
	    	$data['createtime'] = date('Y-m-d H:i:s',time());
	    	$data['type'] = 1;
	    	$data['uniacid'] = $_W['uniacid'];
	    	$data['uid'] = $uid;
	    	$data['realname'] = $_GPC['name'];
			// $data['Idcard'] = $_GPC['card'];
			$data['mobile'] = $_GPC['mobile'];
	    	//加入到数据库
	    	$result1 = pdo_insert("leju_users",$data);


	/*******************开始判断推荐人的是否达到升级条件***************************/

	    	//查取经纪人信息
			$economy_details = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE id=:id",array('id'=>$recommend));
			$economy_details['rank'] = pdo_fetch("SELECT * FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$economy_details['grade_id']));
			$economy_details['commission'] = pdo_fetch("SELECT * FROM ".tablename('leju_commission')." WHERE id=:id",array('id'=>$economy_details['commission']));
			$users =  pdo_fetchall("SELECT * FROM ".tablename('leju_users')." WHERE recommend_id=:id",array("id"=>$economy_details['id']));
			$economy_details['room'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('leju_room')." WHERE recommend_id=:id",array("id"=>$economy_details['id']));
			$economy_details['client'] = pdo_fetchall("SELECT COUNT(*) FROM ".tablename('leju_client')." WHERE recommon_id=:id",array("id"=>$economy_details['id']));
			$economy_details['mam'] = count($economy_details['client']);

			//本人推荐的客户购房数量
			foreach ($economy_details['client'] as $kl => $var) {

				if($var['buy_status'] == 1){

					$economy_details['buy'] += 1;

				}else{
					$economy_details['buy'] = 0;
				}
				if($var['buy_status'] == 0){

					$economy_details['sale'] += 1;

				}else{
					$economy_details['sale'] = 0;
				}

				$economy_details['shu'] += $var['math'];
			}
		/**
		*	等级升级条件判断区域 开始
		*
		*/

			$economy_details['month'] = count($users);
			$economy_details['month1'] = count($users)+1;
			//查找经纪人推荐人的等级数量
			foreach ($users as $key => $value) {
				// var_dump($value);
				$users[$key]['rank'] = pdo_fetch("SELECT type FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$value['grade_id']));
				//团队除本人之外推荐的客户数量
				$rooma = pdo_fetchall("SELECT * FROM".tablename("leju_client")."WHERE recommon_id=:id ",array("id"=>$value['id']));
				$economy_details['mama'] += count($rooma);
				//团队除本人之外推荐的房源数量
				$roomal = pdo_fetchall("SELECT * FROM".tablename("leju_room")."WHERE recommend_id=:id ",array("id"=>$value['id']));
				$economy_details['man'] += count($roomal);
				//查找团队中的经纪人推荐的客户购买房的总数
				$room = pdo_fetchall("SELECT math FROM".tablename("leju_client")."WHERE recommon_id=:id AND buy_status=1",array("id"=>$value['id']));
				foreach ($room as $k => $val) {

					$account2 += intval($val['math']);

				}

				$type1 += substr_count(($users[$key]['rank']['type']), 1);
				$type2 += substr_count(($users[$key]['rank']['type']), 2);
				$type3 += substr_count(($users[$key]['rank']['type']), 3);

				//判断本人为三级下属团队中是否有人升级为二级
				if($economy_details['rank']['type'] == 3 && $users[$key]['rank']['type']==2){

					//修改其下属的二级人员离开队伍
					$endoverr = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value['id']));
				}
				//判断二级下面是否有人升级为一级
				if($economy_details['rank']['type'] == 2 && $users[$key]['rank']['type']==1){

					//修改其下属的二级人员离开队伍
					$endoverr = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value['id']));
				}
				//判断下属团队中是否有人升为二级
				if($economy_details['rank']['type'] == 2 && $users[$key]['rank']['type']==2){

					//修改其下属的二级人员离开队伍
					$endover = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value['id']));
				}
				//判断下属是否有人升级到一级
				if($economy_details['rank']['type'] == 1 && $users[$key]['rank']['type']==1){

					//修改其下属永远离开队伍独立
					$endovered = pdo_update("leju_users",array("recommend_id"=>0,"record_recommon"=>0),array("id"=>$value['id']));
				}

			}

			$economy_details['mama'] = intval($economy_details['mama']) + intval($economy_details['mam']);
			$economy_details['man'] = intval($economy_details['man']) + intval($economy_details['room']);
			//判断经纪人推荐的人数是否达到标准的升级要求
			//判断经纪人是不是三等级
			if($economy_details['rank']['type'] == 3){

				//查取此经纪人推荐的房源数量
				$month = pdo_fetchall("SELECT * FROM ".tablename('leju_client')."WHERE recommon_id=:id",array("id"=>$economy_details['id']));
				// var_dump($month);
				foreach ($month as $key => $value) {

					$math += $value['math'];
				}
				//查取三级升二级的条件
				$criteria = pdo_fetch("SELECT * FROM ".tablename("leju_scale_two")." WHERE id=:id",array("id"=>1));
				//三级升二级
				if($type3 >= $criteria['person'] || $math >= $criteria['room']){

					//修改等级
					$amend = pdo_update("leju_rank",array("type"=>2),array("id"=>$economy_details['grade_id']));
				}
			}
			//判断是否为二级经纪人
			if($economy_details['rank']['type'] == 2 && $economy_details['accredit'] == 0){

				//二级经纪人升为一级经纪人
				//查找本人团队中的经纪人数量
				$level = $economy_details['month'] + 1;
				//查找本人推荐客户购买房源的数量
				$amount = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE recommon_id=:id AND buy_status=1",array("id"=>$economy_details['id']));
				//计算团队推荐客户购买房屋总合
				$amount1 = intval($amount['math']) + $account2;
				//查取二级升一级经纪人的条件
				$criteria1 = pdo_fetch("SELECT * FROM ".tablename("leju_scale_one")." WHERE id=:id",array("id"=>1));
				if($level >= $criteria1['math'] || $amount1 >= $criteria1['room']){

					//修改经纪人的等级
					$finishal = pdo_update("leju_rank",array("type"=>1),array("uid"=>$economy_details['uid']));

				}

			}

			//判断是否为一级 让其下属队伍回归
			if($economy_details['rank']['type'] == 1){

				//查找其以前的属下是否有二级
				$minion = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE record_recommon=:id",array("id"=>$economy_details['id']));
				foreach ($minion as $ky => $vo) {

					$minion1[$key]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$vo['grade_id']));
					//判断是否有二级
					if($minion1[$key]['rank']['type'] == 2){

						//修改他的推荐id
						$finishel = pdo_update("leju_users",array("recommend_id"=>$vo['record_recommon']),array("id"=>$vo['id']));
					}

				}
			}

		/**
		*		结束
		*/

/**********************判断推荐人是否达到升级标准结束************************/
					//判断
					if(!empty($result1)){

						//跳转到首页
						message("恭喜您注册成功",$this->createMobileUrl("home",array("op"=>'room'),"success"));

					}else{

						message("对不起您注册失败",$this->createMobileUrl("index","error"));
					}

				}else{

					message("对不起您已经注册",$this->createMobileUrl("index","error"));
				}
			}

		}

	}














?>
