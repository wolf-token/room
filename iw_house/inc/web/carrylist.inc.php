<?php 

	global $_GPC, $_W;

	load()->func('tpl');
	$action = 'carrylist';
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	if($operation == "list"){


		//设置条件
		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where.=' AND `name` LIKE :keyword OR `cell` LIKE :keywords OR `time` LIKE :keyworda';
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keyworda'] = "%{$_GPC['keyword']}%";
			
		}

		//查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('leju_carray')." WHERE status=1".$where);
		
		$pager = pagination($total, $pindex, $psize,$params);
		//查取用户信息
		$carry_list = pdo_fetchall('SELECT * FROM '.tablename('leju_carray')." WHERE status=1".$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		// var_dump($carry_list);
		// die;
		foreach ($carry_list as $key => $value) {
			//查取用户的佣金
			$carry_list[$key]['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:id",array("id"=>$value['uid']));
			//查取提取信息
			$carry_list[$key]['users']['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$carry_list[$key]['users']['grade_id']));
			//查取等级信息
			$carry_list[$key]['users']['come'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE  id=:id",array("id"=>$carry_list[$key]['users']['commission']));

		}
		
		//加载提现列表页面
		include $this->template('carry_list');

	}else if ($operation == "edit") {
		
		//查取数据
		$carry_edit = pdo_fetch("SELECT * FROM ".tablename("leju_carray")." WHERE id=:id",array("id"=>$_GPC['carray']));
		$data = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['id']));
		$commission = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$_GPC['commission']));
		//加载修改页面
		include $this->template('carry_edit',$data,$commission);

	}else if($operation == "add"){

		$max1 = pdo_fetch("SELECT * FROM ".tablename("leju_rule")." WHERE id=1");
		//查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename('leju_commission')." WHERE money!=0");
		
		$pager1 = pagination($total1, $pindex1, $psize1);
		//查取用户信息
		$carry_add = pdo_fetchall('SELECT * FROM '.tablename('leju_commission')." WHERE money!=0 ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1);
		// var_dump($carry_list);
		// die;
		foreach ($carry_add as $key1 => $value1) {
			//查取用户的佣金
			$carry_add[$key1]['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE uid=:id",array("id"=>$value1['uid']));
			//查取提取信息
			$carry_add[$key1]['carray'] = pdo_fetch("SELECT * FROM ".tablename("leju_carray")." WHERE uid=:id",array("id"=>$value1['uid']));
			//查取等级信息
			$carry_add[$key1]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE  uid=:id",array("id"=>$value1['uid']));

		}
		//加载添加页面
		include $this->template('carry_add',$max1);

	}else if($operation == "update"){
		
		//判断是否有post
		if($_W['ispost']){


			//查取信息
			$date = pdo_fetch("SELECT * FROM ".tablename("leju_carray")." WHERE id=:id;",array("id"=>$_GPC['id']));
			$date1 = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$_GPC['commission_id']));

			if($date['money'] == $_GPC['money']){

				$addmoney = $date1['money'];

			}

			if($date['money'] > $_GPC['money']){

				$add = $date['money'] - $_GPC['money'];
				$addmoney = $date1['money'] + $add;
			}

			if($date['money'] < $_GPC['money']){

				$add1 = $_GPC['money'] - $date['money'];
				$addmoney = $date1['money'] - $add1;
			}
			
			$cat['money'] = $_GPC['money']; 
			$cat['name'] = $_GPC['name']; 
			$cat['cell'] = $_GPC['mobile']; 
			$cat['status'] = $_GPC['status']; 
			//执行修改
			$result5 = pdo_update("leju_carray",$cat,array("id"=>$_GPC['id']));
			$result6 = pdo_update("leju_users",array("carray_status"=>$_GPC['status']),array("id"=>$_GPC['id']));
			$result7 = pdo_update("leju_commission",array("money"=>$addmoney),array("id"=>$_GPC['commission_id']));
			if(!empty($result5) || !empty($result6) || !empty($result7)){

				message("更改成功",$url);
			}else{

				message("更改失败");
			}

		}
		

	}else if($operation == "delete"){

		//删除提现记录
		$id = $_GPC['id'];
		$commission_id = $_GPC['commission_id'];
		$carray_id = $_GPC['carray'];
		
		// $result = pdo_update("leju_users",array("carray_status"=>0),array("id"=>$id));
		$result1 = pdo_delete("leju_carray",array("id"=>$carray_id));
		// var_dump($result);
		// var_dump($result1);
		// die;
		if(!empty($result1)){

			message('删除提现记录成功',$url);
		}else{
			message("删除提现记录失败");
		}
	
	}else if($operation == "carray"){

		//查取最大的提取值
		$max = pdo_fetch("SELECT * FROM ".tablename("leju_rule")." WHERE id=1");
		if($_GPC['money'] > $max['max']){

			message("对不起您的提取金额超出最大提取金额，请重新输入提取金额");
		}
		//判断是否输入金额
		if(empty($_GPC['money'])){

			message('提现金额不能为空');
		}
		if(empty($_GPC['name'])){

			message('提款人姓名不能为空');
		}
		if(empty($_GPC['cell'])){

			message('提款人电话不能为空');
		}
		$old_money = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$_GPC['id']));
		if($_GPC['money']>$old_money['money']){

			message('您好您的提取金额超出您的佣金数,请重新输入');
		}
		
		//接收数据
		$id1 = $_GPC['id'];
		$users_id1 = $_GPC['users_id'];
		$money = $old_money['money'] - $_GPC['money'];
		$time = date("Y-m-d H:i:s",time());
		//查找信息
		$end = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$users_id1));
		$carray1['name'] = $_GPC['name'];
		$carray1['cell'] = $_GPC['cell'];
		$carray1['money'] = $_GPC['money'];
		$carray1['time'] = $time;
		$carray1['uid'] = $end['uid'];
		$carray1['commission_id'] = $end['commission'];
		$carray1['status'] = 1;
		$carray1['uniacid'] = $_W['uniacid'];
		//执行修改
		$result2 = pdo_update("leju_commission",array("money"=>$money,"addtime"=>$time),array("id"=>$id1));
		$result3 = pdo_insert("leju_carray",$carray1);
		$result4 = pdo_update("leju_users",array("carray_status"=>1,"carray_time"=>$time),array("id"=>$users_id1));

		//判断是否修改成功
		if(!empty($result2) && !empty($result3) && !empty($result4)){

			message("提现成功",$url);
		}else{

			message("提现失败");
		}


	}
	

 ?>