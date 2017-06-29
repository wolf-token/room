<?php 

	global $_W,$_GPC;
	$action = 'record';
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if($operation == "list"){

		//设置条件
		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['staff']) && !empty($_GPC['staff'])){

				$where.=' WHERE user=:id';
				$params[':id'] = $_GPC['staff'];
				$sta = $_GPC['staff'];
		}
		
		//查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 150;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('leju_record').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		//查取记录信息
		$record = pdo_fetchall("SELECT * FROM ".tablename('leju_record').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		//查找用户信息
		foreach ($record as $key => $value) {
			//查找用户信息
			$record[$key]['cust'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$value['user']));
			//查找用户的等级信息
			$record[$key]['cust']['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$record[$key]['cust']['grade_id']));
			//查找用户的佣金总数
			$record[$key]['cust']['comm'] = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$record[$key]['cust']['commission']));
		}
		//查找所有用户的信息
		$users = pdo_fetchall("SELECT * FROM ".tablename("leju_users"));
		//加载页面
		include $this->template("record",$users,$sta);



	}else if($operation == "delete"){

		//删除信息
		$result = pdo_delete("leju_record",array("id"=>$_GPC['id']));

		//判断
		if($result){

			message("删除佣金记录成功",$url);
			
		}else{

			message("删除佣金记录失败");

		}
	}


 ?>