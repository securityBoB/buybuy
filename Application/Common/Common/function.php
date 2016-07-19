<?php 
function CC($key)
{
	$m_setting = M('setting');
	
	
	$res = $m_setting
	          ->field('setting_value')
	          ->where(['setting_key'=>$key])
	          ->find();
	 
	if($res)
	{
		return $res['setting_value'];
	}else 
	{
		return null;
	}
}


?>