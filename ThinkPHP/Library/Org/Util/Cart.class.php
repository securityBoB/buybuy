<?php
 namespace  Org\Util;

 class Cart 
 {  
 	
    public $goodslist = [];
    public $totalWeight;
    
    public function __construct()
    {
    	 $this->get();
    	 $this->setTotalWeight();
    }
 	public function add($goods_id,$quantity,$option = [])
 	{    
       //先判断商品是否存在
       //             dump($this->goodslist);
 		             $length = count($this->goodslist);
		             $flag = false;
				     foreach ($this->goodslist as $k=>$goods)
				     {  
				     	   
				     	     if($goods['goods_id'] == $goods_id )
				     	     {
				     	     	 
				     	     	 $len_op = count($goods['option']);
				     	     	 $i = 0;
                                 
				     	     	 foreach($goods['option'] as $key =>$val)
				     	     	 {     
				     	     	 	  
				     	     	 	  if($option[$key] == $val )
				     	     	 	  {      
				     	     	 	  	     
				     	     	 	  	     $i++;
				     	     	 	  	     if(count($option) == $len_op)
				     	     	 	  	     {
					     	     	 	  	     if($i == $len_op )
					     	     	 	  	     {  
					     	     	 	  	     	$flag = true;
					     	     	 	  	     }
				     	     	 	  	     }
				     	     	 	  } 
				     	     	 }
				     	     	 if($flag)
				     	     	 {  
				     	     	 	$this->goodslist[$k]['quantity'] += $quantity;
				     	     	 	break;
				     	     	 }
				     	     }
				     }
				     if(!$flag)
				     {
				     $this->goodslist[$length] = [
				     		'goods_id' => $goods_id,
				     		'quantity' => $quantity,
				     		'option' => $option
				     		];
				     }
            
		  $this->setTotalWeight();   
 		  $this->save();
		  return $this;     
 	}
 	
 	public function save()
 	{ 
 		
 		 if(session('user'))
 		 {
 		 	//数据库存储
 		 }else 
 		 {
 		 	cookie('goodslist',serialize($this->goodslist));
 		 }
 		
 	}
 	public function get()
 	{
 			
 		if(session('user'))
 		{
 			//数据库获取	
 		}else
 		{
           if(cookie('goodslist'))
           {
           	  $this->goodslist = unserialize(cookie('goodslist'));
           }else 
           {
           	  $this->goodslist = [];
           }
 		}
 	}
 	
 	public function getTotalWeight()
 	{   
 		
 		return  $this->totalWeight;
 	}
 	
    public function setTotalWeight()
    {
    	
    	 $goodslist = $this->goodslist; 
         $m_goods = M('goods');
         $goods_list = [];
         $weight = 0;
    	 foreach ($goodslist as $goods)
    	 {   
    	 	  $g = [];
    	 	  $g['goods_id'] = $goods['goods_id'];
    	 	  $row =       $m_goods  
    	 	                      ->field('g.weight,wu.title')
    	 	                      ->alias('g')
    	 	                      ->join('left join __WEIGHT_UNIT__ as wu  using(weight_unit_id)')
    	 	                      ->find($goods['goods_id']);
    	 	 $g['weight'] = $row['weight']; 
    	 	 $g['weigth_unit'] = $row['title']; 
    	 	 $goods_list [] = $g;
    	 	 
    	 	 if($g['weigth_unit'] == '克')
    	 	 {
    	 	 	$weight += $g['weight']/1000* $goods['quantity'];
    	 	 }else if($g['weigth_unit'] == '千克')
    	 	 { 
    	 	 	$weight += $g['weight'] * $goods['quantity'];
    	 	 }
    	 	 else if($g['weigth_unit'] == '500克(斤)')
    	 	 {
    	 	 	$weight += $g['weight']/2* $goods['quantity'];
    	 	 }
    	 }
         $this->totalWeight = $weight;
    }
    
    public function remove($goods_id,$option)
    {  
                 
    	        if($option[0] =='')
    	        {
    	        	$option = [];
    	        }
		    	foreach ($this->goodslist as $k=>$goods)
		    	{   
		    		
		    		if($goods['goods_id'] == $goods_id 
		    				&&count($option) == count($goods['option']) && count($goods['option']) == 0)
		    		{  
		    			$key =$k;
		    		}
		    		else if($goods['goods_id'] == $goods_id 
		    				&& count($option) == count($goods['option'])
		    				&&count($option)>0)
		    		{  
		    			$i = 0;
		    			foreach ($option as $v)
			    		{  
			    				if(isset($goods['option'][$v]))
			    				{  
			    					$i++;
			    					if($i ==count($option))
			    					{   
			    						$key =$k;
			    					}
			    				}
			    		}
		    		}
		    		
		    		
		    	}
    	$this->goodslist = array_values($this->goodslist);
    	unset($this->goodslist[$key]);
    	$this->save();
    	return $this;
    }
 	
 }
?>