<?php

return array(
		array(
				'title' => 'Advanced Event Upcoming',
				'description' => 'Displays the logged-in member\'s upcoming advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.home-upcoming',
				'isPaginated' => true,
				'requirements' => array(
					'viewer',
					'no-subject',
				),
				'adminForm' => array(
						'elements' => array(
								array(
										'Text',
										'title',
										array(
												'label' => 'Title'
										)
								),
								array(
										'Radio',
										'type',
										array(
												'label' => 'Show',
												'multiOptions' => array(
														'1' => 'Any upcoming advanced events.',
														'2' => 'Current member\'s upcoming advanced events.',
														'0' => 'Any upcoming events when member is logged out, that member\'s advanced events when logged in.',
												),
												'value' => '0',
										)
								),
						)
				),
		),
		array(
				'title' => 'Advanced Event Profile',
				'description' => 'Displays a member\'s advanced events on their profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-events',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Advanced Events',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'user',
				),
		),
		array(
				'title' => 'Advanced Event Profile Discussions',
				'description' => 'Displays a advanced event\'s discussions on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-discussions',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Discussions',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Location',
				'description' => 'Displays a advanced event\'s location on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-map',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Location',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Sponsors',
				'description' => 'Displays a advanced event\'s sponsors on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-sponsors',
				'isPaginated' => false,
				'defaultParams' => array(
						'title' => 'Sponsors',
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Calendar',
				'description' => 'Displays a recurence calendar on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-calendar',
				'isPaginated' => false,
				'defaultParams' => array(
						'title' => 'Calendar',
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Near Location',
				'description' => 'Displays a list of most nearest location events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-near-location',
				'isPaginated' => false,
				'defaultParams' => array(
						'title' => 'Nearest Location Events',
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
				'adminForm' => array(
		            'elements' => array(
		                array(
		                    'Text',
		                    'title',
		                    array(
		                        'label' => 'Title',
		                        'title' => 'Nearest Events',
		                    )
		                ),
		                array(
		                    'Text',
		                    'radius',
		                    array(
		                        'label' => 'Radius',
		                        'description' => 'Within the radius of distance (miles)',
		                        'value' => 500,
		                        'validators' => array(
		                        	array('Int', true),
		        					array('GreaterThan',true,array(0))
								),
		                    )
		                ),
		                array(
		                    'Text',
		                    'max',
		                    array(
		                        'label' => 'Max Item Count',
		                        'description' => 'Number of events in this widget.',
		                        'value' => 5,
		                        'validators' => array(
		                        	array('Int', true),
		        					array('GreaterThan',true,array(0))
								),
		                    )
		                ),
		            )
		        ),
		),
		array(
				'title' => 'Advanced Event Profile Related Events',
				'description' => 'Displays a list of related events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-related',
				'isPaginated' => false,
				'defaultParams' => array(
						'title' => 'Related Events',
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
				'adminForm' => array(
		            'elements' => array(
		                array(
		                    'Text',
		                    'title',
		                    array(
		                        'label' => 'Title',
		                        'title' => 'Related Events',
		                    )
		                ),
		                array(
		                    'Text',
		                    'max',
		                    array(
		                        'label' => 'Max Item Count',
		                        'description' => 'Number of events in this widget.',
		                        'value' => 5,
		                        'validators' => array(
		                        	array('Int', true),
		        					array('GreaterThan',true,array(0))
								),
		                    )
		                ),
		            )
		        ),
		),
	
		array(
				'title' => 'Advanced Event Profile Videos',
				'description' => 'Displays a advanced event\'s videos on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-videos',
				'isPaginated' => false,
				'defaultParams' => array(
						'title' => 'Videos',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Info',
				'description' => 'Displays a advanced event\'s info (creation date, member count, etc) on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-info',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title'=> 'Group Profile Events',
				'description' => 'Displays a group\'s events on the group profile page',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.group-profile-events',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Events',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'group',
				),
		),
		array(
				'title' => 'Advanced Event Profile Members',
				'description' => 'Displays a advanced event\'s members on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-members',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Guests',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Options',
				'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a advanced event on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-options',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Photo',
				'description' => 'Displays a advanced event\'s photo on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-photo',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Photos',
				'description' => 'Displays a advanced event\'s photos on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-photos',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Photos',
						'titleCount' => true,
				),
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile RSVP',
				'description' => 'Displays options for RSVP\'ing to an advanced event on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-rsvp',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Advanced Event Profile Status',
				'description' => 'Displays a advanced event\'s title on it\'s profile.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-status',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Popular Events',
				'description' => 'Displays a list of most viewed advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.list-popular-events',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Popular Events',
						'itemCountPerPage' => 5,
				),
				'requirements' => array(
						'no-subject',
				),
				'adminForm' => array(
						'elements' => array(
								array(
										'Radio',
										'popularType',
										array(
												'label' => 'Popular Type',
												'multiOptions' => array(
														'view' => 'Views',
														'member' => 'Members',
												),
												'value' => 'view',
										)
								),
						)
				),				
		),
		array(
				'title' => 'Most Attending',
				'description' => 'Displays a list of most attending advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.list-most-attending-events',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Most Attending',
				),
				'requirements' => array(
						'no-subject',
				),
				'adminForm' => array(
						'elements' => array(
								array(
										'Radio',
										'popularType',
										array(
												'label' => 'Popular Type',
												'multiOptions' => array(
														'view' => 'Views',
														'member' => 'Members',
												),
												'value' => 'view',
										)
								),
						)
				),
		),
		array(
				'isPaginated' => true,
				'title' => 'Most Rated',
				'description' => 'Displays a list of most rated advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.list-most-rated-events',
				'defaultParams' => array(
					'title' => 'Most Rated',
				),
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'isPaginated' => true,
				'title' => 'Most Liked',
				'description' => 'Displays a list of most liked advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.list-most-liked-events',
				'defaultParams' => array(
					'title' => 'Most Liked',
				),
				'requirements' => array(
					'no-subject',
				),
		),
		array(
				'title' => 'Recent Events',
				'description' => 'Displays a list of recently created advanced events.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.list-recent-events',
				'isPaginated' => true,
				'defaultParams' => array(
						'title' => 'Recent Events',
				),
				'requirements' => array(
						'no-subject',
				),
				'adminForm' => array(
						'elements' => array(
								array(
										'Radio',
										'recentType',
										array(
												'label' => 'Recent Type',
												'multiOptions' => array(
														'creation' => 'Creation Date',
														'modified' => 'Modified Date',
														'start' => 'Started',
														'end' => 'Ended',
												),
												'value' => 'creation',
										)
								),
						)
				),
		),
		array(
				'title' => 'Advanced Event Browse Search',
				'description' => 'Displays a search form in the advanced event browse page.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.browse-search',
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'title' => 'Advanced Event Browse Menu',
				'description' => 'Displays a menu in the advanced event browse page.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'title' => 'Advanced Event Browse Quick Menu',
				'description' => 'Displays a small menu in the advanced event browse page.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.browse-menu-quick',
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'title' => 'Advanced Event Profile Add Rates',
				'description' => 'Displays options for rates, like with Facebook, G+, Twitter to an advanced event on it\'s profile',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-add-rates',
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'title' => 'Advanced Event Profile Follow',
				'description' => 'Displays options for Following\'ing to an advanced event on it\'s profile',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-follow',
				'requirements' => array(
						'no-subject',
				),
		),
		array(
				'title' => 'Advanced Event Calendar',
				'description' => 'Show events in selected month. Click on the highlighted event day to see all events in that day',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.events-calendar',
				'requirements' => array(
						'subject' => 'ynevent',
				),
		),
		array(
				'title' => 'Featured Events',
				'description' => 'Displays featured events on front end.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.feature-events',
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of events to display',
			                'value' => '5',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),//adminForm
		),
		array(
				'title' => 'Advanced Event Profile Add This',
				'description' => 'Displays Add This Event.',
				'category' => 'Advanced Events',
				'type' => 'widget',
				'name' => 'ynevent.profile-addthis',
				'defaultParams' => array('title' => 'Add this', ),
		),
)
?>
