<?php
namespace  Back\Controller;
use Think\Controller;
use Think\Upload;
use Think\Image;

class  CategoryController extends Controller
{
	public function  listAction()
	{  
		 $m_category = D('category');
		 $tree_list = $m_category->getTree();
// 		 dump($tre/e_list);
		 $this->assign('category_list',$tree_list);
		 $this->display();
	}
	
	public function  addAction()
	{   
		$m_category = D('category');
		if(IS_POST)
		{
			 $upload = new Upload();
             $upload->exts = ['gif','jpeg','png','jpg'];
             $upload->rootPath = './Upload/Category/Image/';
             $info = $upload->uploadOne($_FILES['image']);
             if($info)
             {
             	$filePath = $upload->rootPath.$info['savepath'].$info['savename'];
                $image = new Image();
//                 dump($filePath);
                $image->open($filePath);
               
                $thumb_root_Path = './Public/Thumb/category/';
                $thumb_sub_Path  =date('Y-m-d',time());
                $file_name = '40x40thumb'.$info['savename'].'';
                $fileTotle = $thumb_root_Path.$thumb_sub_Path.'/'.$file_name;
                if(!is_dir($thumb_root_Path.$thumb_sub_Path))
                {
                	mkdir($thumb_root_Path.$thumb_sub_Path,0755,ture);
                }
//                 dump($fileTotle);
                $image->thumb(40,40,Image::IMAGE_THUMB_FILLED )->save($fileTotle);
                
                $_POST['image'] = $info['savepath'].$info['savename'];
                $_POST['thumb'] = $thumb_sub_Path.'/'.$file_name;
               
                $res = $m_category->create();
                
//                 die;
                $lastId = $m_category->add($res);
                
                if($lastId)
                {     
                	  S(['type'=>'file']);
                	  S('category_tree_0',null);
                	  S('category_nested_0',null);
                	  $this->redirect('Back/Category/list');
                }
             }
             
             $this->error("数据插入失败",U('Back/Category/add',3));
			 
		}
		else
		{	
			$m_category = D('category');
			$tree_list = $m_category->getTree();
			$this->assign('category_list',$tree_list);
			$this->display();
		}
	}
}

?>