<?php 
namespace  Back\Controller;
use Think\Controller;
class SystemController extends  Controller
{
	
	public  function settingAction()
	{   
		$gruop = M('setting_group');
		$gruop_list = $gruop->select();
// 		dump($gruop_list);
        $this->assign('gruop_list',$gruop_list);
		$setting = M('setting'); 
        $set_list = $setting
                    ->field('s.*,st.type_title')
                    ->alias('s')
                    ->join('left join __SETTING_TYPE__ as st on s.type_id = st.setting_type_id')
                     ->select();  
        foreach ($set_list as $key=>$op_list)
        {
        
        	switch ($op_list["type_title"])
        	{
        		case 'select':
        			$option_list=  explode(',',$op_list['option_list']);
        			
        			$result = array_map(function($p)
        			{
        				return  explode('-',$p);
        			},$option_list);
        			$set_list[$key]["option_list"] = $result;
        			break;
        		case 'checkbox':
        			$option_list=  explode(',',$op_list['option_list']);
        			$value_list=  explode(',',$op_list['setting_value']);
//         			dump($value_list);
        			$result = array_map(function($p)
        			{
        				return  explode('-',$p);
        			},$option_list);
        			$set_list[$key]["setting_value"] = $value_list;
        			$set_list[$key]["option_list"] = $result;
        			break;
        	}
        	 
        }
        
       $grouped_setting_list =[];
       foreach ($set_list as $set)
       {
       	$grouped_setting_list[$set[group_id]][] = $set;
       }
//        dump($grouped_setting_list);
       $this->assign('set_list',$grouped_setting_list);
        $this->display();
	}
	
	
	public function updateAction()
	{
		dump($_POST);
		$m_setting = M('setting');
		
		$upd_list = $_POST['setting'];
		
		foreach ($upd_list as $key=>$value)
		{
             $data['setting_id'] = $key;
             if(is_array($value))
             {
             	$value = implode(',',$value);
             }
             $data['setting_value'] =$value;
             $res = $m_setting->save($data);
		}
		if($res !== false )
		{
			$this->redirect('Back/System/setting');
		}	
	}
	
	public  function updateOneAction()
	{
		$data['setting_id'] = $_POST['setting_id'];
		$data['setting_value'] = $_POST['setting_value'];
// 		dump($data);
		$m_setting = M('setting');
		
		$res = $m_setting->save($data);
		if($res === false)
		{
			$this->ajaxReturn(['error'=>1]);
		}else 
		{
			$this->ajaxReturn(['error'=>0]);
		}
	}
	
	public  function getSettingAction()
	{
		dump(CC('use_captcha'));
	}
}
?>