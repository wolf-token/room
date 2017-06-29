<?php 

	global $_GPC, $_W;
	$action = 'stationlist';
	$url = $this->createWebUrl($action, array('op' => 'list'));
	$url1 = $this->createWebUrl($action, array('op' => 'staff'));
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

	if($operation == 'list'){

		$where = '';
		$params = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where.=' WHERE `name` LIKE :keyword OR `cell` LIKE :keywords OR `username` LIKE :keywordl OR `time` LIKE :keyworda OR `phone` LIKE :keywordp OR `province` LIKE :keywordt OR `city` LIKE :keywordt1 OR `county` LIKE :keywordt2';
			$params[':keywords'] = "%{$_GPC['keyword']}%";
			$params[':keyword'] = "%{$_GPC['keyword']}%";
			$params[':keywordl'] = "%{$_GPC['keyword']}%";
			$params[':keyworda'] = "%{$_GPC['keyword']}%";
			$params[':keywordp'] = "%{$_GPC['keyword']}%";
			$params[':keywordt'] = "%{$_GPC['keyword']}%";
			$params[':keywordt1'] = "%{$_GPC['keyword']}%";
			$params[':keywordt2'] = "%{$_GPC['keyword']}%";
			
		}
			
		//查取数据
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_station').$where);
		$pager = pagination($total, $pindex, $psize,$params);
		
		//查取数据
		$station = pdo_fetchall("SELECT * FROM ".tablename('leju_station').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
			
		//加载站点管理页面
		include $this->template("station");

	}else if($operation == 'add'){
		if($_W['ispost']){

			//判断信息是否完整
			if(empty($_GPC['name'])){

				message('站点名称不能为空');
			}
			if(empty($_GPC['cell'])){

				message('站点联系电话不能为空');
			}
			if(empty($_GPC['username'])){

				message('站点负责人姓名不能为空');
			}
			if(empty($_GPC['phone'])){

				message('站点负责人电话不能为空');
			}
			if(empty($_GPC['province'])){

				message('站点所属省不能为空');
			}
			if(empty($_GPC['city'])){

				message('站点所属市不能为空');
			}
			if(empty($_GPC['county'])){

				message('站点所属县/区不能为空');
			}
			
			//接收数据
			$data['name'] = $_GPC['name'];
			$data['cell'] = $_GPC['cell'];
			$data['username'] = $_GPC['username'];
			$data['phone'] = $_GPC['phone'];
			$data['province'] = $_GPC['province'];
			$data['city'] = $_GPC['city'];
			$data['county'] = $_GPC['county'];
			$data['pictures'] = $_GPC['living'];
			$data['time'] = date("Y-m-d H:i:s",time());

			//添加数据
			$result = pdo_insert("leju_station",$data);
			//判断
			if(!empty($result)){

				message("添加站点成功",$url);
			}else{

				message("添加站点失败");
			}

		}else{

			//加载添加站点页面
			include $this->template("station_add");
		}
		

	}else if($operation == "delete"){

		//删除站点信息
		//判断
		if(!empty($_GPC['id'])){

			//修改所有起站点下面的员工所属站点
			$end = pdo_update("leju_staff",array("station"=>0),array("station"=>$_GPC['id']));
			//删除数据
			$result2 = pdo_delete("leju_station",array("id"=>$_GPC['id']));
			
			if (!empty($result2)) {

			    message('删除成功',$url);
			}else{

				message('删除失败');
			}

		}else{

			message('加载删除页面失败');
		}

	}else if($operation == "edit"){

		//查找信息
		$station_edit = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$_GPC['id']));
		//加载站点修改页面
		include $this->template("station_edit");

	}else if($operation == "details"){

		
		//查取站点信息
		$station_details = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id ",array("id"=>$_GPC['id']));
		
		$pindex2 = max(1, intval($_GPC['page']));
		$psize2 = 20;
		$total2 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_staff')." WHERE station=:id",array("id"=>$_GPC['id']));
		$pager2 = pagination($total2, $pindex2, $psize2);
		//查取所属于其站点的员工信息
		$station_staff = pdo_fetchall("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id ORDER BY id ASC LIMIT ". ($pindex2 - 1) * $psize2 . ',' . $psize2,array("id"=>$_GPC['id']));
		//统计人数
		$station_details['math'] = count($station_staff);
		//统计男女的分别数量
		foreach ($station_staff as $key1 => $value1) {
			
			if($value1['gender'] == 1){

				$station_details['man'] += 1;

			}

			if($value1['gender'] == 0){

				$station_details['woman'] += 1;
			}
		}
		//加载详情页面
		include $this->template("station_details",$station_staff);

	}else if($operation == "update"){

		//修改站点信息
		if($_W['ispost']){

			//接收数据
			$update['name'] = $_GPC['name'];
			$update['cell'] = $_GPC['cell'];
			$update['username'] = $_GPC['username'];
			$update['phone'] = $_GPC['phone'];
			$update['province'] = $_GPC['province'];
			$update['city'] = $_GPC['city'];
			$update['county'] = $_GPC['county'];
			$update['pictures'] = $_GPC['pictures'];
			$update['time'] = date("Y-m-d H:i:s",time());

			//执行修改
			$result3 = pdo_update("leju_station",$update,array("id"=>$_GPC['id']));

			//判断
			if(!empty($result3)){

				message("修改站点信息成功",$url);
			}else{

				message("修改站点信息失败");
			}
		}

	}else if($operation == "staff"){

		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where1.=' WHERE `name` LIKE :keyword OR `phone` LIKE :keywords OR `time` LIKE :keywordl';
			$params1[':keywords'] = "%{$_GPC['keyword']}%";
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			$params1[':keywordl'] = "%{$_GPC['keyword']}%";
			
			
		}
			
		//查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_staff').$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		
		//查取数据
		$staff = pdo_fetchall("SELECT * FROM ".tablename('leju_staff').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		//查找站点
		foreach ($staff as $key => $value) {
				
				$staff[$key]['station'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$value['station']));
			}	
		//加载员工信息页面
		include $this->template("staff");

	}else if($operation == "staff_add"){

		if($_W['ispost']){


			//判断信息是否完整
			if(empty($_GPC['name'])){

				message('员工姓名不能为空');
			}
			if(empty($_GPC['cell'])){

				message('员工联系电话不能为空');
			}
			if($_GPC['title'] == ""){

				message('员工职位不能为空');
			}
			if($_GPC['sex'] == ""){

				message('员工性别不能为空');
			}
			if(empty($_GPC['station'])){

				message('请选择所属站点');
			}
			
			//接收数据
			$date['name'] = $_GPC['name'];
			$date['phone'] = $_GPC['cell'];
			$date['gender'] = $_GPC['sex'];
			$date['title'] = $_GPC['title'];
			$date['pictures'] = $_GPC['living'];
			$date['station'] = $_GPC['station'];
			$date['time'] = date("Y-m-d H:i:s",time());

			//添加数据
			$result4 = pdo_insert("leju_staff",$date);
			//判断
			if(!empty($result4)){

				message("添加员工成功",$url1);
			}else{

				message("添加员工失败");
			}

		}else{

			//查找站点信息
			$staff_add = pdo_fetchall("SELECT * FROM ".tablename("leju_station"));
			//加载员工信息添加页面
			include $this->template("staff_add");
		}
		
	}else if($operation == "staff_edit"){

		//查找信息
		$staff_edit = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['id']));
		$statu = pdo_fetchall("SELECT * FROM ".tablename("leju_station"));
		//加载修改信息页面
		include $this->template("staff_edit",$statu);

	}else if($operation == "staff_update"){

		//修改员工信息
		if($_W['ispost']){
			
		
			//接收数据
			$upda['name'] = $_GPC['name'];
			$upda['phone'] = $_GPC['phone'];
			$upda['gender'] = $_GPC['sex'];
			$upda['title'] = $_GPC['title'];
			$upda['station'] = $_GPC['station'];
			$upda['pictures'] = $_GPC['pictures'];
			$upda['time'] = date("Y-m-d H:i:s",time());

			//执行修改
			$result6 = pdo_update("leju_staff",$upda,array("id"=>$_GPC['id']));

			//判断
			if(!empty($result6)){

				message("修改员工信息成功",$url1);

			}else{

				message("修改站点信息失败");
			}
		}
		
	}else if($operation == "staff_delete"){

		//删除站点信息
		//判断
		if(!empty($_GPC['id'])){

			//删除数据
			$result5 = pdo_delete("leju_staff",array("id"=>$_GPC['id']));
			
			if (!empty($result5)) {

			//查找分配的人员的信息
			$money = pdo_fetchall("SELECT * FROM ".tablename("leju_person")." WHERE broker_id=:id",array("id"=>$_GPC['id']));
			//修改信息
			foreach ($money as $ko => $mom) {
				
				/*****开始分配房源联系人*******/

				//查找客户对应的站点信息
				$station1 = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$mom['station']));
				//查找站点的员工信息
				$staff1 = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE station=:id  AND title=3 OR title=4  ORDER BY month limit 1",array("id"=>$mom['station']));
				//分配到此员工的身上
				$broke['broker_id'] = $staff1['id'];
				$broke['broker_status'] = 1;
				$broke['station'] = $station1['id'];
				$broke['broker_name'] = $staff1['name'];
				$broke['applation_time'] = date("Y-m-d H:i:s",time());
				//修改客户状态
				$enl = pdo_update("leju_person",$broke,array("id"=>$mom['id']));
				//修改员工的信息
				$matl = $staff1['month'] + 1;
				$en2 = pdo_update("leju_staff",array("month"=>$matl),array("id"=>$staff1['id']));

			/*******分配房源联系人结束**************/
			}
			
			    message('删除成功',$url1);
			}else{

				message('删除失败');
			}

		}else{

			message('加载删除页面失败');
		}



	}else if($operation == "staff_details"){

		//查找信息
		$staff_details = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['id']));
		$staff_details['sta'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$staff_details['station']));
		//查找员工负责的客户信息
		$pindex3 = max(1, intval($_GPC['page']));
		$psize3 = 100;
		$total3 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_client')." WHERE broker_id=:id AND broker_status=1",array("id"=>$_GPC['id']));
		$pager3 = pagination($total3, $pindex3, $psize3);
		
		//查取数据
		$staffo = pdo_fetchall("SELECT * FROM ".tablename('leju_client')." WHERE broker_id=:id AND broker_status=1 ORDER BY id ASC LIMIT ". ($pindex3 - 1) * $psize3 . ',' . $psize3,array("id"=>$_GPC['id']));
		foreach ($staffo as $keyo => $vala) {
			$staffo[$keyo]['time'] = date("Y-m-d H:i:s",$vala['time']);
		}

		//查取房源联系人
		$pindex4 = max(1, intval($_GPC['page']));
		$psize4 = 100;
		$total4 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_person')." WHERE broker_id=:id AND broker_status=1",array("id"=>$_GPC['id']));
		$pager4 = pagination($total4, $pindex4, $psize4);
		
		//查取数据
		$person =pdo_fetchall("SELECT * FROM ".tablename('leju_person')." WHERE broker_id=:id AND broker_status=1 ORDER BY id ASC LIMIT ". ($pindex4 - 1) * $psize4 . ',' . $psize4,array("id"=>$_GPC['id']));
		foreach ($person as $kui => $las) {
			$person[$kui]['time'] = date("Y-m-d H:i:s",$las['time']);
		}
		// echo "<prev>";
		// var_dump($staffo);
		// echo "<prev>";
		
		//加载员工详情页面
		include $this->template("staff_details",$staffo,$person);
	}



 ?>