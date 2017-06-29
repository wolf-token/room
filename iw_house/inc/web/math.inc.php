<?php


global $_GPC, $_W;
$action = 'math';
$url = $this->createWebUrl($action, array('op' => 'list'));
$url1 = $this->createWebUrl($action, array('op' => 'tisment'));

//判断是否为空
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';

if($operation == "list"){

	//查取信息
	$math = pdo_fetchall("SELECT * FROM ".tablename("leju_math"));
	//加载房源格局页面
	include $this->template("math");

}else if($operation == "add"){

	//加载添加页面
	if($_W['ispost']){
		//查找数据
		$end = pdo_fetchall("SELECT * FROM ".tablename("leju_math"));
		if(!empty($end)){

				message("提现规则已存在不能再添加");
		}else{
			//接收数据
			$data['person'] = $_GPC['person'];
			$data['math'] = $_GPC['math'];
			$data['time'] = date("Y-m-d H:i:s",time());

			//添加数据
			$result = pdo_insert("leju_math",$data);
			if(!empty($result)){

				//加载页面
				message("添加成功",$url);
			}else{

				message("添加失败");
			}
		}
	}else{

		//加载页面
		include $this->template("math_add");
	}

}else if($operation == "edit"){

		//加载修改页面
		$math_edit = pdo_fetch("SELECT * FROM ".tablename("leju_math")." WHERE id=:id",array("id"=>$_GPC['id']));

		include $this->template("math_edit");

}else if($operation == "delete"){

	//删除信息
	$result2 = pdo_delete("leju_math",array("id"=>$_GPC['id']));
		if(!empty($result2)){

			//加载页面
			message("删除成功",$url);

		}else{

			message("删除失败");
		}


}else if($operation == "update"){

	if($_W['ispost']){

		//接收数据
		$date['person'] = $_GPC['person'];
		$date['client'] = $_GPC['client'];
		$date['time'] = date("Y-m-d H:i:s",time());

		//修改数据
		$result1 = pdo_update("leju_math",$date,array("id"=>$_GPC['id']));

		//判断
		if(!empty($result1)){

		//加载页面
		message("修改成功",$url);

		}else{

			message("修改失败");
		}
		
	}


}else if($operation == "tisment"){

	//查取信息
	$tisment = pdo_fetchall("SELECT * FROM ".tablename("leju_tisment"));
	//加载页面
	include $this->template("tisment");


}else if($operation == "tisment_add"){

	if($_W['ispost']){

		//查取信息
		$check = pdo_fetchall("SELECT * FROM ".tablename("leju_tisment"));
		if(empty($check)){

			//接收信息
			$tisment_add['info'] = $_GPC['info'];
			$tisment_add['status'] = $_GPC['status'];
			$tisment_add['cell'] = $_GPC['cell'];
			$tisment_add['time'] = date("Y-m-d H:i:s",time());
			
			//添加到数据库
			$endover = pdo_insert("leju_tisment",$tisment_add);

			//判断
			if($endover){

				message("添加广告位成功",$url1);
			}else{

				message("添加失败");
			}

		}else{

			message("广告位已存在",$url1);
		}
		
	}
	//加载页面
	include $this->template("tisment_add");

}else if($operation == "tisment_update"){

	//判断是否为post
	if($_W['ispost']){

		//接收信息
		$tisment_update['info'] = $_GPC['info'];
		$tisment_update['cell'] = $_GPC['cell'];
		$tisment_update['status'] = $_GPC['status'];
		$tisment_update['time'] = date("Y-m-d H:i:s",time());
		
		//添加到数据库
		$endover1 = pdo_update("leju_tisment",$tisment_update,array("id"=>$_GPC['id']));

		//判断
		if($endover1){

			message("修改广告位成功",$url1);
		}else{

			message("修改失败");
		}

	}

}else if($operation == "tisment_edit"){

	//查取信息
	$tisment_edit = pdo_fetch("SELECT * FROM ".tablename("leju_tisment")." WHERE id=:id",array("id"=>$_GPC['id']));
	//加载页面
	include $this->template("tisment_edit");

}

?>