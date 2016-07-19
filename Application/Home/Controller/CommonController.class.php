<?php
namespace  Home\Controller;
use Think\Controller;
class CommonController extends Controller
{
	 public function __construct()
	 {
	 	parent::__construct();
	 	$this->initNav();
	 }
	 
	 private function initNav()
	 {
	 	$m_category = D('category');
	 	$nested_list = $m_category->getNested();
	 	$this->assign('nested_list',$nested_list);
	 }
} 
?>