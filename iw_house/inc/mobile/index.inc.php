<?php
	global $_GPC, $_W;

	$uid = $_W['fans']['uid'];//获取当前用户的uid
	$openid = $_W['fans']['openid'];//获取当前用户的openid
	$avatar = $_W['fans']['tag']['avatar'];//获取当前用户的头像信息
	$nickname = $_W['fans']['tag']['nickname'];//获取当前用户的昵称信息
	$result = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE uid = :uid",array("uid"=>$uid));

	    //判断数据库是否存在此用户
	    if($result && !empty($result['mobile'])){
			

	    	//判断是否有头像
	    	if($result['avatar'] == '' && $avatar !== '' && $result['avatar'] != $avatar){

	    		//修改头像
	    		$data['avatar'] = $avatar;
	    		pdo_update('leju_users',$data, array('uid' =>$uid));
	    	}
	    	//判断是否有昵称
	    	if($result['nickname'] == '' && $result['nickname'] != $nickname){

	    		//修改头像
	    		$data1['nickname'] = $nickname;
	    		pdo_update('leju_users',$data1, array('uid' =>$uid));
	    	}
	/*******************开始判断推荐人的是否达到升级条件***************************/

	    	//查取经纪人信息	
			$economy_details = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE uid=:id",array('id'=>$uid));
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


	    	//查取数据 普通类型
			$room = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=0 AND pay_status!=3 ORDER BY id ASC");
			//查找推荐房源
			$home = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=1 AND pay_status!=3 ORDER BY id ASC");
			//查找房源城市信息
			$city = pdo_fetchall("SELECT * FROM ".tablename('leju_station'));
			$type = pdo_fetchall("SELECT * FROM ".tablename('leju_pattern'));
			//查找房源的区域信息
			$area = pdo_fetchall("SELECT * FROM ".tablename('leju_area')); 

			//查找房源的图片信息
			foreach ($room as $key => $value) {
				
				$room[$key]['pictures'] = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$value['photo_id']));
				$room[$key]['pat'] = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$value['pattern']));
				$room[$key]['cat'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$value['city']));
			}
			foreach ($home as $ky => $val) {
				
				$home[$ky]['pictures'] = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$val['photo_id']));
				$home[$ky]['pat'] = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$val['pattern']));
				$home[$ky]['cat'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$val['city']));
			}
			//查找相应的房源位置的房源
			foreach ($area as $ko => $cao) {
				$a = pdo_fetch("SELECT * FROM ".tablename('leju_room')." WHERE liation=:lation AND pay_status!=3 ORDER BY id DESC",array("lation"=>$cao['id']));
				$area[$ko]['photo'] = pdo_fetch("SELECT imguqcl FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$a['photo_id']));
			}
			//查取广告位信息
			$tisment = pdo_fetchall("SELECT * FROM ".tablename("leju_tisment"));
			// var_dump($room);
			//加载页面
			// var_dump($openid);
			include $this->template("room",$home,$city,$type,$tisment,$area);
			
	    }else{

	    	$register = $uid;
	    	//加载注册的页面
			include $this->template("register");
	    }
	


	
	


	

?>