<?php 

	global $_W,$_GPC;
	$uid = $_W['fans']['uid'];
	$opertion = $_GPC['op'] ? $_GPC['op'] : 'room';

	//加载控制器
	if($opertion == "room"){
		
		$where = "";
		//判断是否有加载地区条件
		if (!empty($_GPC['tid'] && isset($_GPC['tid']))) {
			
			$where = " AND liation=".$_GPC['tid'];
		}
		//查取数据 普通类型
		$room = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=0 AND pay_status!=3".$where." ORDER BY id ASC");
		//查找推荐房源
		$home = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=1 AND pay_status!=3".$where." ORDER BY id ASC");
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
		include $this->template("room",$home,$city,$type,$tisment,$area);

		}else if($opertion == "single"){

			//加载详情房屋页面
			//查找数据
			$single = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE id=:id",array("id"=>$_GPC['mid']));
			$single['pictures'] = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$single['photo_id']));
			// $single['price'] = intval($single['price']);
			$single['infomation'] = htmlspecialchars_decode($single['infomation']);
			$single['pat'] = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$single['pattern']));
			//加载页面
			include $this->template("single");
		}
		

 ?>