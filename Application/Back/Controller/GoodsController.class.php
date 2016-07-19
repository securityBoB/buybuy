<?php
namespace  Back\Controller;
use Think\Controller;
use Think\Upload;
use Think\Image;
use Org\Util\Page;

class GoodsController extends Controller
{   
	
	
	public function testAction()
	{   
		
	}  
	public function listAction()
	{    
		
// 		dump($_POST);
		$cond['is_delete'] =0;
		if(($filterName = I('get.filter_name','','trim'))!== '')
		{
			$cond['name']= ['like',"$filterName%"];
			$this->assign('filter_name',$filterName);
			$search['filter_name']=$filterName;
		}
		if(($filterStatus = I('get.filter_status','','trim') )!== '')
		{
			$cond['status']= $filterStatus;
			$this->assign('filter_status',$filterStatus);
			$search['filter_status']=$filterStatus;
		}
		if(($filterPriceMin = I('get.filter_price_min','','trim')) !== '')
		{   
			
			if(($filterPriceMax = I('get.filter_price_max','','trim')) === '')
			{   
				if(is_numeric($filterPriceMin))
				{
					$cond['price'] =array('gt',$filterPriceMin);
					$this->assign('filter_price_min',$filterPriceMin);
					$search['filter_price_min']=$filterPriceMin;
				}
			}
			else
			 {  
			 	if(is_numeric($filterPriceMin)&&is_numeric($filterPriceMax))
			 	{
				 	$cond['price'] =array('between',"$filterPriceMin,$filterPriceMax");
				 	$this->assign('filter_price_min',$filterPriceMin);
				 	$this->assign('filter_price_max',$filterPriceMax);
				 	$search['filter_price_min']=$filterPriceMin;
				 	$search['filter_price_max']=$filterPriceMax;
			 	}
			 }
		}else 
		{
			if(($filterPriceMax = I('get.filter_price_max','','trim')) !== '')
			{  
				if(is_numeric($filterPriceMax))
				{
					$cond['price'] =array('lt',$filterPriceMax);
					$this->assign('filter_price_max',$filterPriceMax);
					$search['filter_price_max']=$filterPriceMax;
				}
			}
		}
		
		if (isset($_GET['order_field']))  {
			$order = I('get.order_field').' '.I('get.order_method');
// 			dump($order);
		} else {
			$order = "sort_number";
		}
	   
// 		dump($cond);
// 		die;
		$m_goods = M('goods');
		$page = I('get.p',1);
        $pagesize = CC('back_pagesize');
		$goods_list = $m_goods
		                     ->where($cond)
		                     ->order($order)
		                     ->page($page,$pagesize)
// 		                     ->fetchSQL()
		                     ->select();
// 		dump($goods_list);
		$this->assign('search',$search);//
// 		dump();
		$totle=$m_goods->where($cond)->count();
		$subPage =new Page($totle,$pagesize);
// 		dump($search);
		$subPage->parameter=$search;
		
		$show = $subPage->show();// 分页显示输出
		$this->assign('page',$show);// 
	
        $this->assign('goods_list',$goods_list);
		$this->display();
		
	}
	public function addAction()
	{   
		
		
		
		
        $m_goods = M('goods');
        if(IS_POST)
        {   
        	$m_goods->create();
        	$goods_id = $m_goods->add();
        	if($goods_id)
        	{   
        		$m_goodsImage = M('GoodsImage');
        		$image_list = $_POST['gallery'];
        		foreach ($image_list as $image)
        		{
        			$image['goods_id'] = $goods_id;
        			$m_goodsImage->add($image);
        		}
        		//相册处理成功
        		//属性的处理
        		//更新商品表中的goods_type_id
        		$m_goods->goods_type_id = I('post.goods_type_id');
        		
        		$m_goods->save();
        		
        		
        		//得到属性列表  
        		
        		$attr_list = I('post.attribute');
        		
        		$is_option_list = I('post.is_option');
        		//更新商品与属性关联表
        		$m_ga = M('goods_attribute');
        		$m_a = M('attribute');
        		$m_gav = M('goods_attribute_value');
        		$m_at = M('attribute_type');
        		foreach ($attr_list as $attr_id=>$val)
        		{  
        			if(in_array($attr_id,$is_option_list))
        			{
        				 $is_o = 1;
        			}else 
        			{
        				 $is_o = 0;
        			}
        			$data_ga= array(
        				'goods_id' => $goods_id,
        				'attribute_id'=>$attr_id,
        				'option'=>$is_o
        			);
        		    
        		   $goods_attribute_id = $m_ga->add($data_ga);
        		          $r = $m_a
        		                            ->field('attribute_type_id')
        		                            ->where(['attribute_id'=>$attr_id])
        		                            ->find(); 
        		                         
        		          $attribute_type_id=$r['attribute_type_id'];
        		          $ro = $m_at
        		                               ->field('title')
        		                               ->where(['attribute_type_id'=>$attribute_type_id])
        		                               ->find();
        		   $attribute_type_title = $ro['title'];
        		   if(in_array($attribute_type_title,['select','select_multiple']))
        		   {      
        		   	     if(is_array($val))
        		   	     {
	        		   	      foreach ($val as $v )
	        		   	      {
				        		   $data_gav = array(
				        		   	'goods_attribute_id'=>$goods_attribute_id,
				        		   	'attribute_value_id'=>$v,
				        		   	'value'=>'',
				        		    );
				        		   $m_gav->add($data_gav);
	        		   	      }
        		   	     }else
        		   	     {
        		   	     	        $data_gav = array(
        		   	     			'goods_attribute_id'=>$goods_attribute_id,
        		   	     			'attribute_value_id'=>$val,
        		   	     			'value'=>'',);
        		   	     	        $m_gav->add($data_gav);
        		   	     }
        		   }else 
        		   {
        		   	        $data_gav = array(
        		   			'goods_attribute_id'=>$goods_attribute_id,
        		   			'value'=>$val,
        		   	       );
        		   	       $m_gav->add($data_gav);
        		   }
        		}
        		
        		$this->redirect('Back/Goods/list');
        	}else
        	{
        		$this->error('添加失败',U('Back/Goods/add'),2);
        	}
        	
        }else 
        {   
        	$this->assign('tax_list',M('tax')->select());
        	$this->assign('stock_status_list',M('stock_status')->select());
            $this->assign('length_unit_list',M('length_unit')->select());
            $this->assign('weight_unit_list',M('weight_unit')->select());
            $this->assign('goods_type_list',M('goods_type')->select());
            $this->assign('brand_list',M('brand')->select());
            $this->assign('category_list',D('category')->getTree());
        	$this->display();
        }
		
	}
	public function imageUploadAction()
	{
		$upload = new Upload();
		$upload->exts=['jpeg','png','jpg'];
		$upload->rootPath='./Upload/Goods/Image/';
		$info = $upload->uploadOne($_FILES['image_ajax']);
		if($info)
		{   
			$img = new Image();
			$filePath = $upload->rootPath.$info['savepath'].$info['savename'];
            $image = new Image();
            $image->open($filePath);
            $thumb_root_Path = './Public/Thumb/goods/';
            $thumb_sub_Path  =date('Y-m-d',time());
            $file_name = '340x340thumb'.$info['savename'].'';
            $fileTotle = $thumb_root_Path.$thumb_sub_Path.'/'.$file_name;
            if(!is_dir($thumb_root_Path.$thumb_sub_Path))
            {
            	mkdir($thumb_root_Path.$thumb_sub_Path,0755,ture);
            }
            $image->thumb(40,40,Image::IMAGE_THUMB_FILLED )->save($fileTotle);
			$this->ajaxReturn(['error'=>0,'image'=>$info['savepath'].$info['savename'],'thumb'=>$thumb_sub_Path.'/'.$file_name]);
		}else 
		{
			$this->ajaxReturn(['error'=>1]);
		}
		
	}
	public function  ajaxAttrAction()
	{   
		$goods_type_id = I('get.goods_type_id');
		$m_attr = M('attribute');
		$attr_list  = $m_attr
		                    ->field('a.*,at.title as attr_type_title')
		                    ->alias('a')
		                    ->join('left join __ATTRIBUTE_TYPE__  as at using(attribute_type_id)')
		                    ->where(['goods_type_id'=>$goods_type_id])
// 		                    ->fetchSQL()
		                    ->select();
		$m_attr_val = M('attribute_value');
		
// 		$attr_value_lsit = $m_attr_val->select();
// 		dump($attr_list);
// 		dump($attr_value_lsit);
		
		foreach ($attr_list as   &$attr)
		{
			if(in_array($attr['attr_type_title'],['select','select_multiple']))
			{   
				
				$attr['option_list'] = $m_attr_val
				                                 ->where(['attribute_id'=>$attr['attribute_id']])
// 				                                 ->fetchSQL()
				                                 ->select();
			}
		}
		$this->assign('attr_list',$attr_list);
// 		dump($attr_list);
// 		die;
		$this->display();
		
		
	}
	
	
	public function galleryUpLoadAction()
	{   
		//大图 680*680
		//中图340*340
		//小图60*60
		$upload = new Upload();
		$upload->exts=['jpeg','png','jpg'];
		$upload->rootPath='./Upload/Goods/Image/';
		$info = $upload->uploadOne($_FILES['gallery']);
		if($info)
		{
			$img = new Image();
			$filePath = $upload->rootPath.$info['savepath'].$info['savename'];
			$image = new Image();
			$image->open($filePath);
			$thumb_root_Path = './Public/Gallery/';
			$thumb_sub_Path  =date('Y-m-d',time());
			if(!is_dir($thumb_root_Path.$thumb_sub_Path))
			{
				mkdir($thumb_root_Path.$thumb_sub_Path,0755,ture);
			}
			
			
			$gallery_big = '600x600gallery_big'.$info['savename'];
			$gallery_medium = '340x340gallery_medium'.$info['savename'];
			$gallery_small = '60x60gallery_small'.$info['savename'];
			$gallery_big_totle = $thumb_root_Path.$thumb_sub_Path.'/'.$gallery_big;
			$gallery_medium_totle = $thumb_root_Path.$thumb_sub_Path.'/'.$gallery_medium;
			$gallery_small_totle = $thumb_root_Path.$thumb_sub_Path.'/'.$gallery_small;
			$image->thumb(600,600)->save($gallery_big_totle);
			$image->thumb(340,340)->save($gallery_medium_totle);
			$image->thumb(60,60)->save($gallery_small_totle);
			
			$this->ajaxReturn([
					          'error'=>0,
					          'image'=>$info['savepath'].$info['savename'],
					          'gallery_small'=>$thumb_sub_Path.'/'.$gallery_small,
					          'gallery_big'=>$thumb_sub_Path.'/'.$gallery_big,
					          'gallery_medium'=>$thumb_sub_Path.'/'.$gallery_medium,
					         
		
				]);
		}else
		{
			$this->ajaxReturn(['error'=>1]);
		}
	}
	
	public function optionAction($goods_id)
	{   
		if(IS_POST)
		{
// 			dump($_POST);
// 			die;
           $opt_list = I('post.option');
           $m_gav = M('goods_attribute_value');
           foreach ($opt_list as $goods_attr_id =>  $opt)
           {
           	      foreach ($opt as $attr_id => $value_list)
           	      {  
           	      	    $data_gav['quantity'] = $value_list['quantity'];
           	      	    $data_gav['price_operate'] = $value_list['price_operate'];
           	      	    $data_gav['price_drift'] = $value_list['price_drift'];
           	      	    $data_gav['status'] = isset($value_list['status'])? $value_list['status']:0;
           	      	    
           	      	    $res = $m_gav
           	      	              ->where(['goods_attribute_id'=>$goods_attr_id,'attribute_value_id'=>$attr_id])
           	      	              ->save($data_gav);
           	      	    if(!$res)
           	      	    {
           	      	    	 $this->error('更新失败',U('Back/Goods/option',['$goods_id'=>$goods_id]));
           	      	    }
           	      }
           }
          $this->redirect('Back/Goods/list',[],0,'更新成功');
           
		}
		else 
		{
		
		$m_ga = M('goods_attribute');
		$attr_info =  $m_ga
						->field('a.title as attr_title , a.attribute_id as attr_id, 
								at.title as attr_type_title , 
								ga.option as is_option ,ga.goods_attribute_id as goods_attr_id ,
								gav.value as gav_value,group_concat(av.value,\'-\',av.attribute_value_id,
                                \'-\',gav.goods_attribute_value_id,
								 \'-\',gav.price_operate,
								 \'-\',gav.price_drift,
								 \'-\',gav.status,
								 \'-\',gav.quantity
                                 ) as av_value')
								
						->alias('ga')
						->join('left join __ATTRIBUTE__ as a using(attribute_id)')
						->join('left join __ATTRIBUTE_TYPE__ as at using(attribute_type_id)')
						->join('left join __GOODS_ATTRIBUTE_VALUE__ as gav using(goods_attribute_id)')
						->join('left join __ATTRIBUTE_VALUE__ as av using(attribute_value_id)')
						->group('ga.attribute_id')
						->where(['ga.goods_id'=>$goods_id])
						->select();
		
// 		dump($attr_info);
// 		        die;
		$option_list = [];
		foreach ($attr_info as $attr)
		{   
			if($attr['is_option'] == 1)
			{   $option =[];
				$option['goods_attr_id'] = $attr['goods_attr_id'];
				$option['option_id'] = $attr['attr_id'];
				$option['option_title'] = $attr['attr_title'];
				$value_list = explode(',',$attr['av_value']);
				foreach($value_list as $val)
				{
					list($v['attr_value'],$v['attr_value_id'],
						 $v['goods_attr_value_id'],
						 $v['price_operate'],$v['price_drift'],
						 $v['status'],$v['quantity']) = explode('-',$val);
					$option['list'][] = $v;
				}
				$option_list [] = $option;
			}
		}
// 		dump($option_list);
		$this->assign('option_list',$option_list);
		$this->display();
	 }
	}
} 
?>