<?php 
	class IndexAction extends BaseAction{
		public function index(){
			$this -> display();
		}

		public function categorymanager(){

			import('ORG.Util.Page');
			$count = M('CommonCategories') -> count();
			$page  = new Page($count,10);
			$limit = $page->firstRow . ',' .$page->listRows;

			$categories = M('CommonCategories')->order('entityCreateTime DESC')->limit($limit)->select();
			$this->categories = $categories;
			$this->page = $page->show();

			$this -> display();
		}

		public function savecategory(){
			if (!IS_AJAX) {
				halt('Undefined');
			}else{
				$category = D('CommonCategories');
				if($category->create()){
					$category -> number = I('number');
					$category -> title = I('title');
					$category -> parentNumber = I('parentNumber');
					$category -> description = I('description');
					$category -> sort = intval(I('sort'));
					if(isset($_POST['id']) && intval($_POST['id'])>0){
						//更新操作
						$category  -> id = intval(I('id'));
						if($category -> save())
							$this->ajaxReturn('更新成功','JSON');
						else
							$this->ajaxReturn('更新失败','JSON');
					}else{
						//添加操作
						if($category -> add())
							$this->ajaxReturn('添加成功','JSON');
						else
							$this->ajaxReturn('添加失败','JSON');
					}
				}else
					$this->ajaxReturn($category->getError(),'JSON');
			}
		}

		public function deletecategory(){
			if (!IS_AJAX) {
				halt('Undefined');
			}else{
				if(isset($_POST['id']) && intval($_POST['id'])){
					$id = intval($_POST['id']);
					if(M('CommonCategories')->where('id='.$id)->delete())
						$this->ajaxReturn('删除成功','JSON');
					else
						$this->ajaxReturn('删除失败失败','JSON');
				}else{
						$this->ajaxReturn('参数错误','JSON');
				}
			}
		}
	}

 ?>