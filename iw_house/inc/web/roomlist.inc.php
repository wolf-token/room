<?php 

	global $_GPC, $_W;
	load()->func('tpl');
	$action = 'roomlist';
	$url = $this->createWebUrl($action, array('op' => 'list'));
	//判断是否为空
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
	if($operation == "list"){
	
	//设置条件
	$where = '';
	$params = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where.=' AND `name` LIKE :keyword OR `position` LIKE :keywords OR `area` LIKE :keywordl OR `time` LIKE :keyworda OR `price` LIKE :keywordp OR `average` LIKE :keywordt';
		$params[':keywords'] = "%{$_GPC['keyword']}%";
		$params[':keyword'] = "%{$_GPC['keyword']}%";
		$params[':keywordl'] = "%{$_GPC['keyword']}%";
		$params[':keyworda'] = "%{$_GPC['keyword']}%";
		$params[':keywordp'] = "%{$_GPC['keyword']}%";
		$params[':keywordt'] = "%{$_GPC['keyword']}%";
		
	}
		
	//查取数据
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_room')." WHERE pay_status!=3 ".$where);
	$pager = pagination($total, $pindex, $psize,$params);
	
	//查取房源数据
	$room_list = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE pay_status!=3 ".$where." ORDER BY id ASC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$params);
		
		//查取房东的信息
		foreach ($room_list as $keys => $values) {
			$room_list[$keys]['person'] = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array("id"=>$values['person_id']));
			$room_list[$keys]['img'] = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$values['photo_id']));
			$room_list[$keys]['cito'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$values['city']));
			$room_list[$keys]['pat'] = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$values['pattern']));
		}
		// var_dump($room_list);
		// die;
		//这个操作被定义用来呈现 管理中心导航菜单
		include $this->template('room_list');
		
	}else if ($operation == "edit") {
		
		//判断
		if(!empty($_GPC['id']) && !empty($_GPC['pictures']) && !empty($_GPC['person'])){

			//查找数据
			$room_edit = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE id=:id",array("id"=>$_GPC['id']));
			$cito= pdo_fetchall("SELECT * FROM ".tablename("leju_station"));
			$paty = pdo_fetchall("SELECT * FROM ".tablename("leju_pattern"));
			$pictures = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array('id'=>$_GPC['pictures']));
			$person = pdo_fetchall("SELECT * FROM ".tablename("leju_person"));
			$broker = pdo_fetchall("SELECT * FROM ".tablename("leju_users")." WHERE type=1");
			//查找客户信息
			$customer = pdo_fetchall("SELECT * FROM ".tablename("leju_client"));
			$pop = pdo_fetchall("SELECT * FROM ".tablename("leju_area"));
			//加载修改页面
			include $this->template('room_edit',$pictures,$person,$broker,$customer,$cito,$paty,$pop);
		}else{

			message('加载修改页面失败');
		}


	}else if($operation == "add"){
		if($_W['ispost']){
			// var_dump($_POST);
			// die;
			//判断信息是否完整
			if(empty($_GPC['name'])){

				message('房屋名称不能为空');
			}
			if(empty($_GPC['area'])){

				message('房屋面积不能为空');
			}
			if($_GPC['fus'] == ""){

				message('房型不能为空');
			}
			if(empty($_GPC['community'])){

				message('小区名称不能为空');
			}
			if(empty($_GPC['position'])){

				message('房屋位置不能为空');
			}
			if(empty($_GPC['city'])){

				message('房源归属城市不能为空');
			}
			if(empty($_GPC['price'])){

				message('房源总价不能为空');
			}
			if(empty($_GPC['average'])){

				message('房源平均价格不能为空');
			}
			if(empty($_GPC['imguqlc'])){

				message('房屋卧室图不能为空');
			}
			if(empty($_GPC['type'])){

				message('房屋户型图不能为空');
			}
			if(empty($_GPC['living'])){

				message('房屋客厅图不能为空');
			}
			if(empty($_GPC['traffic'])){

				message('房屋交通图不能为空');
			}
			if(empty($_GPC['other'])){

				message('房屋其它展示图不能为空');
			}
			
			//接收房源图片数据
			$data1['imguqcl'] = $_GPC['imguqlc'];
			$data1['type'] = $_GPC['type'];
			$data1['living'] = $_GPC['living'];
			$data1['traffic'] = $_GPC['traffic'];
			$data1['other'] = $_GPC['other'];
			$data1['time'] = date('Y-m-d H:i:s',time());
			$data1['uniacid'] = $_W['uniacid'];
			//添加数据
			$result = pdo_insert("leju_pictures",$data1);
			$result_id = pdo_insertid();

			//接收数据
			$data['photo_id'] = $result_id;
			$data['name'] = $_GPC['name'];
			$data['price'] = $_GPC['price'];
			$data['average'] = $_GPC['average'];
			$data['area'] = $_GPC['area'];
			$data['decorate'] = $_GPC['decorate'];
			$data['pattern'] = $_GPC['pattern'];
			$data['type'] = $_GPC['fus'];
			$data['community'] = $_GPC['community'];
			$data['position'] = $_GPC['position'];
			$data['city'] = $_GPC['city'];
			$data['mold'] = $_GPC['mold'];
			$data['status'] = $_GPC['status'];
			$data['person_id'] = $_GPC['person_id'];
			$data['time'] = date('Y-m-d H:i:s',time());
			$data['uniacid'] = $_W['uniacid'];
			$data['infomation'] = $_GPC['content'];
			$data['area'] = $_GPC['from'];

			//查找房主信息
			$house = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array("id"=>$_GPC['person_id']));
			$pusher = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$house['recommend_id']));
			$commission = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$pusher['commission']));
			//查取提佣的标准
			$norm = pdo_fetch("SELECT * FROM ".tablename("leju_assign")." WHERE id=:id",array("id"=>1));
			$data['recommend_id'] = $pusher['id'];
			//奖励钱数
			$money = $norm['norm']*$_GPC['area'];
			$mon = $money;
			$money = $money + $commission['money'];
			//添加奖励钱数
			$result5 = pdo_update("leju_commission",array("money"=>$money),array("id"=>$pusher['commission']));
			//添加到数据库
			$time1 = date("Y-m-d H:i:s",time());
			$result8 = pdo_insert("leju_record",array("user"=>$pusher['id'],"room"=>$mon,"time"=>$time1));
			//修改房主的拥有房源数量
			$mam = intval($house['math']) + 1;
			$enl = pdo_update("leju_person",array("math"=>$mam),array("id"=>$house['id']));
			//添加房源信息
			$result1 = pdo_insert("leju_room",$data);

			if(!empty($result) && !empty($result1) && !empty($result5)){

				message('添加房源成功',$url);
			}else{
				message('添加失败');
			}

		}else{

		//查取房东信息
		$room_add = pdo_fetchall('SELECT * FROM '.tablename("leju_person"));
		//查取城市信息
		$cit = pdo_fetchall("SELECT * FROM ".tablename("leju_station"));
		$pat = pdo_fetchall("SELECT * FROM ".tablename("leju_pattern"));
		//查取城市归属
		$from = pdo_fetchall("SELECT * FROM ".tablename("leju_area"));
		//查取推荐人信息
		foreach ($room_add as $key1 => $value1) {
			$room_add[$key1]['push'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$value1['recommend_id']));
		}
		$date = pdo_fetchall('SELECT * FROM '.tablename("leju_users")." WHERE type=1");
		//加载房源添加页面
		include $this->template('room_add',$date,$cit,$pat);

		}
		
	}else if($operation == "delete"){
		
		//判断
		if(!empty($_GPC['id']) && !empty($_GPC['pictures'])){

			//修改房东的房源数量
			$room = pdo_fetch("SELECT * FROM ".tablename('leju_room')."WHERE id=:id",array("id"=>$_GPC['id']));
			$lady = pdo_fetch("SELECT * FROM ".tablename('leju_person')."WHERE id=:id",array("id"=>$room['person_id']));
			$ma = $lady['math'] - 1;
			$ee = pdo_update("leju_person",array("math"=>$ma),array("id"=>$lady['id']));
			//删除数据
			$result2 = pdo_delete("leju_room",array("id"=>$_GPC['id']));
			$photo = pdo_fetch("SELECT * FROM ".tablename('leju_pictures')."WHERE id=:id",array("id"=>$_GPC['pictures']));
			
			$result3 = pdo_delete("leju_pictures",array("id"=>$_GPC['id']));
			
			if (!empty($result2) && !empty($result3)) {

			    message('删除成功',$url);
			}else{

				message('删除失败');
			}

		}else{

			message('加载详情页面失败');
		}
		

	}else if($operation == "details"){

		//判断
		if(!empty($_GPC['id']) && !empty($_GPC['pictures']) && !empty($_GPC['person'])){

			//查找数据
			$room_details = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE id=:id",array("id"=>$_GPC['id']));
			$room_details['cito'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$room_details['city']));
			$room_details['pat'] = pdo_fetch("SELECT * FROM ".tablename("leju_pattern")." WHERE id=:id",array("id"=>$room_details['pattern']));
			$pictures = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array('id'=>$_GPC['pictures']));
			$person = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array('id'=>$_GPC['person']));
			$room_details['staff'] = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array('id'=>$person['broker_id']));
			$room_details['station'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array('id'=>$person['station']));
			$choose = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$room_details['recommend_id']));
			if($room_details['buy_id'] != 0){

				$customer = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$room_details['buy_id']));
			}
			$too = pdo_fetch("SELECT * FROM ".tablename("leju_area")." WHERE id=:id",array("id"=>$room_details['liation']));
			//加载详情页面
			include $this->template('room_details',$pictures,$person,$choose,$customer);
		}else{

			message('加载详情页面失败');
		}
		


	}else if($operation == "update"){

		if($_W['ispost']){

	
			//接收数据
			//判断房源是否成交
			if($_GPC['assign'] != 0){

				if($_GPC['pay_status'] == 0){

					message("对不起，请您不要输入返现金额，您的房源尚未交易成功");
				}
				if($_GPC['buy_id'] == 0){

					message("对不起，请您不要输入返现金额，您的房源尚未交易成功");
				}

			}	
			if($_GPC['alone_one'] != 0 || $_GPC['alone_two'] != 0 || $_GPC['alone_three'] != 0){

				if($_GPC['pay_status'] == 0){

					message("对不起，请您不要输入返现金额，您的房源尚未交易成功");
				}
				if($_GPC['buy_id'] == 0){

					message("对不起，请您不要输入返现金额，您的房源尚未交易成功");
				}

			}	

			//判断是否一起输入分配金额
			if($_GPC['assign'] != 0 && ($_GPC['alone_one'] != 0 || $_GPC['alone_two'] != 0 || $_GPC['alone_three'] != 0)){

					message("对不起，请输入正确的分配佣金方式，否则后果自负");
				}

/*************************************房源成交分配佣金******************************/
	

		if($_GPC['pay_status'] == 3 && ($_GPC['assign'] != 0 || $_GPC['alone_one'] != 0 || $_GPC['alone_two'] != 0 || $_GPC['alone_three'] != 0)&& $_GPC['buy_id'] != 0){
			// echo "nice";
			
				//查取房源的分配金额数
				$allot = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE id=:id",array("id"=>$_GPC['id']));
				
				//判断是否已经分配过金额
				if($allot['money'] ){
					
				//判断是否修改过状态	
					if($allot['send_status'] == 0){
						//修改分配金额的状态
						$status = pdo_update("leju_room",array("send_status"=>1),array("id"=>$_GPC['id']));
					}
					if($_GPC['assign'] != 0 ){

						//接收分配的总金额数
						$sum = $_GPC['assign'];
					}
					if($_GPC['alone_one'] != 0 || $_GPC['alone_two'] != 0 || $_GPC['alone_three'] != 0){

						//接收分配的总金额数
						$sum = $_GPC['alone_one']+$_GPC['alone_two']+$_GPC['alone_three'];
					}
					//查取购房客户的信息
					$info = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['buy_id']));
					//查取推荐人的信息
					$referrer = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$info['recommon_id']));
					//查取推荐人的等级信息
					$grade =  pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE uid=:id",array("id"=>$referrer['uid']));
					//查取相应的推荐人的佣金
					$remuneration = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$referrer['commission']));
					
					//判断是否为自定义的分配金额
					if($_GPC['alone_one'] != 0 || $_GPC['alone_two'] != 0 || $_GPC['alone_three'] != 0){

						//修改分配佣金的数量
						$assign['assign_one'] = $_GPC['alone_one'];
						$assign['rank_one'] = $_GPC['alone_one'];
						$assign['assign_two'] = $_GPC['alone_two'];
						$assign['rank_two'] = $_GPC['alone_two'];
						$assign['assign_three'] = $_GPC['alone_three'];
						$assign['rank_three'] = $_GPC['alone_three'];

					}else{

						//查取执行标准的信息
						$assign =  pdo_fetch("SELECT * FROM ".tablename("leju_assign")." WHERE id=:id",array("id"=>1));
					}
				
				//分配时间
				$time = date("Y-m-d H:i:s");
				//判断推荐的等级
				//判断推荐人是否为一级经纪人
				if($grade['type'] == 1){

					//判断此一级经纪人是否分配过
					if($allot['send_status'] == 1){
						//已分配过 把原来分配的钱数减去
						$remuneration['money'] = $remuneration['money'] - $allot['rank_one'];
						
					}
					
					//判断输入的分配金总数是否够分配
					if($sum > $assign['rank_one']){

						//直接把钱转到相应的账户
						$residue = $sum - $assign['rank_one'];
						$update['rank_one'] = $assign['rank_one'];
						$update['residue_money'] = $residue;
						$change = $remuneration['money'] + $assign['rank_one'];
						//插入到数据库记录
						$a = pdo_insert("leju_record",array("myself"=>$assign['rank_one'],"time"=>$time,"user"=>$referrer['id']));
					}else{

						//分配金额不足时
						$change = $remuneration['money'] + $sum;
						$update['rank_one'] = $sum;
						$update['residue_money'] = 0;
						//插入到数据库记录
						$b = pdo_insert("leju_record",array("myself"=>$sum,"time"=>$time,"user"=>$referrer['id']));
					}	

					//修改佣金
					$gend = pdo_update("leju_commission",array("money"=>$change),array("id"=>$remuneration['id']));
					
				}

				//判断推荐人是否为二级经纪人
				if($grade['type'] == 2){
					
					//判断此二级经纪人是否分配过
					if($allot['send_status'] == 1){
						//已分配过 把原来分配的钱数减去
						$remuneration['money'] = $remuneration['money'] - $allot['rank_two'];
					}
					//判断分配金额是否足够分配
					if($sum >= $assign['rank_two']){

					//分配二级经纪人佣金后的余额
					$residue1 = $sum - $assign['rank_two'];
					$update['rank_two'] = $assign['rank_two'];
					//二级推荐人的佣金总和
					$change1 = $remuneration['money'] + $assign['rank_two'];
					//插入到数据库记录
					$c = pdo_insert("leju_record",array("myself"=>$assign['rank_two'],"time"=>$time,"user"=>$referrer['id']));
					}else{

						//金额不足
						$change1 = $remuneration['money'] + $sum;
						$update['rank_two'] = $sum;
						$residue1 = 0;
						//插入到数据库记录
						$d = pdo_insert("leju_record",array("myself"=>$sum,"time"=>$time,"user"=>$referrer['id']));
					}		
					
					//修改二级推荐人相应的佣金
					$gend2 = pdo_update("leju_commission",array("money"=>$change1),array("id"=>$remuneration['id']));

					/**
					*	一级经纪人的信息
					*/
					//查取二级经纪人的上级即一级经纪人的信息
					$superior = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$referrer['recommend_id']));		
					//判断是否有一级
					if(!empty($superior)){

					//查取一级经纪人的佣金
					$remuneration1 = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$superior['commission']));

					//判断此一级经纪人是否分配过
					if($allot['send_status'] == 1){
						//已分配过 把原来分配的钱数减去
						$remuneration1['money'] = $remuneration1['money'] - $allot['rank_one'];
					}
					//判断是否还有剩余的佣金
					if($residue1 >= $assign['assign_one']){

					//一级所有的佣金和
					$one = $assign['assign_one'] + $remuneration1['money'];					
					//剩余金额
					$update['residue_money'] = $residue1 - $assign['assign_one'];
					$update['rank_one'] = $assign['assign_one'];	
					//插入到数据库记录
					$e = pdo_insert("leju_record",array("rank_money"=>$assign['assign_one'],"time"=>$time,"user"=>$superior['id']));
					}else{

						//余额不足时
						//一级所有的佣金和
						$one = $residue1 + $remuneration1['money'];				
						//一级分配到的钱数
						$update['rank_one'] = $residue1;
						//插入到数据库记录
						$f = pdo_insert("leju_record",array("rank_money"=>$residue1,"time"=>$time,"user"=>$superior['id']));
					}
					
					//修改一级的佣金
					$gend1 = pdo_update("leju_commission",array("money"=>$one),array("id"=>$remuneration1['id']));
					}else{

					//剩余金额
					$update['residue_money'] = $residue1;	

					}
					
				}

				//判断推荐人是否为三级经纪人
				if($grade['type'] == 3){
					
					//判断此三级经纪人是否分配过
					if($allot['send_status'] == 1){
						//已分配过 把原来分配的钱数减去
						$remuneration['money'] = $remuneration['money'] - $allot['rank_three'];
					}
					//判断输入的佣金总数是否足够
					if($sum >= $assign['rank_three']){

					//分配三级经纪人佣金后的余额
					$residue2 = $sum - $assign['rank_three'];
					$update['rank_three'] = $assign['rank_three'];
					//三级推荐人的佣金总和
					$change2 = $remuneration['money'] + $assign['rank_three'];
					//插入到数据库记录
					$g = pdo_insert("leju_record",array("myself"=>$assign['rank_three'],"time"=>$time,"user"=>$referrer['id']));

					}else{

						//佣金不足时
						//分配三级经纪人佣金后的余额
						$residue2 = 0 ;
						$update['rank_three'] = $sum;
						//三级推荐人的佣金总和
						$change2 = $remuneration['money'] + $sum;
						//插入到数据库记录
						$h = pdo_insert("leju_record",array("myself"=>$sum,"time"=>$time,"user"=>$referrer['id']));
					}
					
					//修改三级推荐人相应的佣金
					$gend3 = pdo_update("leju_commission",array("money"=>$change2),array("id"=>$remuneration['id']));

					/**
					*	二级经纪人的信息
					*/
					//查取三级经纪人的上级即二级经纪人
					$superiors = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$referrer['recommend_id']));
					//判断是否拥有二级
					if(!empty($superiors)){

						//查取二级经纪人的佣金
						$remuneration2 = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$superiors['commission']));
						//判断此二级经纪人是否分配过
						if($allot['send_status'] == 1){
							//已分配过 把原来分配的钱数减去
							$remuneration2['money'] = $remuneration2['money'] - $allot['rank_two'];
						}
						//判断分配给三级经纪人后的佣金数值
						if($residue2 >= $assign['assign_two']){

						//求取二级经济人的佣金总和
						$two = $remuneration2['money'] + $assign['assign_two'];
						$update['rank_two'] = $assign['assign_two'];
						//分配完二级经纪人的佣金后的余额
						$difference = $residue2 - $assign['assign_two'];
						//插入到数据库记录
						$i = pdo_insert("leju_record",array("rank_money"=>$assign['assign_two'],"time"=>$time,"user"=>$superiors['id']));
						}else{
							//求取二级经济人的佣金总和
							$two = $remuneration2['money'] + $residue2;
							$update['rank_two'] = $residue2;
							//分配完二级经纪人的佣金后的余额
							$difference = 0;
							$o = pdo_insert("leju_record",array("rank_money"=>$residue2,"time"=>$time,"user"=>$superiors['id']));
						}
						
						//修改二级的佣金
						$check = pdo_update("leju_commission",array("money"=>$two),array("id"=>$remuneration2['id']));
						
						/**
						*	一级经纪人的信息
						*/
						//查取二级的上级信息即一级
						$rankone = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$superiors['recommend_id']));
						//判断二级是否有上级即一级
						if(!empty($rankone)){

							//查取一级经纪人的佣金信息
							$reward = pdo_fetch("SELECT * FROM ".tablename("leju_commission")." WHERE id=:id",array("id"=>$rankone['commission']));
							//判断此一级经纪人是否分配过
							if($allot['send_status'] == 1){
								//已分配过 把原来分配的钱数减去
								$reward['money'] = $reward['money'] - $allot['rank_one'];
							}
							//判断剩余的佣金数值
							if($difference >= $assign['assign_one']){

							//求取一级经纪人的佣金总和
							$tate = $reward['money'] + $assign['assign_one'];
							$update['rank_one'] = $assign['assign_one'];
							//分配完一级佣金后的余额
							$update['residue_money'] = $difference - $assign['assign_one'];
							//记录信息
							$u = pdo_insert("leju_record",array("rank_money"=>$assign['assign_one'],"time"=>$time,"user"=>$rankone['id']));
							}else{

								//剩余佣金不足时
								//求取一级经纪人的佣金总和
								$tate = $reward['money'] + $difference;
								$update['rank_one'] = $difference;
								//记录信息
								$p = pdo_insert("leju_record",array("rank_money"=>$difference,"time"=>$time,"user"=>$rankone['id']));
							}
							//修改一级经纪人的佣金数值
							$loend = pdo_update("leju_commission",array("money"=>$tate),array("id"=>$reward['id']));
							
						}else{

							//不存在一级
							//剩余的金额
							$update['residue_money'] = $difference;
						}

					}else{

						//不存在二级
						//剩余金额
						$update['residue_money'] = $residue2;
					}

					

				}

				}
				
			}

/**********************************分配佣金结束*************************************/
		
	
			//判断是否修改的房东信息
		
			if($_GPC['person'] != $_GPC['person_id']){

				$update['person_id'] = $_GPC['person_id'];
				// var_dump($update['person_id']);
				// die;
				//修改房东的房源数量
				$lady1 = pdo_fetch("SELECT * FROM ".tablename('leju_person')."WHERE id=:id",array("id"=>$_GPC['person']));
				$lady2 = pdo_fetch("SELECT * FROM ".tablename('leju_person')."WHERE id=:id",array("id"=>$_GPC['person_id']));
				$ma1 = $lady1['math'] - 1;
				$ma2 = $lady2['math'] + 1;
				$ee1 = pdo_update("leju_person",array("math"=>$ma1),array("id"=>$lady1['id']));
				$ee2 = pdo_update("leju_person",array("math"=>$ma2),array("id"=>$lady2['id']));

			}
			// var_dump($update['person_id']);
			// die;
			//修改房源信息
			$update['name'] = $_GPC['name'];
			$update['area'] = $_GPC['area'];
			$update['price'] = $_GPC['price'];
			$update['average'] = $_GPC['average'];
			$update['pattern'] = $_GPC['pattern'];
			$update['mold'] = $_GPC['mold'];
			$update['position'] = $_GPC['position'];
			$update['city'] = $_GPC['city'];
			$update['decorate'] = $_GPC['decorate'];
			$update['type'] = $_GPC['type'];
			$update['community'] = $_GPC['community'];
			$update['status'] = $_GPC['status'];
			$update['time'] = date("Y-m-d H:i:s",time());
			$update['uniacid'] = $_W['uniacid'];
			$update['infomation'] = $_GPC['content'];
			$update['pay_status'] = $_GPC['pay_status'];
			$update['pay_way'] = $_GPC['pay_way'];
			$update['recommend_id'] = $_GPC['recommend_id'];
			$update['money'] = $_GPC['assign'];
			$update['alone_one'] = $_GPC['alone_one'];
			$update['alone_two'] = $_GPC['alone_two'];
			$update['alone_three'] = $_GPC['alone_three'];
			$update['liation'] = $_GPC['liation'];
			if($_GPC['status'] == 1 && $allot['buy_id'] == 0){

				$update['buy_id'] = $_GPC['buy_id'];
			}
			//判断是否定房
			if($_GPC['status'] == 1 && $_GPC['buy_id'] != 0 && ($_GPC['pay_status'] !=2 || $_GPC['pay_status'] !=3)){

				$book = time();
				//修改客户定房的时间
				$home = pdo_update("leju_client",array("book_time"=>$book,"buy_status"=>2),array("id"=>$_GPC['buy_id']));

			}
			
			//修改客户的买房状态和数量
			if($_GPC['buy_id'] != 0 && $allot['buy_id'] != $_GPC['buy_id'] &&  $allot['buy_id'] == 0){
			
				$math = 1;
				//查取客户购买的房源数量
				$month = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['buy_id']));
				$math1 = $math + $month['math'];
				//判断付款的状态
				if($_GPC['pay_status'] == 2){

					$shit = 3;

				}
				if($_GPC['pay_status'] == 3){

					$shit = 4;
				}
				//修改数据
				$finish = pdo_update("leju_client",array("buy_status"=>$shit,"math"=>$math1),array("id"=>$_GPC['buy_id']));
			}
			
			//接收房源图片修改信息
			$picture['imguqcl'] = $_GPC['imguqcl'];
			$picture['type'] = $_GPC['pictures_type'];
			$picture['traffic'] = $_GPC['traffic'];
			$picture['living'] = $_GPC['living'];
			$picture['other'] = $_GPC['other'];
			$picture['time'] = date("Y-m-d H:i:s",time());
			$picture['uniacid'] = $_W['uniacid'];
			
			//执行修改
			$end = pdo_update("leju_room",$update,array("id"=>$_GPC['id']));
			$ending = pdo_update("leju_pictures",$picture,array("id"=>$_GPC['pictures_id']));


/**********************************房源成交查看此人是否达到升级标准开始******************************/
	

		if($_GPC['pay_status'] == 3 && $_GPC['assign'] != 0 && $_GPC['buy_id'] != 0){

			//查取购买人的信息
			$agent = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$_GPC['buy_id']));
			//查取经纪人信息	
			$economy_details = pdo_fetch("SELECT * FROM ".tablename('leju_users')." WHERE id=:id",array('id'=>$agent['recommon_id']));
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
			foreach ($users as $key2 => $value2) {
				// var_dump($value);
				$users[$key2]['rank'] = pdo_fetch("SELECT type FROM ".tablename('leju_rank')." WHERE id=:id",array('id'=>$value2['grade_id']));	
				//团队除本人之外推荐的客户数量
				$rooma = pdo_fetchall("SELECT * FROM".tablename("leju_client")."WHERE recommon_id=:id ",array("id"=>$value2['id']));
				$economy_details['mama'] += count($rooma);
				//团队除本人之外推荐的房源数量
				$roomal = pdo_fetchall("SELECT * FROM".tablename("leju_room")."WHERE recommend_id=:id ",array("id"=>$value2['id']));
				$economy_details['man'] += count($roomal);
				//查找团队中的经纪人推荐的客户购买房的总数
				$roome = pdo_fetchall("SELECT math FROM".tablename("leju_client")."WHERE recommon_id=:id AND buy_status=1",array("id"=>$value['id']));
				foreach ($roome as $k => $val0) {
					
					$account2 += intval($val0['math']);

				}

				$type1 += substr_count(($users[$key2]['rank']['type']), 1);
				$type2 += substr_count(($users[$key2]['rank']['type']), 2);
				$type3 += substr_count(($users[$key2]['rank']['type']), 3);
				//判断三级下属团队中是否有人升为二级
				if($economy_details['rank']['type'] == 3 && $users[$key2]['rank']['type']==2){

					//修改其下属的二级人员离开队伍
					$endoverq = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value2['id']));
				}
				//判断二级下属团队中是否有人升为一级
				if($economy_details['rank']['type'] == 2 && $users[$key2]['rank']['type']==1){

					//修改其下属的一级人员离开队伍
					$endoverq = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value2['id']));
				}
				//判断下属团队中是否有人升为二级
				if($economy_details['rank']['type'] == 2 && $users[$key2]['rank']['type']==2){

					//修改其下属的二级人员离开队伍
					$endoverq = pdo_update("leju_users",array("recommend_id"=>0),array("id"=>$value2['id']));
				}
				//判断下属是否有人升级到一级
				if($economy_details['rank']['type'] == 1 && $users[$key2]['rank']['type']==1){

					//修改其下属永远离开队伍独立
					$endovered = pdo_update("leju_users",array("recommend_id"=>0,"record_recommon"=>0),array("id"=>$value2['id']));
				}

			}	

			$economy_details['mama'] = intval($economy_details['mama']) + intval($economy_details['mam']);
			$economy_details['man'] = intval($economy_details['man']) + intval($economy_details['room']);
			//判断经纪人推荐的人数是否达到标准的升级要求
			//判断经纪人是不是三等级
			if($economy_details['rank']['type'] == 3){
			
				//查取此经纪人推荐的房源数量
				$monthe = pdo_fetchall("SELECT * FROM ".tablename('leju_client')."WHERE recommon_id=:id",array("id"=>$economy_details['id']));
				// var_dump($month);
				foreach ($monthe as $key3 => $value3) {
					
					$matha += $value3['math'];
				}
				//查取三级升二级的条件
				$criteria = pdo_fetch("SELECT * FROM ".tablename("leju_scale_two")." WHERE id=:id",array("id"=>1));
				//三级升二级
				if($type3 >= $criteria['person'] || $matha >= $criteria['room']){

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
				foreach ($minion as $kye => $vo) {
					
					$minion1[$kye]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$vo['grade_id']));
					//判断是否有二级
					if($minion1[$kye]['rank']['type'] == 2){

						//修改他的推荐id
						$finishel = pdo_update("leju_users",array("recommend_id"=>$vo['record_recommon']),array("id"=>$vo['id']));
					}

				}
			}

		/**
		*		结束
		*/



}


/*********************************判断是否达到升级标准结束********************************************/
	
			//判断
			if(!empty($end) || !empty($ending) || !empty($endover)){

				message('修改成功',$url);
			}else{

				message('修改失败');
			}
			
		  
		}
	}else if($operation == "contact"){

			//设置条件
		$where1 = '';
		$params1 = array();
		//判断是否有搜索
		if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

			$where1.=' WHERE `name` LIKE :keyword OR `cell` LIKE :keywords OR `time` LIKE :keywordl';
			$params1[':keywords'] = "%{$_GPC['keyword']}%";
			$params1[':keyword'] = "%{$_GPC['keyword']}%";
			$params1[':keywordl'] = "%{$_GPC['keyword']}%";
			
		}
		if(isset($_GPC['staff']) && !empty($_GPC['staff'])){

				$where1.=' WHERE broker_id=:id AND broker_status=1';
				$params1[':id'] = $_GPC['staff'];
				$sta = $_GPC['staff'];
		}	
		//查取数据
		$pindex1 = max(1, intval($_GPC['page']));
		$psize1 = 20;
		$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_person').$where1);
		$pager1 = pagination($total1, $pindex1, $psize1,$params1);
		
		//查取房源联系人信息
		$contact_list = pdo_fetchall("SELECT * FROM ".tablename('leju_person').$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		foreach ($contact_list as $key => $value) {
			$contact_list[$key]['time'] = date("Y-m-d H:i:s",$value['time']);
			$contact_list[$key]['users'] = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$value['recommend_id']));
			$contact_list[$key]['rank'] = pdo_fetch("SELECT * FROM ".tablename("leju_rank")." WHERE id=:id",array("id"=>$contact_list[$key]['users']['grade_id']));
			$contact_list[$key]['stat'] =  pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$value['station']));
		}
		//查找店员信息
		$staffo =  pdo_fetchall("SELECT * FROM ".tablename('leju_staff'));
		//加载房主联系人信息
		include $this->template('contact_list',$staffo,$sta);

	}else if($operation == "contactte"){

		//删除房主信息
		if(!empty($_GPC['id'])){
			
			//查找房主信息
			$hate = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array("id"=>$_GPC['id']));
			$tol = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$hate['broker_id']));
			//修改分配员工的信息
			$count = $tol['month'] - 1;
			//修改信息
			$over = pdo_update("leju_staff",array("month"=>$count),array("id"=>$tol['id']));
			
			$result4 = pdo_delete("leju_person",array("id"=>$_GPC['id']));

			//判断是否删除成功
			if(!empty($result4)){

				message("删除房主信息成功",$url);
			}else{

				message("删除失败");
			}
		}
	}else if($operation == "contact_edit"){

		
		//查取房东信息
		$contact_edit = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array("id"=>$_GPC['id']));
		$dato = pdo_fetchall("SELECT * FROM ".tablename("leju_users"));
		//查找站点信息
		$staff = pdo_fetchall("SELECT * FROM ".tablename("leju_staff"));
		foreach ($staff as $ky => $ca) {
			
			$staff[$ky]['stat'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$ca['station']));
		}
		
		//加载修改信息页面
		include $this->template("contact_edit",$dato,$staff);

	}else if($operation == "contact_update"){

		//判断
		if($_W['ispost']){

			$come['name'] = $_GPC['person_name'];
			$come['cell'] = $_GPC['person_cell'];
			
			//判断是否改变推荐人
			if($_GPC['broker_id'] != $_GPC['contact_id']){

				//修改房源的推荐人的id
				$res = pdo_update("leju_room",array("recommend_id"=>$_GPC['broker_id']),array("person_id"=>$_GPC['person_id']));
				$come['recommend_id'] = $_GPC['broker_id'];
			}
			//判断是否修改负责人信息
			if($_GPC['staff_id'] != $_GPC['staff']){

				//修改员工的分配数量
				$staf = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['staff_id']));
				$shu = $staf['month'] - 1;
				$e = pdo_update("leju_staff",array("month"=>$shu),array("id"=>$staf['id']));
				//查找新分配的站点信息
				$staf1 = pdo_fetch("SELECT * FROM ".tablename("leju_staff")." WHERE id=:id",array("id"=>$_GPC['staff']));
				$shu1 = $staf1['month'] + 1;
				$e1 = pdo_update("leju_staff",array("month"=>$shu1),array("id"=>$staf1['id']));

				$sto = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$staf1['station']));
				//修改客户的分配员工信息
				$come['broker_id'] = $_GPC['staff'];
				$come['station'] = $sto['id'];
				$come['broker_name'] = $staf1['name'];
				$come['applation_time'] = date("Y-m-d H:i:s",time());


			}
			//执行修改
			$result6 = pdo_update("leju_person",$come,array("id"=>$_GPC['person_id']));

			if(!empty($result6)){

				message('修改成功',$url);
			}else{

				message('修改失败');
			}
		}
	}else if ($operation == "recle") {
		
		//设置条件
	$where1 = '';
	$params1 = array();
	//判断是否有搜索
	if(isset($_GPC['keyword']) && !empty($_GPC['keyword'])){

		$where1.=' AND `name` LIKE :keyword OR `position` LIKE :keywords OR `area` LIKE :keywordl OR `time` LIKE :keyworda OR `price` LIKE :keywordp OR `average` LIKE :keywordt';
		$params1[':keywords'] = "%{$_GPC['keyword']}%";
		$params1[':keyword'] = "%{$_GPC['keyword']}%";
		$params1[':keywordl'] = "%{$_GPC['keyword']}%";
		$params1[':keyworda'] = "%{$_GPC['keyword']}%";
		$params1[':keywordp'] = "%{$_GPC['keyword']}%";
		$params1[':keywordt'] = "%{$_GPC['keyword']}%";
		
	}
		
	//查取数据
	$pindex1 = max(1, intval($_GPC['page']));
	$psize1 = 20;
	$total1 = pdo_fetchcolumn('SELECT COUNT(*) FROM '. tablename('leju_room')." WHERE pay_status=3 ".$where1);
	$pager1 = pagination($total1, $pindex1, $psize1,$params1);
	

	$room_recle = pdo_fetchall("SELECT * FROM ".tablename('leju_room')." WHERE pay_status=3 ".$where1." ORDER BY id ASC LIMIT ". ($pindex1 - 1) * $psize1 . ',' . $psize1,$params1);
		
		//查取房东的信息
		foreach ($room_recle as $k => $val) {
			$room_recle[$k]['person'] = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array("id"=>$val['person_id']));
			$room_recle[$k]['img'] = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array("id"=>$val['photo_id']));
			$room_recle[$k]['stat'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$val['city']));
		}
		// var_dump($room_list);
		// die;
		//这个操作被定义用来呈现 管理中心导航菜单
		include $this->template('room_recle');

	}else if($operation == "recle_delete"){
		
		//判断
		if(!empty($_GPC['pid']) && !empty($_GPC['pictures_id'])){

			//删除数据
			$endov = pdo_delete("leju_room",array("id"=>$_GPC['pid']));
			$photo = pdo_fetch("SELECT * FROM ".tablename('leju_pictures')."WHERE id=:id",array("id"=>$_GPC['pictures_id']));
			// //删除本地的图片
			// unlink($photo['imguqlc']);
			// unlink($photo['type']);
			// unlink($photo['living']);
			// unlink($photo['traffic']);
			// unlink($photo['other']);
			$endov1 = pdo_delete("leju_pictures",array("id"=>$_GPC['pid']));
			
			if (!empty($endov) && !empty($endov1)) {

			    message('删除成功',$url);
			}else{

				message('删除失败');
			}

		}else{

			message('加载详情页面失败');
		}


	}else if($operation == "recle_details"){

		//判断
		if(!empty($_GPC['id']) && !empty($_GPC['pictures']) && !empty($_GPC['person'])){

			//查找数据
			$recle_details = pdo_fetch("SELECT * FROM ".tablename("leju_room")." WHERE id=:id",array("id"=>$_GPC['id']));
			$pictures1 = pdo_fetch("SELECT * FROM ".tablename("leju_pictures")." WHERE id=:id",array('id'=>$_GPC['pictures']));
			$person1 = pdo_fetch("SELECT * FROM ".tablename("leju_person")." WHERE id=:id",array('id'=>$_GPC['person']));
			$choose1 = pdo_fetch("SELECT * FROM ".tablename("leju_users")." WHERE id=:id",array("id"=>$recle_details['recommend_id']));
			$recle_details['station'] = pdo_fetch("SELECT * FROM ".tablename("leju_station")." WHERE id=:id",array("id"=>$recle_details['city']));
			if($recle_details['buy_id'] != 0){

				$customer1 = pdo_fetch("SELECT * FROM ".tablename("leju_client")." WHERE id=:id",array("id"=>$recle_details['buy_id']));
			}
			
			//加载详情页面
			include $this->template('recle_details',$pictures1,$person1,$choose1,$customer1);
		}else{

			message('加载详情页面失败');
		}
		
	}




 ?>