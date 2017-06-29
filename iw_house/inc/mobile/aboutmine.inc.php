<?php

	global $_W,$_GPC;
	$uid = $_W['fans']['uid'];
	$acid = $_W['fans']['uniacid'];
	//判断是否有信息
	$option = !empty($_GPC['op']) ? $_GPC['op'] : 'info';


	if($option == "info"){

		//查取规则说明
		$aboutmine['rule'] = pdo_fetch("SELECT * FROM ".tablename("leju_rule")." WHERE id=:id",array("id"=>1));
		$aboutmine['rule']['content'] = htmlspecialchars_decode($aboutmine['rule']['content']);
		$aboutmine['rank_one'] = pdo_fetch("SELECT * FROM ".tablename("leju_scale_one")." WHERE id=:id",array("id"=>1));
		$aboutmine['rank_one']['information'] = htmlspecialchars_decode($aboutmine['rank_one']['information']);
		$aboutmine['rank_two'] = pdo_fetch("SELECT * FROM ".tablename("leju_scale_two")." WHERE id=:id",array("id"=>1));
		$aboutmine['rank_two']['information'] = htmlspecialchars_decode($aboutmine['rank_two']['information']);
		$aboutmine['assign'] = pdo_fetch("SELECT * FROM ".tablename("leju_assign")." WHERE id=:id",array("id"=>1));
		$aboutmine['assign']['information'] = htmlspecialchars_decode($aboutmine['assign']['information']);
		//加载我的信息主页
		// var_dump($aboutmine);
		// die;
		include $this->template("aboutmine");

	}else if($option == "recommend"){


		//查找站点的信息
		$recommend = pdo_fetchall("SELECT * FROM ".tablename("leju_station"));

		//加载我要推荐的页面
		include $this->template("recommend",$uid);

	}else if($option == "home"){

		if($_W['ispost']){


			//查取推荐人的信息
			$monday = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$_GPC["uid"]));
			// $pan = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id AND name=:name AND phone=:phone AND (title!=3 OR title!=4)",array("name"=>$monday['realname'],"phone"=>$monday['mobile'],"id"=>$contact['station']));
			// var_dump($pan);
			// die;
			//接收数据
			//房主信息
			$house['name'] = $_GPC['name'];
			$house['cell'] = $_GPC['cell'];
			$house['station'] = $_GPC['position'];
			$house['recommend_id'] = $monday['id'];
			$house['time'] = time();
			$house['uniacid'] = $acid;
			// 此处还差推荐人的id或者是uid openid******
			$year = date("Y");
			$month = date("m");
			$day = date("d");
			$start = mktime(0,0,0,$month,$day,$year);//当天开始时间戳
			$end= mktime(23,59,59,$month,$day,$year);//当天结束时间戳
			//统计推荐的当天数量
			$ending1 =  pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("leju_person")." WHERE time<=:end AND time>=:start AND recommend_id=:id",array("start"=>$start,"end"=>$end,"id"=>$monday['id']));

			$all = pdo_fetch("SELECT * FROM ".tablename("leju_math")." WHERE id=1");
			//查取
			//判断推荐人的推荐信息

			$ar = intval($ending1);
			$la = intval($all['person']);

			//判断推荐的数量是否超标
			if($ar>=$la){

				message("您今天推荐的人数已经达到上限",$this->createMobileUrl("home",array("op"=>"room")),"error");
			}
			if(empty($ending)){
				$ending=0;
			}

			//判断是否超过上传的数据的数量
			if(($ending < $la) && !empty($_GPC['name']) && !empty($_GPC['cell'])){
				
				//判断是否已经推荐过联系人
				$chi = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE name=:name AND cell=:cell",array("name"=>$_GPC['name'],"cell"=>$_GPC['cell']));
				if(empty($chi)){
					
					//添加信息
					$result4 = pdo_insert("leju_person",$house);
					$mid = pdo_insertid();

				/*****开始分配房源联系人*******/

					//查取房源联系人信息
					$contact = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id AND broker_status=:status",array("id"=>$mid,"status"=>0));
					//查找客户对应的站点信息
					$station1 = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$_GPC['position']));
					//查找推荐人是否为员工
					$pan = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id AND name=:name AND phone=:phone AND (title!=3 OR title!=4)",array("name"=>$monday['realname'],"phone"=>$monday['mobile'],"id"=>$contact['station']));
					// var_dump($pan);
					if (!empty($pan)) {
						
						 $staff1 = $pan;

					}else{

						//查找站点的员工信息
						$staff1 = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id  AND title=3 OR title=4  ORDER BY month limit 1",array("id"=>$contact['station']));

					}
						
					
					//分配到此员工的身上
					$broke['broker_id'] = $staff1['id'];
					$broke['broker_status'] = 1;
					$broke['station'] = $station1['id'];
					$broke['broker_name'] = $staff1['name'];
					$broke['applation_time'] = date("Y-m-d H:i:s",time());
					//修改客户状态
					$enl = pdo_update("leju_person",$broke,array("id"=>$contact['id']));
					//修改员工的信息
					$matl = $staff1['month'] + 1;
					$en2 = pdo_update("leju_staff",array("month"=>$matl),array("id"=>$staff1['id']));

				/*******分配房源联系人结束**************/

					//判断是否添加成功
					if(!empty($result4) && !empty($enl)){

						//查找店员信息
						$store = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE realname=:name AND mobile=:mobile",array("name"=>$staff1['name'],"mobile"=>$staff1['phone']));
						//发送消息通知
						$jian = date("Y-m-d H:i:s",time());
						$contente = '分配提醒：你被分配新的出售房源客户，姓名：'.$contact['name'].' 电话：'.$contact['cell']."时间：".$jian;

						$send['touser'] = trim($store['openid']);
						$send['msgtype'] = 'text';
						$send['text'] = array('content' => urlencode($contente));
						$acc = WeAccount::create();
						$datsa = $acc->sendCustomNotice($send);
						// echo json_encode($datsa);
						message("恭喜您推荐成功",$this->createMobileUrl("home",array("op"=>"room")),"success");

					}else{

						message("对不起您推荐失败",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
					}
				
				}else{
					
					message("对不起您已经推荐过此客户",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
				}
				

			}

		}else{

					message("对不起您推荐失败",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
				}




	}else if($option == "tell"){

		$tell = $_GPC['tell'];
		//查取信息
		$result = pdo_fetchall("SELECT * FROM ".tablename("leju_person")." WHERE cell=:tell",array("tell"=>$tell));


		//判断是否存在
		if(empty($result)){

			echo "yes";

		}else{

			echo "no";
		}

	}else if($option == "phone"){

		$phone = $_GPC['phone'];
		//查取信息
		$result1 = pdo_fetchall("SELECT * FROM ".tablename("leju_client")." WHERE phone=:phone",array("phone"=>$phone));

		//判断是否存在
		if(empty($result1)){


			echo "yes";
		}else{

			echo "no";
		}

	}else if($option == "share"){

		// var_dump($_W['member']);
		// die;
		//查取数据
		$share = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));

		//判断此人是否有生成的二维码
		// if(empty($share['code'])){
			/***获取二维码*/
			$poster = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));
			$member = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));
			$account_api = WeAccount::create();
			// $account_api->clearAccessToken();
			$token = $account_api->getAccessToken();

			if(is_error($token)){
			    message('获取access token 失败');
			}

			$uniaccount = $token;
			$scene_str ="ims_leju_users:{$_W['uniacid']}:{$member['openid']}:{$poster['id']}";
			$data = '{"action_info":{"scene":{"scene_str":"' . $scene_str . '"} },"action_name":"QR_LIMIT_STR_SCENE"}';
			$access_token = $uniaccount;
			$url1 = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_URL, $url1);
			curl_setopt($ch1, CURLOPT_POST, 1);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
			$res = curl_exec($ch1);
			$content = @json_decode($res, true);
			// var_dump($content);die;
			if (!is_array($content)) {

				return false;
			}
			// var_dump($content);
			// die;
			if (!empty($content['errcode'])) {

				$account_api->clearAccessToken();
				$share['warn'] = 1;//报错
				// return error(-1, $content['errmsg']);
				message("请点击这里",$this->createMobileUrl("aboutmine",array('op'=>'share'),'error'));
			}

			$ticket = $content['ticket'];
			$model = array('barcode' => json_decode($data, true), 'ticket' => $ticket);
			$ticket = $model['ticket'];
			$share['code'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
			/**
			*		获取二维码结束
			*/
			$code = $share['code'];
			//把数据插入到数据库
			$result3 = pdo_update("leju_users",array("code"=>$share['code']),array("uid"=>$share['uid']));
		// }
		//加载分享页面

		include $this->template("share");

	}else if($option == "team"){

		//查取本人信息
		$private = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));
		//查找等级
		$private['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE uid=:uid",array("uid"=>$private['uid']));
		//查取佣金
		$private['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE uid=:uid",array("uid"=>$private['uid']));
		//把金币转化为千单位
		$private['comm']['money'] = $private['comm']['money']/1000;

		//查取团队其他人员的信息
		$group = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE recommend_id=:id",array("id"=>$private['id']));
		//查取相应的等级信息和佣金信息
		foreach ($group as $key => $value) {

			//查找相应的等级信息
			$group[$key]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$value['grade_id']));
			//查找相应的佣金信息
			$group[$key]['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$value['commission']));
			$group[$key]['comm']['money'] = $group[$key]['comm']['money']/1000;
			$type1 += substr_count(($group[$key]['rank']['type']), 1);
			$type2 += substr_count(($group[$key]['rank']['type']), 2);
			$type3 += substr_count(($group[$key]['rank']['type']), 3);

		}
		//判断此人是否为一级
		if($private['rank']['type'] == 1){

			//判断此人的等级从而判断前台输出的内容
			foreach ($group as $k => $val) {
				//判断此人是否为一级
				if($private['rank']['type'] == 1 && $val['rank']['type'] == 3){

					unset($group[$k]);
				}

			}
		}

		//查取自己推荐的客户信息
		$team = pdo_fetchall("SELECT * FROM ".tablename("leju_client")." WHERE recommon_id=:id",array("id"=>$private['id']));
		//加载我的团队页面
		include $this->template("team",$private,$group,$type2,$type3);

	}elseif($option=='yjin'){
		//查取数据
		$share1 = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$uid));

		$yjins=pdo_fetchall('select * from '.tablename('leju_record').' where user=:user',array(':user'=>$share1['id']));
		// foreach ($yjins as &$v) {
		// 	$v['time']=substr($v['time'],0,10);
		// }
		// unset($v);
		include $this->template("yjin");
	}else if($option=='delrc'){
		$id=intval($_GPC['id']);
		$res=pdo_update('leju_record',array('userstatus'=>1),array('id'=>$id));
		$url=$this->createMobileUrl('aboutmine',array('op'=>'yjin'));
		header("Location:".$url);
	}
	else if($option == "myself"){

		//自己推荐的客户详细信息
		$id = $_GPC['id'];
		//查找信息
		$myself = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$id));
		$myself['time'] = date("Y-m-d H:i:s",$myself['time']);
		//判断是否有分配时间
		// if($myself['allocation_time'] != 0){

		// 	$myself['allocation_time'] = date("Y-m-d H:i:s",$myself['allocation_time']);
		// }
		//判断是否有订房时间
		if($myself['book_time'] != 0){

			$myself['book_time'] = date("Y-m-d H:i:s",$myself['book_time']);
		}
		//记载页面
		include $this->template("myself");

	}else if($option == "rank"){

		//加载团队推荐的客户
		//查找信息
		$rank = pdo_fetchall("SELECT * FROM ".tablename("leju_client")." WHERE recommon_id=:id",array("id"=>$_GPC['pid']));
		$users = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['pid']));
		$users['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$users['grade_id']));
		$users['commis'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$users['commission']));
		//加载页面
		include $this->template("rank",$users);

	}else if($option == "run"){

		//查找本人信息
		$pop = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['id']));
		$pop['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$pop['grade_id']));
		$pop['commiss'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$pop['commission']));
		$pop['commiss']['money'] = $pop['commiss']['money']/1000;
		//查找此人队下的三级人物
		$run = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE recommend_id=:id",array("id"=>$_GPC['id']));
		//查找三级人员的信息
		foreach ($run as $ko => $ca) {

			$run[$ko]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$ca['grade_id']));
			//判断是否为三级
			if($run[$ko]['rank']['type'] != 3){

				unset($run[$ko]);
			}

		}
		//查找金钱信息
		foreach ($run as $ka => $po) {
			
			$run[$ka]['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$po['commission']));
			$run[$ka]['comm']['money'] = $run[$ka]['comm']['money']/1000;
		}
		$moa = count($run);
		//加载页面
		include $this->template("run",$pop,$moa);

	}else if($option == "order"){


		 //查取信息
		 $order = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE recommend_id=:id",array("id"=>$_GPC['cid']));
		 //查取本人的信息
		 $self = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['cid']));
		 $self['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$self['grade_id']));
		 $self['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$self['commission']));
		 //统计下线的数量
		 $self['math'] = count($order);
		 //查找佣金信息
		 foreach ($order as $mb => $lv) {
		 	$order[$mb]['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$lv['commission']));
		 }
		 //加载页面
		 include $this->template("order",$self);

	}else if($option == "details"){

		//加载客户的详细信息
		$details = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['mid']));
		//查找购房的信息
		$details['room'] = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE buy_id=:id",array("id"=>$_GPC['mid']));
		$details['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['realid']));

		//加载页面
		include $this->template("details");

	}else if($option == "users"){

		if($_W['ispost']){

			//查取推荐人的信息
			$monday1 = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:uid",array("uid"=>$_GPC["uid"]));
			//接收数据
			//接收客户信息
			$user['name'] = $_GPC['username'];
			$user['phone'] = $_GPC['phone'];
			$user['recommon_id'] = $monday1['id'];
			$user['intention'] = $_GPC['intention'];
			$user['station'] = $_GPC['address'];
			$user['time'] = time();
			$user['uniacid'] = $acid;
			// 此处还差推荐人的id或者是uid openid******
			// echo '<pre>';var_dump($_GPC);echo '<pre>';die;
			$year1 = date("Y");
			$month1 = date("m");
			$day1 = date("d");
			$start1 = mktime(0,0,0,$month1,$day1,$year1);//当天开始时间戳
			$end1= mktime(23,59,59,$month1,$day1,$year1);//当天结束时间戳
			//统计推荐的当天数量
			$ending =  pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("leju_client")." WHERE time<=:end AND time>=:start AND recommon_id=:id",array("start"=>$start1,"end"=>$end1,"id"=>$monday1['id']));

			$all1 = pdo_fetch("SELECT * FROM ".tablename("leju_math")." WHERE id=1");
			//查取
			//判断推荐人的推荐信息
			$as = intval($ending);
			$lao = intval($all1['client']);

			//判断推荐的数量是否超标
			if($as>=$lao){

				message("您今天推荐的人数已经达到上限",$this->createMobileUrl("home",array("op"=>"room")),"error");
			}



			if(($as < $lao) && !empty($_GPC['username']) && !empty($_GPC['phone'])){

				//判断是否存在信息
				$message = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE name=:name AND phone=:phone",array("name"=>$_GPC['username'],"phone"=>$_GPC['phone']));
				if(empty($message)){
					//添加信息
					$result2 = pdo_insert("leju_client",$user);
					$lol = pdo_insertid();
					/*****开始分配客户*******/

					//查取客户信息
					$customer = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id AND broker_status=:status",array("id"=>$lol,"status"=>0));
					//查找客户对应的站点信息
					$station = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$_GPC['address']));
					//查找推荐人是否为员工
					$duan = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id AND name=:name AND phone=:phone AND (title!=3 OR title!=4)",array("name"=>$monday1['realname'],"phone"=>$monday1['mobile'],"id"=>$customer['station']));
					// var_dump($pan);
					if (!empty($pan)) {
						
						 $staff = $duan;

					}else{

						//查找站点的员工信息
						$staff = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id AND title=3 OR title=4 ORDER BY math limit 1",array("id"=>$customer['station']));
					}
					
					//分配到此员工的身上
					$broker['broker_id'] = $staff['id'];
					$broker['broker_status'] = 1;
					$broker['buy_status'] = 1;
					$broker['brokername'] = $staff['name'];
					$broker['allocation_time'] = date("Y-m-d H:i:s",time());
					//修改客户状态
					$en = pdo_update("leju_client",$broker,array("id"=>$customer['id']));
					//修改员工的信息
					$mat = $staff['math'] + 1;
					$en1 = pdo_update("leju_staff",array("math"=>$mat),array("id"=>$staff['id']));

				/*******分配客户结束**************/

					//判断是否添加成功
					if(!empty($result2) && !empty($en)){


						$store1 = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE realname=:name AND mobile=:mobile",array("name"=>$staff['name'],"mobile"=>$staff['phone']));
						//发送消息通知
						$shi = date("Y-m-d H:i:s",time());
						$contentel = '分配提醒：你被分配新的购房客户，姓名：'.$customer['name'].' 电话：'.$customer['phone']."时间：".$shi;
						// $store1['openid'] = "oRZx2wgtcns7534PzjCiC2R3lQvU";
						// echo $store1['openid'];   
						$send1['touser'] = trim($store1['openid']);
						$send1['msgtype'] = 'text';
						$send1['text'] = array('content' => urlencode($contentel));
						$acc1 = WeAccount::create();
						$datsa1 = $acc1->sendCustomNotice($send1);

						// echo json_encode($datsa1);
						// die;
						message("恭喜您推荐成功",$this->createMobileUrl("home",array("op"=>"room")),"success");

					}else{

						message("对不起您推荐失败",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
					}
					
				}else{
					
					
						message("对不起您已经推荐过此客户",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
				}
				

			}


		}else{

					message("对不起您推荐失败",$this->createMobileUrl("aboutmine",array("op"=>"recommend")),"error");
				}





	}









 ?>