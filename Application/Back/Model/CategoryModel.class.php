<?php
 namespace Back\Model;
 use Think\Model;
 class CategoryModel extends Model
 {
 	public function getTree()
 	{            
 		          S(['type'=>'File']);
 		          if(!($tree_list = S('category_tree_0')))
 		          {
 		           $list = $this
 		                       ->order('sort_number')
 		                       ->select();
                   $tree_list = $this->tree($list);
                
                  
                   S('category_tree_0',$tree_list);
 		          } 
 		          return $tree_list;
 	}
 	protected  function tree($list,$category_id=0,$deep=0)
 	{  
        static $treeList =[];
 		foreach ($list as $row)
 		{  
 			
 			if($row['parent_id'] == $category_id)
 			{
 				$row['deep'] = $deep;
 				$treeList[]= $row;
 				$this->tree($list,$row['category_id'],$deep+1);
 			}
 		}
 			
 		
 		return $treeList;
 		
 	}
 	
 	
 	
 } 
?>