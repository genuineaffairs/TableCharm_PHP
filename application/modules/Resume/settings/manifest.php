<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

defined('RADCODES_ROUTE_RESUME_SINGLE')
  || define('RADCODES_ROUTE_RESUME_SINGLE', 'resume');
  
defined('RADCODES_ROUTE_RESUME_PLURAL')  
  || define('RADCODES_ROUTE_RESUME_PLURAL', 'resumes');

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'resume',
    'version' => '4.0.8',
    'path' => 'application/modules/Resume',
    'repository' => 'radcodes.com',

    'title' => 'Resume / Curriculum Vitae Plugin',
    'description' => 'This plugin let your member create resume on your social networking website.',
    'author' => 'Radcodes Developments',  

    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Resume/settings/install.php',
      'class' => 'Resume_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.1.3'
      ),
      'epayment' => array(
        'type' => 'module',
        'name' => 'epayment',
        'minVersion' => '4.0.2'
      )      
    ),
    'directories' => array(
      'application/modules/Resume',
    ),
    'files' => array(
      'application/languages/en/resume.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Resume_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Resume_Plugin_Core',
    ),
    array(
      'event' => 'onResumeDeleteBefore',
      'resource' => 'Resume_Plugin_Core'
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'resume',
    'resume_album',
    'resume_category',
    'resume_package',
    'resume_photo',
    'resume_section',
    'resume_education',
    'resume_employment',
    'resume_sportinghistory',
    'resume_award',
    'resume_qualification',
    'resume_coachinghistory',
    'resume_video',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'resume_extended' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/:controller/:action/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'resume_general' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/:action/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|browse|create|manage|tags|upload-photo|testing)',
      )
    ),   
    'resume_specific' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/item/:resume_id/:action/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'resume',
      ),
      'reqs' => array(
        //'action' => '(edit|sections|add-section|order-sections|delete|success|location|payments|style|packages|checkout|renew|upgrade|payment-cancel|payment-success)',
        'resume_id' => '\d+',
      )
    ),
    'resume_profile' => array(
      'route' => RADCODES_ROUTE_RESUME_SINGLE . '/:resume_id/:slug/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'profile',
        'action' => 'index',
        'slug' => ''
      ),
      'reqs' => array(
        'resume_id' => '\d+',
      )
    ),
    'resume_user' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/list/:user_id/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      )
    ),
    'resume_package' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/packages/:action/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'package',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(browse)',
      )
    ),
    'resume_package_profile' => array(
      'route' => RADCODES_ROUTE_RESUME_PLURAL . '/package/:package_id/*',
      'defaults' => array(
        'module' => 'resume',
        'controller' => 'package',
        'action' => 'profile',
      ),
      'reqs' => array(
        'package_id' => '\d+',
      )
    ),
    'resume_video_view' => array(
        'route' => RADCODES_ROUTE_RESUME_SINGLE . '/:resume_id/:slug/video/:video_id/*',
        'defaults' => array(
            'module' => 'resume',
            'controller' => 'video',
            'action' => 'view',
            'slug' => '',
        ),
        'reqs' => array(
            'resume_id' => '\d+',
            'video_id' => '\d+'
        )
    ),
    'resume_video_general' => array(
        'route' => RADCODES_ROUTE_RESUME_SINGLE . '/videos/:action/*',
        'defaults' => array(
            'module' => 'resume',
            'controller' => 'video',
            'action' => 'view',
            'slug' => '',
        ),
    ),
    'resume_video_create' => array(
        'route' => RADCODES_ROUTE_RESUME_SINGLE . '/video/create/:resume_id/*',
        'defaults' => array(
            'module' => 'resume',
            'controller' => 'video',
            'action' => 'create',
        ),
    ),
    'resume_video_edit' => array(
        'route' => RADCODES_ROUTE_RESUME_SINGLE . '/video/edit/:video_id/*',
        'defaults' => array(
            'module' => 'resume',
            'controller' => 'video',
            'action' => 'edit'
        )
    ),
    'resume_video_delete' => array(
        'route' => RADCODES_ROUTE_RESUME_SINGLE . '/video/delete/:video_id/:resume_id/*',
        'defaults' => array(
            'module' => 'resume',
            'controller' => 'video',
            'action' => 'delete'
        ),
        'reqs' => array(
            'video_id' => '\d+',
            'resume_id' => '\d+'
        )
    ),
  ),
);
