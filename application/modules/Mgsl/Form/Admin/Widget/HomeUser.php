<?php
class Mgsl_Form_Admin_Widget_HomeUser extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();

    // Set form attributes
    $this
      ->setTitle('Home User')
      ->setDescription('Shows user profile and profile edit links.')
      ;
  }
}