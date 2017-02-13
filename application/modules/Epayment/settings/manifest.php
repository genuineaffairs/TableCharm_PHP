<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'epayment',
    'version' => '4.0.2',
    'revision' => '$Revision: $Id$ $',
    'path' => 'application/modules/Epayment',
    'repository' => 'radcodes.com',
    'title' => 'ePayment',
    'description' => 'This plugin provides an uniform interface to handle and accept payments for various of Radcodes plugins.',
    'author' => 'Radcodes LLC',
    'changeLog' => 'settings/changelog.php',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Epayment/settings/install.php',
      'class' => 'Epayment_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.0.7'
      )
    ),    
    'directories' => array(
      'application/modules/Epayment',
    ),
    'files' => array(
      'application/languages/en/epayment.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Epayment_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'epayment',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'epayment_extended' => array(
      'route' => 'epayments/:controller/:action/*',
      'defaults' => array(
        'module' => 'epayment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),
    ),   
    'epayment_general' => array(
      'route' => 'epayments/:action/*',
      'defaults' => array(
        'module' => 'epayment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|checkout|return|cancel-return|notify)',
      ),
    ),
    'epayment_specific' => array(
      'route' => 'epayments/:action/:epayment_id/*',
      'defaults' => array(
        'module' => 'epayment',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'epayment_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),
  ),
);
