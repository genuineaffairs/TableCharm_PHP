<?php
class Mgsl_Form_Admin_Widget_UserCircles extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();

    // Set form attributes
    $this
      ->setTitle('Home User')
      ->setDescription('Shows a list of the logged-in user\'s circles.')
      ;
  }
}