<?php 
namespace  Home\Controller;
use Home\Controller\CommonController;

class ShopController extends  CommonController
{
	public function indexAction()
	{
       
		 $m_goods = M('goods');
		 $newest_num = CC('newest_number');
		 $goods_list = $m_goods
		                      ->where(['is_delete'=>0,'status'=>1])
		                      ->order('create_at desc')
		                      ->limit($newest_num)
		                      ->select();

		 $this->assign('goods_list',$goods_list);
// 		 dump($goods_list);
         $this->display();
	}
	public function goodsAction($id)
	{
        
		$m_goods = D('goods');
		 
		$goods = $m_goods 
		            ->field('g.*,ss.title as status_title')
		            ->alias('g')
		            ->join('left join __STOCK_STATUS__ as ss on g.stock_status_id =ss.stock_status_id ')
		            ->find($id);
		
		$breadcrumb = $m_goods->getBreadCrumb($id);
		
		$breadcrumb = array_reverse($breadcrumb);
// 		dump($breadcrumb);
		
		$m_image = M('GoodsImage');
		$image_list = $m_image
						->where(['goods_id'=>$id])
						->order('sort_number')
						->select();
		
// 		dump($image_list);
		
		$this->assign('breadcrumb',$breadcrumb);
		$this->assign('image_list',$image_list);
		$this->assign('goods',$goods);
		
		
		//获取商品属性
		$m_ga = M('goods_attribute');  
		$att_list = $attr_info =  $m_ga
		                  ->field('a.title as attr_title , a.attribute_id as attr_id, at.title as attr_type_title ,
		                  		 ga.option  as is_option ,gav.value as gav_value,
		                  		 group_concat(av.value,\'-\',av.attribute_value_id,
		                  		  \'-\',gav.goods_attribute_value_id,
		                  		  \'-\',ga.goods_attribute_id) as av_value,
		                  		 group_concat(av.value) as attribute_value'
		                         )
		                  ->alias('ga')
		                  ->join('left join __ATTRIBUTE__ as a using(attribute_id)')
		                  ->join('left join __ATTRIBUTE_TYPE__ as at using(attribute_type_id)')
		                  ->join('left join __GOODS_ATTRIBUTE_VALUE__ as gav using(goods_attribute_id)')
		                  ->join('left join __ATTRIBUTE_VALUE__ as av using(attribute_value_id)')
		                  ->group('ga.attribute_id')
		                  ->where(['ga.goods_id'=>$id])
		                  ->select();
		
// 		dump($attr_info);
       
		$option_list = [];
		foreach ($attr_info as $attr)
		{  
		    if($attr['is_option'] == 1)
		    {   
		    	 $option = [];
		    	 $option['option_id'] = $attr['attr_id']; 
		    	 $option['goods_id'] = $id; 
		    	 $option['option_title'] = $attr['attr_title']; 
			     $value_list = explode(',',$attr['av_value']);
			     foreach($value_list as $val)
			     {
			     	    list($v['attr_value'],$v['attr_value_id'],$v['gav_id'],$v['ga_id']) = explode('-',$val);
			     	    $option['list'][] = $v;
			     }
			     $option_list [] = $option;
		    }
		}
// 		dump($option_list);
        $this->assign('attr_list',$attr_info); 
        $this->assign('goods_id',$id); 
        $this->assign('option_list',$option_list); 
		$this->display();
	}
	
}
?>