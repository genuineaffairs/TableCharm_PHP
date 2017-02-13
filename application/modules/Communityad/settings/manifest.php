<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
        'package' =>
        array(
                'type' => 'module',
                'name' => 'communityad',
                 'version' => '4.8.2',
                'path' => 'application/modules/Communityad',
								'title' => 'Advertisements / Community Ads Plugin',
								'description' => 'Advertisements / Community Ads Plugin',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
								'date' => 'Friday, 09 Jul 2010 18:33:08 +0000',
								'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
                'actions' =>
                array(
                        0 => 'install',
                        1 => 'upgrade',
                        2 => 'refresh',
                        3 => 'enable',
                        4 => 'disable',
                ),
                'callback' => array(
                        'path' => 'application/modules/Communityad/settings/install.php',
                        'class' => 'Communityad_Installer',
                ),
                'directories' => array(
                        'application/modules/Communityad',
                ),
                'files' => array(
                        'application/languages/en/communityad.csv',
                ),
        ), 
        'sitemobile_compatible' =>true,
        // Hooks ---------------------------------------------------------------------
        'hooks' => array(
                array(
                        // 'event' => 'addActivity',
                        'event' => 'onRenderLayoutDefault',
                        'resource' => 'Communityad_Plugin_Core'
                ),
                array(
                        'event' => 'onUserDeleteBefore',
                        'resource' => 'Communityad_Plugin_Core'
                ),
                 array(
                        'event' => 'onCommunityadAdcampaignDeleteBefore',
                        'resource' => 'Communityad_Plugin_Core'
                ),
                 array(
                        'event' => 'onCommunityadUseradDeleteBefore',
                        'resource' => 'Communityad_Plugin_Core'
                ),
        ),
        // Items ---------------------------------------------------------------------
        'items' => array(
                'option',
                'meta',
                'map',
                'value',
                'target',
                'package',
                'usertarget',
                'userads',
                'adcampaign',
                'communityad_adcancel',
                'communityad_pagesetting',
                'communityad_module',
                'communityad_infopage',
                'communityad_faq',
                'communityad_like',
                'adstatistic',
                'communityad_gateway',
                'communityad_transaction',
				'communityad_storie',
			    'communityad_adtype'
        ),
        // Routes --------------------------------------------------------------------
        'routes' => array(
                // Public+

                'sponcerd_display' => array(
                        'route' => 'ads/stories/*',
                        'defaults' => array(
                                'module' => 'communityadsponsored',
                                'controller' => 'index',
                                'action' => 'adboard',
                                'page' => 1
                        )
                ),

                'communityad_display' => array(
                        'route' => 'ads/adboard/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'display',
                                'action' => 'adboard',
                                'page' => 1
                        )
                ),
                'communityad_help_and_learnmore' => array(
                        'route' => 'ads/help-and-learnmore/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'display',
                                'action' => 'help-and-learnmore',
                        )
                ),
                // User
                'communityad_listpackage' => array(
                        'route' => 'ads/package/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'index',
                        )
                ),
                'communityad_create' => array(
                        'route' => 'ads/create/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'create'
                        )
                ),
                'communityad_edit' => array(
                        'route' => 'ads/edit/:id',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'edit',
                                'id' => '0'
                        ),
                        'reqs' => array(
                                'id' => '\d+'
                        )
                ),
                'communityad_copyad' => array(
                        'route' => 'ads/create/state/:copy/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'edit',
                        ),
                        'reqs' => array(
                                'copy' => 'copy'
                        )
                ),
                'communityad_targetdetails' => array(
                        'route' => 'ads/targetdetails/:id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'target-details'
                        )
                ),
                'communityad_webpagereport' => array(
                        'route' => 'ads/statistics/export-webpage/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'statistics',
                                'action' => 'export-webpage'
                        )
                ),
                'communityad_reports' => array(
                        'route' => 'ads/statistics/export-report/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'statistics',
                                'action' => 'export-report'
                        )
                ),
                'communityad_campaigns' => array(
                        'route' => 'ads/campaigns/:action/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'statistics',
                                'action' => 'index'
                        )
                ),
                'communityad_ads' => array(
                        'route' => 'ads/campaignads/:action/:ad_subject/:adcampaign_id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'statistics',
                                'action' => 'browse-ad',
                                'ad_subject' => 'campaign',
                                'adcampaign_id' => 0
                        ),
                        'reqs' => array(
                                'adcampaign_id' => '\d+'
                        )
                ),
                'communityad_userad' => array(
                        'route' => 'ads/detail/:action/:ad_subject/:ad_id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'statistics',
                                'action' => 'view-ad',
                                'ad_subject' => 'ad',
                                'ad_id' => 0
                        ),
                        'reqs' => array(
                                'ad_id' => '\d+'
                        )
                ),
                'communityad_payment' => array(
                        'route' => 'ads/payment/',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'payment',
                                'action' => 'index',
                        ),
                ),
                'communityad_process_payment' => array(
                        'route' => 'ads/payment/process',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'payment',
                                'action' => 'process',
                        ),
                ),
                'communityade_renew' => array(
                        'route' => 'ads/renew/:id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'renew',
                        )
                ),
                'communityad_editcamp' => array(
                        'route' => 'ads/editcamp/:id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'editcamp',
                        ),
                ),
                'communityad_deleteselectedcamp' => array(
                        'route' => 'ads/deleteselectedcamp/',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'deleteselectedcamp',
                        )
                ),
                'communityad_deletecamp' => array(
                        'route' => 'ads/deletecamp/:id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'deletecamp',
                        ),
                ),
                'communityad_deletead' => array(
                        'route' => 'ads/deletead/:id/*',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'index',
                                'action' => 'deletead',
                        ),
                ),
                'communityad_adredirect' => array(
                        'route' => 'ads/redirect/:adId',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'display',
                                'action' => 'ad-redirect',
                        )
                ),
                'communityad_help' => array(
                        'route' => 'ads/help-and-learnmore/page_id/:page_id',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'display',
                                'action' => 'help-and-learnmore',
                        )
                ),
               'communityas_sponsoredstory' =>array(
                        'route' => 'ads/sponsored-story/:action/:id',
                        'defaults' => array(
                                'module' => 'communityad',
                                'controller' => 'sponsored-story',
                                'action' => 'create',
                        )
                ),
        ),
);
