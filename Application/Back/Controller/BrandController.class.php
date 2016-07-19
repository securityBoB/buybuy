<?php 
namespace  Back\Controller;
use Org\Util\Page;
use Think\Controller;

class BrandController extends  Controller
{
	public function addAction()
	{  
		$model = M(Brand);
		
		if(IS_POST)
		{
			if ($model->create()) 
			 {
				if ($model->add()) 
				{
					$this->redirect('list', [], 0);
				}
			 }
		}else 
		{
			$this->display();
		}
		   
	}
	public function listAction()
	{
		$model = M(Brand);
		
		$pagesize = 10;
		
		$page = I('get.p', '1');
		
		$list = $model 
		             ->page($page, $pagesize)
		             ->select();
		
		$this->assign('list', $list);
		
		$total = $model->count();
		
		$temp_page = new Page($total, $pagesize);
		
		$this->assign('page_html', $temp_page->show());
		
		$this->display();
		
	}
	public function editAction($brand_id)
	{
		$model = M(Brand);
		if(IS_POST)
		{ 
			if($model->create())
			{
				if($model->save())
				{
					$this->redirect('list', [], 0);
				}
			}
			$this->error('更新失败'.$model->getError(),
					 U('exit',[$brand_id=>I('post.$brand_id')]),0);
		}
		else 
		{ 
			 $this->assign('row',$model->find($brand_id));
			 $this->display();
		}
	}
	
	public function removeAction($brand_id)
	{
		$model = M('Brand');
		if (false === $model->delete($brand_id)) {
			$this->error('删除失败'.$model->getError(), U('list'), 2);
		} else {
		
			$this->redirect('list', [], 0);
		}
	}
}
 
?>