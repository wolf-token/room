<?php


global $_GPC, $_W;
$action = 'pattern';
$url = $this->createWebUrl($action, array('op' => 'list'));
$url1 = $this->createWebUrl($action, array('op' => 'area'));

//判断是否为空
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

if($operation == "list"){

		//设置条件
	$where = '';
	$params = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where.=' WHERE `name` LIKE :keyword OR `time` LIKE :keywords';
		$params[':keywords'] = "%{$_GPC['keyword']}%";
		$params[':keyword'] = "%{$_GPC['keyword']}%";

	}

	// //查取数据
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('leju_pattern').$where);
	$pager = pagination($total, $pindex, $psize,$params);
	//查取信息
	$pattern = pdo_fetchall("SELECT * FROM ".tablename('leju_pattern').$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
	//加载房源格局页面
	include $this->template("pattern");

}else if($operation == "add"){

	//加载添加页面
	if($_W['ispost']){

		//接收数据
		$data['name'] = $_GPC['name'];
		$data['time'] = date("Y-m-d H:i:s",time());

		//添加数据
		$result = pdo_insert("leju_pattern",$data);
		if(!empty($result)){

			//加载页面
			message("添加成功",$url);
		}else{

			message("添加失败");
		}

	}else{

		//加载页面
		include $this->template("pattern_add");
	}

}else if($operation == "edit"){

		//加载修改页面
		$pattern_edit = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$_GPC['id']));

		include $this->template("pattern_edit");

}else if($operation == "delete"){

	//删除信息
	$result2 = pdo_delete("leju_pattern",array("id"=>$_GPC['id']));
		if(!empty($result2)){

			//加载页面
			message("删除成功",$url);
		}else{

			message("删除失败");
		}


}else if($operation == "update"){

	if($_W['ispost']){

		//接收数据
		$date['name'] = $_GPC['name'];
		$date['time'] = date("Y-m-d H:i:s",time());

		//修改数据
		$result1 = pdo_update("leju_pattern",$date,array("id"=>$_GPC['id']));

		//判断
		if(!empty($result1)){

		//加载页面
		message("修改成功",$url);

		}else{

			message("修改失败");
		}

	}


}else if($operation == "area"){
	$where1 = '';
	$params1 = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where1.=' WHERE `name` LIKE :keyword OR `time` LIKE :keywords';
		$params1[':keywords'] = "%{$_GPC['keyword']}%";
		$params1[':keyword'] = "%{$_GPC['keyword']}%";

	}

	$pindex1 = max(1, intval($_GPC['page']));
	$psize1 = 20;
	$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('leju_area').$where1);
	$pager1 = pagination($total1, $pindex1, $psize1,$params1);
	//查取信息
	$area = pdo_fetchall("SELECT * FROM ".tablename('leju_area').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
	
	//加载归属地分类页面
	include $this->template("area");

}else if($operation == "area_add"){

	//加载添加归属地分类页面
	include $this->template("area_add");

}else if($operation == "area_post"){

	//接收数据
	if ($_W['ispost']) {
		
		$post['name'] = $_GPC['name'];
		$post['time'] = date("Y-m-d H:i:s",time());

		//添加数据
		$res = pdo_insert("leju_area",$post);

		if (!empty($res)) {
			
			message("添加成功",$url1);
		}else{
			message("添加失败");
		}
	}

}else if ($operation == "area_delete") {

	//接收数据
	$id = $_GPC['id'];
	if (!empty($id)) {
		
		//执行删除
		$resl = pdo_delete("leju_area",array("id"=>$id));

		if (!empty($resl)) {
			
			message("删除成功",$url1);
		}else{

			message("删除失败");
		}
	}
}else if ($operation == "area_edit") {
	$uid = $_GPC['id'];
	//查取数据
	$area_edit = pdo_fetch("SELECT * FROM ".tablename('leju_area')." WHERE id = :uid", array(':uid' => $uid));
	// var_dump($area_edit);
	//加载页面
	include $this->template("area_edit");
}else if($operation == "area_update"){

	//接收数据
	if ($_W['ispost']) {
		$piod = $_GPC['id'];
		$area_update['name'] = $_GPC['name'];
		$area_update['time'] = date("Y-m-d H:i:s",time());

		//添加数据
		$res1 = pdo_update("leju_area",$area_update,array("id"=>$piod ));

		if (!empty($res1)) {
			
			message("修改成功",$url1);
		}else{
			message("修改失败");
		}
	}

}

?>