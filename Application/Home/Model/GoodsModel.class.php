<?php 
namespace  Home\Model;
use Think\Model;
class GoodsModel extends  Model
{
	public function getBreadCrumb($id)
	{
		$category_id = $this->getFieldByGoodsId($id,'category_id');
		if($category_id == 0)
		{
			return [];
		}
		
		$m_category =D('category');
		
		return $m_category->getCrumb($category_id);
	}
}
?>