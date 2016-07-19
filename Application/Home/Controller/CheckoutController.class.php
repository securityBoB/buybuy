<?php 
namespace  Home\Controller;
use Home\CommonController\CommonCtroller;
use Org\Util\Cart;
class CheckoutController extends CommonController
{
	 public  function addCartAction()
	 {
	 	 //购物车内的商品具有标识性
	 	 //1. 商品id  2 . 数量      3.  选项['2'=>4,3=>5]  表示选项goods_attribute_value_id 与 attribute_value_id 的 对应关系
         //   dump($_POST);
         //     die;
         //  die;
         //['2'=>4,3=>5]
	 	 $quantity = I('post.quantity',1);
	 	 $goods_id = I('post.goods_id');
	 	 $option_list = I('post.option');
         $opt = [];
	 	 foreach ($option_list as $option)
	 	 {
            $v = explode(',',$option);
            $opt [$v[0]]=  $v[1];
	 	 }
	 	 $cart = new Cart();
// 	 	 dump($opt);
// 	 	 die;
	 	 $cart->add($goods_id,$quantity,$opt);
	 	 if(I('post.is_ajax')==1)
	 	 {    
	 	 	 $this->ajaxReturn(['error'=>0]);
	 	 }else {
	 	   $this->redirect('Home/Checkout/cart', [], 0);
	 	 }
	 }
	 public function cartAction()
	 {  
	 	 
	 	$cart = new Cart();
	 	
	 	$glist = $cart->goodslist;
        
// 	 	dump($glist);
	 	
	 	$m_goods = M('goods');
	 	$m_gav = M('goods_attribute_value');
	 	$m_av = M('attribute_value');
	 	
	 	$goods_list = [];
	 	$max_total = 0;
	 	foreach ($glist as $g)
	 	{     
	 		  $goods =[];
              $goods['quantity'] = $g['quantity'];
              foreach ($g['option'] as $gav_id => $av_id)
              { 
              	
              	$goods['agv_id'][] = $gav_id;
              	$goods['av_id'][]  = $av_id;
              }
              
//               $goods['opt_str'] = implode(',',);
                     $row =$m_goods
 	 	                           ->field('g.goods_id,g.upc,g.thumb,g.name,g.price')
	 	                           ->alias('g')
	 	                           ->find($g['goods_id']); 
                 
              list($goods['goods_id'], $goods['upc'],
              	   $goods['thumb'],$goods['name'],$goods['price']) = [ $row['goods_id'],$row['upc'],
              	   		                               $row['thumb'],  $row['name'],$row['price']];
             $temp = [];
             foreach ($goods['av_id'] as $attr_id)
             {    
             	  $temp [] =  $m_av->getFieldByAttributeValueId($attr_id,'value'); 
             }
             $goods['attr_val'] =  implode('+',$temp); 
             //处理选项 价格1+ 2-
             $gav_id_list = array_keys($g['option']);
             $goods['real_price'] = $goods['price'];
             foreach ($gav_id_list as $gav_id)
             {
             	   $gav_list = $m_gav
             	                    ->field('price_operate,price_drift')
             	                    ->find($gav_id);
             	   
             	   if($gav_list['price_operate'] == 1 )
             	   {
             	   	   $goods['real_price'] +=  $gav_list['price_drift'];
             	   	
             	   }else if($gav_list['price_operate'] == 2)
             	   {
             	       $goods['real_price'] -= $gav_list['price_drift'];
             	   }
             }
             $goods['gav_list'] = implode(',',$goods['agv_id']);
             $goods['total_price'] = $goods['real_price'] * $goods['quantity'];
             $max_total += $goods['total_price'];
	 	     $goods_list[] = $goods;
	 	}
	 	
        $this->assign('goods_list',$goods_list);
        $this->assign('max_total',$max_total);
        $this->assign('total_weight',$cart->totalWeight);
        if(I('get.is_ajax')==1)
        {   
        	$this->display('cartContent');
        }else 
        {
	 	$this->display();
        }
	 }
	 
	 public function hasOptionAction()
	 {
	 	  $goods_id = I('get.goods_id');
	 	  
	 	  $m_ga = M('goods_attribute');
	 	  
	 	  
	 	  $coun =  $m_ga 
	 	               ->where(['goods_id'=>$goods_id,'is_option'=>1])
	 	               ->limit(1)
	 	               ->count();
	 	  if($coun)
	 	  {
	 	  	 $this->ajaxReturn(['option'=>1]);
	 	  }else
	 	  {
	 	  	 $this->ajaxReturn(['option'=>0]);
	 	  }
	 	
	 }
	 
	 public function removeCartAction()
	 {
	 	$goods_id = I('post.goods_id');
	 	$gav_list = I('post.gav_list');//gav_id_list
// 	 	$gav_list = '2,4';
	 	$cart = new Cart();
        
	 	$goods_list = $cart->goodslist;
	    
	    $option = explode(',',$gav_list);
	  
	 	if($option == '')
	 	{
	 		$option = [];
	 	}
	 	$cart->remove($goods_id,$option);
	  
	 }
	
}
?>