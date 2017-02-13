<?php
class Ynevent_Widget_FeatureEventsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$Name = $table -> info('name');
		$limit = 5;
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		
		$select = $table -> select() -> from($Name, "$Name.*");
		$select -> where("featured = 1");
		$select -> order("rand()");
		$select -> limit($limit);
		//echo $select;
		$this -> view -> items = $items = $table -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($items);
		
		$this -> view -> typed = '1';
		if (!$totalItems)
		{
			$this -> setNoRender();
		}
	}

}
