<?php 

	global $_GPC, $_W;
	$action = 'systemlist';
	$url = $this->createWebUrl($action,array("op"=>'rule'));
	$url1 = $this->createWebUrl($action,array("op"=>'rank'));
	$url2 = $this->createWebUrl($action,array("op"=>'rank_list_one'));
	$url3 = $this->createWebUrl($action,array("op"=>'assign'));
	$url4 = $this->createWebUrl($action,array("op"=>'agreement'));
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'rule';

	if($operation == "rule"){

			//查取信息
			$system_list = pdo_fetchall("SELECT * FROM ".tablename("leju_rule"));
			//加载规则显示页面
			include $this->template('system_list');
		
		
	}else if($operation == "rule_add"){

		//判断是否是添加
		if($_W['ispost']){

			//判断是否已经存在规则信息
			$result1 = pdo_fetchall("SELECT * FROM ".tablename("leju_rule"));
			if(!empty($result1)){

				message("提现规则已存在不能再添加");
			}else{
				//判断是否添加信息
				if(empty($_GPC['content'])){

					message("请添加提现说明");
				}
				if(empty($_GPC['max'])){

					message("请添加提现最大值");
				}
				if(!empty($_GPC['max'])){

					$rule['max'] = $_GPC['max'];
				}
				//接收数据
				$rule['content'] = $_GPC['content'];
				$rule['time'] = date("Y-m-d H:i:s",time());
				//添加信息
				$result = pdo_insert("leju_rule",$rule);
				//判断是否添加成功
				if(!empty($result)){

					message("添加提现规则成功",$url);
				}else{

					message("添加提现规则失败");
				}

			}
		
		}else{

			include $this->template("rule_add");
		}

	}else if($operation == "rule_edit"){

		//查取信息
		$rule_edit = pdo_fetch("SELECT * FROM ".tablename("leju_rule")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载提现规则修改页面
		include $this->template('rule_edit');

	}else if($operation == "rule_update"){

		if($_W['ispost']){

			if(empty($_GPC['content'])){

				message('体现说明不能为空');
			}

			if(empty($_GPC['max'])){

				$max1['max'] = 0;
			}else{
				$max1['max'] = $_GPC['max'];
			}

			$max1['content'] = $_GPC['content'];
			$max1['time'] = date("Y-m-d H:i:s",time());
			//执行修改
			$result2 = pdo_update("leju_rule",$max1,array("id"=>$_GPC['id']));
			//判断是否添加成功
				if(!empty($result2)){

					message("修改提现规则成功",$url);
				}else{

					message("修改提现规则失败");
				}
		}

	}else if($operation == "rank") {

			if($_W['ispost']){

				//接收数据
				$date['person'] = $_GPC['person'];
				$date['room'] = $_GPC['room'];
				$date['information'] = $_GPC['content'];
				//执行修改
				$result5 = pdo_update("leju_scale_two",$date,array("id"=>$_GPC['id']));
				if(!empty($result5)){

					message("修改成功",$url1);
				}else{
					message("修改失败");
				}
			}else{

			/**
			*		获取二维码
			*/
			// $poster = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=1629");
			// $member = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=1629");
			// $account_api = WeAccount::create();
			// $token = $account_api->getAccessToken();
			// if(is_error($token)){
			//     message('获取access token 失败');
			// }
			// $uniaccount = $token;
			// $scene_str = md5("ims_leju_users:{$_W['uniacid']}:{$member['openid']}:{$poster['id']}");
			// $data = '{"action_info":{"scene":{"scene_str":"' . $scene_str . '"} },"action_name":"QR_LIMIT_STR_SCENE"}';
			// $access_token = $uniaccount;
			// $url1 = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='. $access_token;
			// $ch1 = curl_init();
			// curl_setopt($ch1, CURLOPT_URL, $url1);
			// curl_setopt($ch1, CURLOPT_POST, 1);
			// curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
			// curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
			// curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
			// curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
			// $res = curl_exec($ch1);
			// $content = @json_decode($res, true);
			// if (!is_array($content)) {
			// 	return false;
			// }
			// if (!empty($content['errcode'])) {
			// 	return error(-1, $content['errmsg']);
			// }
			// $ticket = $content['ticket'];
			// $model = array('barcode' => json_decode($data, true), 'ticket' => $ticket);
			// $ticket = $model['ticket'];
			// $rank_list = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
			
			/**
			*		获取二维码结束
			*/

			//查取规则信息
			$rank_list = pdo_fetchall("SELECT * FROM ".tablename("leju_scale_two"));
			//加载等级设置页面
			include $this->template('rank_list');
		}
	}else if($operation == "rank_add"){

		if($_W['ispost']){

			//判断是否已经存在规则信息
			$result4 = pdo_fetchall("SELECT * FROM ".tablename("leju_scale_two"));
			if(!empty($result4)){

				message("升级规则已存在不能再添加");
			}else{

				if (empty($_GPC['person'])) {
					
					message("请添加升级规则");
				}
				if (empty($_GPC['math'])) {
					
					message("请添加客户购房规则");
				}
				//接收添加的数据
				$rank_add['person'] = $_GPC['person'];
				$rank_add['room'] = $_GPC['math'];
				$rank_add['information'] = $_GPC['content'];
				//添加数据
				$result3 = pdo_insert("leju_scale_two",$rank_add);
				//判断
				if(!empty($result3)){

					message("添加提现规则成功",$url1);
				}
			}
		}else{

			//加载添加页面
			include $this->template("rank_add");
		}

	}else if($operation == "rank_edit"){

		//查找信息
		$rank_edit = pdo_fetch("SELECT * FROM ".tablename("leju_scale_two")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载修改页面
		include $this->template("rank_edit");

	}else if($operation == "rank_list_one"){
		if($_W['ispost']){


				//接收数据
				$date1['math'] = $_GPC['math'];
				$date1['room'] = $_GPC['room'];
				$date1['information'] = $_GPC['content'];
				//执行修改
				$result8 = pdo_update("leju_scale_one",$date1,array("id"=>$_GPC['id']));
				if(!empty($result8)){

					message("修改成功",$url2);
				}else{
					message("修改失败");
				}
			}else{

			//查取信息
			$rank_list_one = pdo_fetchall("SELECT * FROM ".tablename("leju_scale_one"));
			//加载修改页面
			include $this->template("rank_list_one");
		}
	}else if($operation == "rank_add_one"){
			if($_W['ispost']){

			//判断是否已经存在规则信息
			$result6 = pdo_fetchall("SELECT * FROM ".tablename("leju_scale_one"));
			if(!empty($result6)){

				message("升级规则已存在不能再添加");
			}else{

				if (empty($_GPC['person'])) {
					
					message("请添加团队人数规则");
				}
				if (empty($_GPC['math'])) {
					
					message("请添加客户购房数量规则");
				}
				//接收添加的数据
				$rank_add1['math'] = $_GPC['person'];
				$rank_add1['room'] = $_GPC['math'];
				$rank_add1['information'] = $_GPC['content'];
				//添加数据
				$result7 = pdo_insert("leju_scale_one",$rank_add1);
				//判断
				if(!empty($result7)){

					message("添加提现规则成功",$url2);
				}else{
					message("添加失败");
				}
			}

		}else{

			//添加二级升一级的条件
			include $this->template("rank_add_one");
		}

	}else if($operation == "rank_edit_one"){

		//查找信息
		$rank_edit_one = pdo_fetch("SELECT * FROM ".tablename("leju_scale_one")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载修改页面
		include $this->template("rank_edit_one");

	}else if($operation == "assign"){

		//查找信息
		$assign_list = pdo_fetchall("SELECT * FROM ".tablename("leju_assign"));
		//加载分配金额设置页面
		include $this->template('assign_list');

	}else if($operation == "assign_add"){

		//判断是否为上传
		if($_W['ispost']){
			//判断是否已有规则
			$ending = pdo_fetchall("SELECT * FROM ".tablename("leju_assign"));
			if(!empty($ending)){

				message("金额分配规则已存在不能再添加");

			}else{

			
				//判断是否是上传信息
				if(empty($_GPC['private'])){

					message("经济人推荐房源佣金标准不能为空");
				}
				if(empty($_GPC['rank_one'])){

					message("一级经纪人分配不能为空");
				}
				if(empty($_GPC['rank_two'])){

					message("二级经纪人分配不能为空");
				}
				if(empty($_GPC['rank_three'])){

					message("三级经纪人分配不能为空");
				}
				if(empty($_GPC['assign_one'])){

					message("一级经纪人分配不能为空");
				}
				if(empty($_GPC['assign_two'])){

					message("二级经纪人分配不能为空");
				}
				if(empty($_GPC['assign_three'])){

					message("三级经纪人分配不能为空");
				}
				//接收数据
				$data['norm'] = $_GPC['private'];
				$data['rank_one'] = $_GPC['rank_one'];
				$data['rank_two'] = $_GPC['rank_two'];
				$data['rank_three'] = $_GPC['rank_three'];
				$data['assign_one'] = $_GPC['assign_one'];
				$data['assign_two'] = $_GPC['assign_two'];
				$data['assign_three'] = $_GPC['assign_three'];
				$data['information'] = $_GPC['content'];
				//保存
				$end = pdo_insert("leju_assign",$data);
				//判断
				if(!empty($end)){

					message("保存信息成功",$url3);	
				}else{

					message("保存失败");
				}
			}
		}else{

			//加载分配金额添加页面
			include $this->template("assign_add");

		}
	}else if($operation == "assign_edit"){

		//查取信息
		$assign_edit = pdo_fetch("SELECT * FROM ".tablename("leju_assign")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载修改页面
		include $this->template("assign_edit");

	}else if($operation == "assign_update"){	

		if($_W['ispost']){
			
			
			//接收数据
			$assign_update['norm'] = $_GPC['norm'];
			$assign_update['rank_one'] = $_GPC['rank_one'];
			$assign_update['rank_two'] = $_GPC['rank_two'];
			$assign_update['rank_three'] = $_GPC['rank_three'];
			$assign_update['assign_one'] = $_GPC['assign_one'];
			$assign_update['assign_two'] = $_GPC['assign_two'];
			$assign_update['assign_three'] = $_GPC['assign_three'];
			$assign_update['information'] = $_GPC['content'];
			//执行修改
			$endoer = pdo_update("leju_assign",$assign_update,array("id"=>$_GPC['id']));
			
			//判断
			if(!empty($endoer)){

				message("修改分配金额成功",$url3);
			}else{

				message("修改分配金额失败");
			}
		}
		
	}else if($operation == "agreement"){

		//查取信息
		$agreement = pdo_fetchall("SELECT * FROM ".tablename("leju_agreement"));
		//加载用户协议页面
		include $this->template("agreement");

	}else if($operation == "agreement_add"){

		//加载添加页面
		if($_W['ispost']){

			//判断是否为空
			if(empty($_GPC['content'])){

				message("用户服务协议不能为空");
			}
			//判断是否已经添加
			$agreement_add1 = pdo_fetchall("SELECT * FROM ".tablename("leju_agreement"));
			if(!empty($agreement_add1)){

				message("已经存在规则不能再添加");

			}else{

				//接收数据
				$agreement_add['information'] = $_GPC['content'];
				$agreement_add['time'] = date("Y-m-d H:i:s",time());
				//添加数据
				$endoer1 = pdo_insert("leju_agreement",$agreement_add);

				if(!empty($endoer1)){

					message("添加成功",$url4);
				}else{

					message("添加失败");
				}

			}
			
		}else{

			include $this->template("agreement_add"); 
		}
	}else if($operation == "agreement_edit"){

		//查取信息
		$agreement_edit = pdo_fetch("SELECT * FROM ".tablename("leju_agreement")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载修改页面
		include $this->template("agreement_edit");

	}else if($operation == "agreement_update"){

		//判断是否为修改
		if($_W['ispost']){

			if(empty($_GPC['content'])){

				message("修改信息不能为空");
			}
			//接收数据
			$agreement_update['information'] = $_GPC['content'];
			$agreement_update['time'] = date("Y-m-d H:i:s",time());
			//修改信息
			$empty = pdo_update("leju_agreement",$agreement_update,array("id"=>$_GPC['id']));

			//判断
			if(!empty($empty)){

				message("修改成功",$url4);
			}else{

				message("修改失败");
			}

		}
	}
	

 ?>