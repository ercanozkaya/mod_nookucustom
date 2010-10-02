<?php

class ModNookucustomHtml extends ModDefaultHtml
{
	public function getModel()
	{
		$identifier = $this->params->get('model');
       	$model = KFactory::tmp($identifier);

		return $model;
	}

	public function display()
	{
		try {
			$model = $this->getModel();
		}
		catch (KIdentifierException $e) {
			return;
		}

		$state = json_decode($this->params->get('state'));
		if (!empty($state)) {
			$model->set($state);
		}

		$this->assign('state', $model->getState());
		
		$name  = $model->getIdentifier()->name;
		if(KInflector::isPlural($name)) {
			$this->assign($name, 	$model->getList())
				 ->assign('total',	$model->getTotal());
		}
		else {
			$this->assign($name, $model->getItem());
		}
		
		parent::display();
	}
	
	public function loadTemplate($identifier = null, $data = null)
	{
		// Clear prior output
		$this->output = '';
		
		$data = isset($data) ? $data : $this->_data;
		
		// TODO: find a way to use tmpfile()
		$file = tempnam(sys_get_temp_dir(), 'nooku-tmpl');
		file_put_contents($file, $this->params->get('template'));

		/*
		$file = tmpfile();
		fwrite($file, $this->params->get('template'));
		*/
		
		$result = KFactory::get($this->getTemplate())
					->render($file, $data);

		unlink($file);
		
		return $result;  
	}
}