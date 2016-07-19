<?php 
namespace  Back\Controller;
use Think\Controller;
class GenerateController extends Controller
{
	  public  function  controllerAction()
	  {  
	  	 if(IS_POST)
	  	 {
	  	 	 $table_name = 'brand' ;
	  	 	 
	  	 	 
	  	 	 $model_name = $contr_name = implode('',array_map(function($v)
	  	 	 		            {
	  	 	 			         return ucfirst(strtolower($v));
	  	 	 		            },explode('_',$table_name)));
	  	 	   
	  	 	 $search[] = '__CONTROLLER__';// 查找内容
	  	 	 $replace[] = $contr_name;// 提换内容
	  	 	 
	  	 	 $search[] = '__MODEL__';
	  	 	 $replace[] = $model_name;
	  	 	 
	  	 	 $search[] = '__PK_FIELD__';
	  	 	 $replace[] = M($model_name)->getPk();
	  	 	 
	  	 	 
// 	  	 	 dump($replace);
// 	  	 	 die;
	  	 	 $content = file_get_contents(COMMON_PATH . 'Controller/Controller.template.php');
	  	 	 
	  	 	 $controller_content = str_replace($search, $replace, $content);
	  	 	 
	  	 	 $controller_file = APP_PATH . 'Back/Controller/' . $contr_name . 'Controller.class.php';
	  	 	 
	  	 	 file_put_contents($controller_file, $controller_content);
	  	 	 
	  	 	 echo '控制器生成成功';
	  	 }else 
	  	 {
	  	    $this->display();
	  	 }
	  } 
	  public function viewAction()
	  {
	  	if(IS_POST)
	  	{   
	  		$table_name = 'brand' ;
	  		$model_name = $contr_name = implode('',array_map(function($v)
	  		{
	  			return ucfirst(strtolower($v));
	  		},explode('_',$table_name)));
	  		 
		  	$model =  M($table_name);
		  	$fields = $model->getFields();
		  	$type = $fields['_type'];
		  	$pk = $fields['_pk'];
		  	unset($fields['_pk']);
		  	unset($fields['_type']);
		  	//['field'=>'brand_id', 'type'=>'int unsigned', 'pk'=>true],
		  	$field_list = [] ;
		  	foreach ($fields as $fd)
		  	{
		  		$field = [] ;
		  		$field['field'] = $fd;
		  		$field['type'] = $type[$fd];
		  		if($fd == $pk)
		  		{
		  			$field['pk'] = true;
		  		}else
		  		{
		  			$field['pk'] = false;
		  		}
		  		$field_list [] = $field;
		  	}
		  	
// 		  	dump($field_list);
// 		  	die;
		  	//生成添加模板
		  	$element_list = '';
		  	$temp_text = '';
		  	foreach ($field_list as $field)
		  	{  
		  		 if($field['pk'])
		  		 {
		  		 	 continue;
		  		 }
		  		 if(
		  		 	substr($field['type'],0,3) == 'int'||
		  		 	substr($field['type'],0,7) == 'varchar'||
		  		 	substr($field['type'],0,7) == 'tinyint'||
		  		 	substr($field['type'],0,8) == 'smallint'||
		  		 	substr($field['type'],0,8) == 'smallint'||
		  		 	substr($field['type'],0,6) == 'bigint'||
		  		 	substr($field['type'],0,8) == 'datetime'||
		  		 	substr($field['type'],0,4) == 'time'||
		  		 	substr($field['type'],0,4) == 'year'
		  		   )
		  		 { 
// 		  		 	dump('d');
// 		  		 	die;
		  		 	$text_template = file_get_contents (COMMON_PATH.'view/element.text.template.html'); 
// 		  		 	mixed str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
		  		 	$search_text = '__FIELD__' ;
		  		 	$replace_text = $field['field'];
		  		 	$temp_text .=str_replace($search_text,$replace_text,$text_template); 
// 		  		 	dump($temp_text);
// 		  		 	die;
		  		 }
		  		 $element_list .= $temp_text;
		  	}
		  	
		    $search = [] ;
		    $replace = [] ;
		  	
		    $search [] = '__CONTROLLER_TITLE__';
		  	
		    $replace[] =  $contr_name.'管理';
		    
		    $search [] = '__ACTION_TITLE__';
		     
		    $replace[] =  $model_name.'添加';
		  
		    $search [] = '__ELEMENT_LIST__';
		     
		    $replace[] =  $element_list;
		  	
		    
		    $add_temp = file_get_contents(COMMON_PATH.'view/add.template.html');
		    
		    
		    
// 		    dump($add_temp);
		    $add_text = str_replace($search,$replace,$add_temp);
		    
// 		    dump($add_text);
		    $add_view_path = APP_PATH.'Back/view/'.$contr_name.'/add.html'; 
// 		    dump($add_view_path);
		    if(!is_dir(APP_PATH.'Back/view/'.$contr_name))
		    {
		    	mkdir(APP_PATH.'Back/view/'.$contr_name,0755,ture);
		    }
		    
		    file_put_contents($add_view_path,$add_text);
		    
		    echo 'add<br>';
		    
		    //生成list模板
		    
		    $head_list = $body_list = '';
		    
		    $head_td_temp =  file_get_contents (COMMON_PATH.'view/td_head_template.html'); 
		    $body_td_temp =  file_get_contents (COMMON_PATH.'view/td_body_template.html'); 
		    
		    foreach ($field_list as $field)
		    {
		    	if($field['pk'])
		    	{
		    		continue;
		    	}
		    	$search_td = '__FIELD__';
		    	$replace_td = $field['field'];
		    	$head_list .=str_replace($search_td,$replace_td,$head_td_temp);
		    	$body_list .=str_replace($search_td,$replace_td,$body_td_temp);
		    }
		   
// 		    主题
		    $search = [] ;
		    $replace = [] ;
		     
		    $search [] = '__CONTROLLER_TITLE__';
		     
		    $replace[] =  $contr_name.'管理';
		    
		    $search [] = '__ACTION_TITLE__';
		     
		    $replace[] =  $model_name.'展示';
		    
		    $search [] = '__HEAD_LIST__';
		     
		    $replace[] =  $head_list;
		    
		    $search [] = '__BODY_LIST__';
		     
		    $replace[] =  $body_list;
		    $search [] = '__PK_FIELD__';
		     
		    $replace[] =  $pk;

		    $list_temp =  file_get_contents (COMMON_PATH.'view/list.template.html');
		    
		    
		    $list_text = str_replace($search,$replace,$list_temp);
		    
		    // 		    dump($add_text);
		    $add_view_path = APP_PATH.'Back/view/'.$contr_name.'/list.html';
		    // 		    dump($add_view_path);
		    if(!is_dir(APP_PATH.'Back/view/'.$contr_name))
		    {
		    	mkdir(APP_PATH.'Back/view/'.$contr_name,0755,ture);
		    }
		    
		    file_put_contents($add_view_path,$list_text);
		    
		    echo 'list<br>';
// 		    dump($head_list);
// 		    dump($body_list);
// 		    die;
		  	//edit 模板
		    $element_list = '';
		    $temp_text = '';
		    foreach ($field_list as $field)
		    {
		    	if($field['pk'])
		    	{
		    		continue;
		    	}
		    	if(
		    			substr($field['type'],0,3) == 'int'||
		    			substr($field['type'],0,7) == 'varchar'||
		    			substr($field['type'],0,7) == 'tinyint'||
		    			substr($field['type'],0,8) == 'smallint'||
		    			substr($field['type'],0,8) == 'smallint'||
		    			substr($field['type'],0,6) == 'bigint'||
		    			substr($field['type'],0,8) == 'datetime'||
		    			substr($field['type'],0,4) == 'time'||
		    			substr($field['type'],0,4) == 'year'
		    	)
		    	{
		    		// 		  		 	dump('d');
		    		// 		  		 	die;
		    		$text_template = file_get_contents (COMMON_PATH.'view/edit_eliment_text.template.html');
		    		// 		  		 	mixed str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
		    		$search_text = '__FIELD__' ;
		    		$replace_text = $field['field'];
		    		$temp_text .=str_replace($search_text,$replace_text,$text_template);
		    		// 		  		 	dump($temp_text);
		    		// 		  		 	die;
		    	}
		    	$element_list .= $temp_text;
		    }
		     
		    $search = [] ;
		    $replace = [] ;
		     
		    $search [] = '__CONTROLLER_TITLE__';
		     
		    $replace[] =  $contr_name.'管理';
		    
		    $search [] = '__ACTION_TITLE__';
		     
		    $replace[] =  $model_name.'编辑';
		    
		    $search [] = '__ELEMENT_LIST__';
		     
		    $replace[] =  $element_list;
		    
		    $search [] = '__PK_FIELD__';
		     
		    $replace[] =  $pk;
		    
		    $edit_temp = file_get_contents(COMMON_PATH.'view/edit.template.html');
		    
		    
		    
		    // 		    dump($add_temp);
		    $edit_text = str_replace($search,$replace,$edit_temp);
		    
		    // 		    dump($add_text);
		    $edit_view_path = APP_PATH.'Back/view/'.$contr_name.'/edit.html';
		    // 		    dump($add_view_path);
		    if(!is_dir(APP_PATH.'Back/view/'.$contr_name))
		    {
		    	mkdir(APP_PATH.'Back/view/'.$contr_name,0755,ture);
		    }
		    
		    file_put_contents($edit_view_path,$edit_text);
		    
		    echo 'eidt<br>';
	  	}
	  	else 
	  	{
	  		 $this->display();
	  	}
	  }
}
?>