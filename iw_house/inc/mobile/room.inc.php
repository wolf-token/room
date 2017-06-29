<?php 

	global $_W,$_GPC;
	$uid = $_W['fans']['uid'];
	$acid = $_W['fans']['uniacid'];
	//判断是否有信息
	$option = !empty($_GPC['op']) ? $_GPC['op'] : 'check';
	
	if($option == "check"){

		//判断是否为post
		if($_W['ispost']){
			
			//判断是否存在
			if(!empty($_GPC['type'])){

				$where.= " AND type=".$_GPC['type'];
			}
			if(!empty($_GPC['city'])){

				$where.= " AND city=".$_GPC['city'];
			}
			if(!empty($_GPC['pattern'])){

				$where.= " AND pattern=".$_GPC['pattern'];
			}
			if(!empty($_GPC['decorate'])){

				$where.= " AND decorate=".$_GPC['decorate'];
			}
			//查取数据 普通类型
			$room = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=0 AND pay_status!=3".$where." ORDER BY id ASC");
			//查找推荐房源
			$home = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE mold=1 AND pay_status!=3".$where." ORDER BY id ASC");
			//查找房源城市信息
			$city = pdo_fetchall("SELECT * FROM ".tablename('leju_station'));
			$type = pdo_fetchall("SELECT * FROM ".tablename('leju_pattern'));
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
			//查取广告位信息
			$tisment = pdo_fetchall("SELECT * FROM ".tablename("leju_tisment"));
			// var_dump($room);
			//加载页面
			include $this->template("room",$home,$city,$type,$tisment);

		}

	}


 ?>