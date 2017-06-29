<?php


global $_GPC, $_W;
$action = 'brokerlist';
$url = $this->createWebUrl($action, array('op' => 'list'));
$url1 = $this->createWebUrl($action, array('op' => 'client'));
//判断是否为空
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

//判断传输的数据类型
if($operation == 'list'){

	//设置条件
	$where = '';
	$params = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where.=' WHERE `nickname` LIKE :keyword OR `mobile` LIKE :keywords OR `realname` LIKE :keywordl';
		$params[':keywords'] = "%{$_GPC['keyword']}%";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
		$params[':keywordl'] = "%{$_GPC['keyword']}%";
		
	}
	
	// //查取数据
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('leju_users').$where);
	$pager = pagination($total, $pindex, $psize,$params);
	//查取用户信息
	$economy_list = pdo_fetchall("SELECT * FROM ".tablename('leju_users').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
	//查取佣金和等级
	foreach($economy_list as $key => $value) {
		//查取佣金
		$result = pdo_fetch("SELECT * FROM ".tablename('leju_commission')." WHERE id=:id",array('id'=>$value['commission']));
		//查取等级
		$result1 = pdo_fetch("SELECT * FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$value['grade_id']));
		$result2 = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE id=:id",array('id'=>$value['recommend_id']));
		$economy_list[$key]['money'] = $result['money'];
		$economy_list[$key]['rank'] = $result1['type'];
		$economy_list[$key]['recommon_name'] = $result2['realname'];
	}
	
	include $this->template('economy_list');

}else if($operation == 'edit'){


	//查取经济人信息
	$id = $_GPC['id'];
	$economy_edit = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE id=:id",array('id'=>$id));
	$economy_edit['comm'] = pdo_fetch("SELECT * FROM ".tablename('leju_commission')." WHERE id=:id",array('id'=>$_GPC['commission']));  
	$economy_edit['rank'] = pdo_fetch("SELECT * FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$_GPC['grade_id']));  
	//加载添加信息页面
	include $this->template('economy_edit');
}else if ($operation == 'details') {

	//查取经纪人信息	
	$economy_details = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE id=:id",array('id'=>$_GPC['id']));
	$economy_details['rank'] = pdo_fetch("SELECT * FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$_GPC['grade_id']));
	$economy_details['comm'] = pdo_fetch("SELECT * FROM ".tablename('leju_commission')." WHERE id=:id",array('id'=>$_GPC['commission']));
	$users =  pdo_fetchall("SELECT * FROM ".tablename('leju_users')." WHERE recommend_id=:id",array("id"=>$_GPC['id']));
	$economy_details['room'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('leju_room')." WHERE recommend_id=:id",array("id"=>$_GPC['id']));
	$economy_details['client'] = pdo_fetchall("SELECT * FROM ".tablename('leju_client')." WHERE recommon_id=:id",array("id"=>$_GPC['id']));
	// var_dump($economy_details['client']);
	// if($economy_details['client']['name'] != ""){

	// 	$economy_details['mam'] = 0;

	// }else{

		$economy_details['mam'] = count($economy_details['client']);
	// }
	
	// echo $economy_details['mam'];
	//本人推荐的客户购房数量
	foreach ($economy_details['client'] as $kl => $var) {
		
		if(($var['buy_status'] == 3 || $var['buy_status'] == 4) && $var['buy_status'] != ""){

			$economy_details['buy'] += 1;

		}else{
			$economy_details['buy'] = 0;
		}
		if(($var['buy_status'] == 0 || $var['buy_status'] == 1 || $var['buy_status'] == 2) && $var['buy_status'] != ""){

			$economy_details['sale'] += 1;

		}else{
			$economy_details['sale'] = 0;
		}

		$economy_details['shu'] += $var['math'];
	}

	//查找本人的佣金具体详情
	$pindex2 = max(1, intval($_GPC['page']));
	$psize2 = 10;
	$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_record')." WHERE user=:id",array("id"=>$_GPC['id']));
	$pager2 = pagination($total2, $pindex2, $psize2);
	//查取所属于其站点的员工信息
	$station_staff = pdo_fetchall("SELECT * FROM ".tablename("leju_record")." WHERE user=:id ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,array("id"=>$_GPC['id']));

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
		foreach ($month as $keyo => $valueo) {
			
			$math += $valueo['math'];
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
			
			$minion1[$ky]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$vo['grade_id']));
			//判断是否有二级
			if($minion1[$ky]['rank']['type'] == 2){

				//修改他的推荐id
				$finishel = pdo_update("leju_users",array("recommend_id"=>$vo['record_recommon']),array("id"=>$vo['id']));
			}

		}
	}

/**
*		结束
*/
	//加载经纪人详情页面
	
	if(!empty($economy_details)){

		include $this->template('economy_details',$account2,$users,$type1,$type2,$type3,$station_staff);
	}

}else if($operation == 'delete'){

	//判断是否接收数据
	if(!empty($_GPC['commission']) && !empty($_GPC['grade_id']) && !empty($_GPC['id'])){
		
		$result10 = pdo_delete('leju_users', array('id' => $_GPC['id']));
		$result3 = pdo_delete('leju_carray', array('commission_id' => $_GPC['commission']));
		$result11 = pdo_delete('leju_commission', array('id' => $_GPC['commission']));
		$result4 = pdo_delete('leju_rank', array('id' => $_GPC['grade_id']));
		// var_dump($result10);
		// var_dump($result3);
		// var_dump($result4);
		// var_dump($result11);

		if (!empty($result10) && !empty($result4) && !empty($result11) && !empty($result3)) {

   			 message('删除成功',$url);
		}else{

			message('删除失败',$url);
		}
	}else{

			message('删除失败',$url);
	}
	
}else if($operation == 'update'){

	//接收数据
	$data['realname'] = $_GPC['name'];
	$data['mobile'] = $_GPC['mobile'];
	$data['Idcard'] = $_GPC['Idcard'];
	$data['type'] = $_GPC['type'];
	$data['accredit'] = $_GPC['accredit'];
	//佣金
	$data1['money'] = $_GPC['money'];
	//等级
	$data2['type'] = $_GPC['rank']; 

	$result5 = pdo_update('leju_users', $data, array('id' =>$_GPC['id']));
	$result6 = pdo_update('leju_commission', $data1, array('id' =>$_GPC['commission_id']));
	$result7 = pdo_update('leju_rank', $data2, array('id' =>$_GPC['rank_id']));
	//判断是否成功
	if(!empty($result5) || !empty($result6) || !empty($result7)){

		message('修改成功',$url);
	}else{

		message('修改失败');
	}

}else if($operation == "client"){

	$where1 = '';
	$params1 = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where1.=' WHERE `name` LIKE :keyword OR `phone` LIKE :keywords OR `time` LIKE :keywordl';
		$params1[':keywords'] = "%{$_GPC['keyword']}%";
		$params1[':keyword'] = "%{$_GPC['keyword']}%";
		$params1[':keywordl'] = "%{$_GPC['keyword']}%";
		
	}
	if(isset($_GPC['staff']) && !empty($_GPC['staff'])){

		$where1.=' WHERE broker_id=:id AND broker_status=1';
		$params1[':id'] = $_GPC['staff'];
		$sta = $_GPC['staff'];
	}
	// //查取数据
	$pindex1 = max(1, intval($_GPC['page']));
	$psize1 = 200;
	$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('leju_client').$where1);
	$pager1 = pagination($total1, $pindex1, $psize1,$params1);
	//查取客户信息
	$client_list = pdo_fetchall("SELECT * FROM ".tablename('leju_client').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
	//查找店员信息
	$staff =  pdo_fetchall("SELECT * FROM ".tablename('leju_staff'));
	//查取推荐人信息
	foreach ($client_list as $keya => $valuea) {
		$client_list[$keya]['time'] = date("Y-m-d H:i:s",$valuea['time']);
		$client_list[$keya]['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$valuea['recommon_id']));
		$client_list[$keya]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$client_list[$keya]['users']['grade_id']));
	}

	//加载页面
	include $this->template('client_list',$staff,$sta);

}else if($operation == "client_edit"){

	//修改客户信息
	//查取客户信息
	$client_edit = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['id']));
	$pusher = pdo_fetchall("SELECT * FROM ".tablename("leju_users"));
	//查取客户被分配到员工的信息
	$stf = pdo_fetchall("SELECT * FROM ".tablename("leju_staff"));
	//查找店面信息
	foreach ($stf as $ku => $lol) {
		
		$stf[$ku]['site'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$lol['station']));
	}
	//加载修改页面
	include $this->template("client_edit",$pusher,$stf);


}else if($operation == "client_delete"){

	//删除客户信息
	if(!empty($_GPC['id'])){
	
		$client_id = $_GPC['id'];
		//查找客户信息
		$hate = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$client_id));
		$tol = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$hate['broker_id']));
		//修改分配员工的信息
		$count = $tol['math'] - 1;
		//修改信息
		$over = pdo_update("leju_staff",array("math"=>$count),array("id"=>$tol['id']));
		$end = pdo_delete("leju_client",array("id"=>$client_id));
		//判断是否删除
		if(!empty($end)){

			message("删除客户信息成功",$url1);
		}else{

			message("删除客户信息失败");
		}
	}

}else if($operation == "client_details"){

	//客户具体信息
	//查找信息
	$client_details = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['id']));
	$client_details['time'] = date("Y-m-d H:i:s",$client_details['time']);
	$users =pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$_GPC['users']));
	$rank = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$_GPC['rank']));
	//查找此客户购买的房源信息
	$pindex4 = max(1, intval($_GPC['page']));
	$psize4 = 20;
	$total4 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_room')." WHERE buy_id=:id",array("id"=>$client_details['id']));
	$pager4 = pagination($total4, $pindex4, $psize4);
	
	//查取数据
	$person =pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE buy_id=:id ORDER BY id ASC LIMIT ". ($pindex4 - 1) * $psize4 . ',' . $psize4,array("id"=>$client_details['id']));
	foreach ($person as $kop => $loa) {
		
		$person[$kop]['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$loa["recommend_id"]));
		
	}
	// var_dump($_W['token']);
	//加载页面
	include $this->template("client_details",$users,$rank);

}else if($operation == "client_update"){

	//判断是否为post
	if($_W['ispost'] && !empty($_POST)){

		$client_update['name'] = $_GPC['name'];
		$client_update['phone'] = $_GPC['phone'];
		$client_update['intention'] = $_GPC['intention'];
		$client_update['recommon_id'] = $_GPC['pusher'];
		//判断是否修改负责人信息
		if($_GPC['staff_id'] != $_GPC['staff']){

			//修改员工的分配数量
			$staf = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['staff_id']));
			$shu = $staf['math'] - 1;
			$e = pdo_update("leju_staff",array("math"=>$shu),array("id"=>$staf['id']));
			//查找新分配的站点信息
			$staf1 = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['staff']));
			$shu1 = $staf1['math'] + 1;
			$e1 = pdo_update("leju_staff",array("math"=>$shu1),array("id"=>$staf1['id']));

			$sto = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$staf1['station']));
			//修改客户的分配员工信息
			$client_update['broker_id'] = $_GPC['staff'];
			$client_update['station'] = $sto['id'];
			$client_update['brokername'] = $staf1['name'];
			$client_update['allocation_time'] = date("Y-m-d H:i:s",time());


		}
		//执行修改
		$ending = pdo_update("leju_client",$client_update,array("id"=>$_GPC['id']));

		//判断
		if(!empty($ending)){

			message('修改客户信息成功',$url1);
		}else{

			message("修改客户信息失败");
		}

	}
	
}



?>