<?php
 namespace Home\Model;
 use Think\Model;
 class CategoryModel extends Model
 {
 	public function  getNested()
 	{
 		S(['type'=>'file']);
 		if(!($nested_list = S('category_nested_0')))
 		{
 			$list = $this
 			->where(['is_used'=>1])
 			->order('sort_number')
 			->select();
 			$nested_list = $this->nested($list);
 			S('category_nested_0',$nested_list);
 		}
//  		dump($nested_list);
 		return $nested_list;
 	}
 	public function getCrumb($category_id)
 	{
 	
 		$Slef = $this->find($category_id);
 		$parents = $this->getParent($category_id);
 		
 		return array_merge([$Slef],$parents);
 	}
 	
    private function getParent($category_id)
    {
    	static $parent_list = [];
    	
    	$parent_id = $this->getFieldByCategoryId($category_id,'parent_id');
    	
    	if($parent_id != 0)
    	{
    		$row = $this->find($parent_id);
    		$parent_list[] = $row;
    		$this->getParent($row['category_id']);
    	}
    	
//     	dump($parent_list);
       return $parent_list;
    	
    	
    	
    }
 	
 	
 	private function nested($list,$category_id=0)
 	{   
 		$nested =[];
 		foreach ($list as $row)
 		{
 			if(($row['is_nav'] == 0 && $row['parent_id']==0))
 			{
 				    continue;
 			}
 			if($row['parent_id'] ==$category_id)
 			{
 				$row['nested'] = $this->nested($list,$row['category_id']);
 				$nested[] = $row;
 			}
 		}
 		return $nested;
 	}
 	
 	
 	
 } 
?>