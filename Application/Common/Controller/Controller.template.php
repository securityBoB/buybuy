<?php 
namespace  Back\Controller;
use Org\Util\Page;
use Think\Controller;

class __CONTROLLER__Controller extends  Controller
{
	public function addAction()
	{  
		$model = M(__MODEL__);
		
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
		$model = M(__MODEL__);
		
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
	public function editAction($__PK_FIELD__)
	{
		$model = M(__MODEL__);
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
					 U('exit',[$__PK_FIELD__=>I('post.$__PK_FIELD__')]),0);
		}
		else 
		{ 
			 $this->assign('row',$model->find($__PK_FIELD__));
			 $this->display();
		}
	}
	
	public function removeAction($__PK_FIELD__)
	{
		$model = M('__MODEL__');
		if (false === $model->delete($__PK_FIELD__)) {
			$this->error('删除失败'.$model->getError(), U('list'), 2);
		} else {
		
			$this->redirect('list', [], 0);
		}
	}
}
 
?>