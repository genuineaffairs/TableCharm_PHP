<?php
$params = array(
  'route' => 'resume_general',
  'reset' => true,
  'action' => 'browse',
);
$route = $params['route'];
$reset = $params['reset'];
unset($params['route']);
unset($params['reset']);
$href = Zend_Controller_Front::getInstance()->getRouter()
  ->assemble($params, $route, $reset);
?>

<div class="wrapper_padding">
  <a class="resume_button_link" href="<?php echo $href ?>"><?php echo $this->translate("Back to browse CVs") ?></a>
</div>