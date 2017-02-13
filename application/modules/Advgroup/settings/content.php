<?php
return array(
  array(
	'title' => 'Most Active Groups',
	'description' => 'Displays a list groups that have the most number of topics.',
	'category' => 'Advanced Groups',
	'type' => 'widget',
	'name' => 'advgroup.list-most-active-groups',
	'isPaginated' => true,
	'defaultParams' => array(
			'title' => 'Most Active Groups',
	),
  	'requirements' => array(
  		'no-subject',
  	),
	'adminForm' => array(
		'elements' => array(
			array(
				'Radio',
				'time',
				array(
					'label' => 'Posted date',
					'multiOptions' => array(
							'1' => '30 days ago',
							'2' => '60 days ago',
							'3' => '90 days ago',
					),
					'value' => '1',
				)
			),
		)
	),
  ),
  array(
    'title' => 'Group Directory',
    'description' => 'Displays all existing groups',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-directory',
  	'isPaginated' => true,
    'defaultParams' => array(
    	'title' => 'Group Directory',
    ),
  ),
  array(
    'title' => 'Group Quick Navigation',
    'description' => 'Display quick navagation',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-quick-navigation',
  	'isPaginated' => true,
    'defaultParams' => array(
    	//'title' => 'Group Directory',
    ),
  ),

  array(
    'title' => 'Groups Menu',
    'description' => 'Displays groups menu on the group Homepage.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-menu',
  ),
  array(
    'title' => 'Groups Search',
    'description' => 'Displays group search form on selected page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-search',
  ),
  array(
    'title' => 'Groups Listing',
    'description' => 'Displays groups listing on groups listing page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-listing',
  ),
  array(
    'title' => 'Featured Groups',
    'description' => 'Displays featured groups on groups home page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.featured-groups',
    'defaultParams' => array(
      'title' => '',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
		array(
				'title' => 'Activity Feed',
				'description' => 'Displays activity feed on groups profile page.',
				'category' => 'Advanced Groups',
				'type' => 'widget',
				'name' => 'advgroup.feed',
				'defaultParams' => array(
						'title' => "What's New",
				),
				'requirements' => array(
						'no-subject',
				),
		),
  array(
    'title' => 'Top Posters',
    'description' => 'Displays top posters of a group on groups profile page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-top-posters',
    'defaultParams' => array(
      'title' => 'Top Posters',
    ),
  ),
  array(
    'title' => 'Popular Groups',
    'description' => 'Displays a list of groups that have the most number of members or number of views( According to the option set).',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.list-popular-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Groups',
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
              'member' => 'Number of Members',
              'view' => 'Number of Views',
            ),
            'value' => 'member',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'New Groups',
    'description' => 'Displays a list of recently created groups.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.list-recent-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'New Groups',
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
            ),
            'value' => 'creation',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Active Groups',
    'description' => 'Displays a list groups that have the most number of topics.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.list-active-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Active Groups',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
    ),
  ),
  array(
    'title' => 'Profile Groups',
    'description' => 'Displays a member\'s groups on their profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-groups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Groups',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Group Profile Discussions',
    'description' => 'Displays a group\'s discussions on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-discussions',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Discussions',
      'titleCount'=>true,
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Profile Info',
    'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-info',
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
   array(
    'title' => 'Group Profile Members',
    'description' => 'Displays a group\'s members on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-members',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Members',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a group on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-options',
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Profile Photo',
    'description' => 'Displays a group\'s photo on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-photo',
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Profile Photos',
    'description' => 'Displays a group\'s photos on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-photos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Photos',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Profile Status',
    'description' => 'Displays a group\'s title on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-status',
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title'=> 'Group Profile Events',
    'description' => 'Displays a group\'s events on its profile',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-events',
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
    'title' => 'Profile Useful Links',
    'description' => 'Display useful links for group member on group profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-useful-links',
    'defaultParams' => array(
      'title' => 'Useful Links',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Group Profile Albums',
    'description' => 'Displays a group\'s albums on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-albums',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Albums',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
 array(
    'title' => 'Group Profile Announcements',
    'description' => 'Displays recent announcements.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-group-announcements',
    'requirements' => array(
      'subject'=>'group',
    ),
  ),
 array(
    'title' => 'Overall Statistic',
    'description' => 'Displays overall statistic of groups on group home page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.overall-statistic',
    'defaultParams' => array(
      'title' => 'Overall Statistic',
    ),
  ),
 array(
    'title'=> 'Group Profile Polls',
    'description' => 'Displays a group\'s polls on its profile',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-polls',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Polls',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
array(
    'title' => 'Group Profile Statistic',
    'description' => 'Displays statistic of a group on that group profile page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-statistic',
    'defaultParams' => array(
      'title' => 'Statistic',
    ),
  ),
array(
    'title' => 'Group Suggested Poll',
    'description' => 'Displays a random group\'s poll on its profile.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.suggested-poll',
    'requirements' => array(
      'title' => 'Suggested Poll',
      'subject' => 'group',
    ),
  ),
array(
    'title' => 'Group Categories',
    'description' => 'Displays group categories on group browse page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-category-search',
    'defaultParams' => array(
      'title' => 'Categories',
    ),
  ),
array(
	'title' => 'Sub Groups',
    'description' => 'Displays sub groups list on group profile page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.sub-groups',
),
 array(
    'title' => 'Recent Group Videos',
    'description' => 'Displays a list of videos that are recently posted on the group.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.recent-group-videos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Videos',
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
    'adminForm' => array(
       'elements' => array(
            array(
                  'Text',
                  'marginLeft',
                  array(
                      'label' => 'Margin left of each video',
                      'value' => '26',
                      'validators' => array(
                          array('Int', true),
                          array('GreaterThan', true, array(0)),
                      )
                  )
           ),
       ),
    ),
  ),
 array(
    'title'=> 'Group Profile Wikis',
    'description' => 'Displays a group\'s wiki pages on its profile',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.profile-wikis',
    'isPaginated' => true,
    'defaultParams' => array(
      'titleCount' => true,
       'title' => 'Wiki Pages'
    ),
    'requirements' => array(
      'subject' => 'group',
    ),
  ),
  array(
    'title' => 'Group Tags',
    'description' => 'Displays groups tags on group home page.',
    'category' => 'Advanced Groups',
    'type' => 'widget',
    'name' => 'advgroup.groups-tags',
      'defaultParams' => array(
      'title' => 'Tags',
    ),
  ),
  array(
        'title' => 'Groups Activities',
        'description' => 'Listing the public groups activities.',
        'category' => 'Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.group-activity',
        'isPaginated' => true,
        'defaultParams' => array(
          'title' => 'Groups Activities',
        ),
        'adminForm' => array(
           'elements' => array(
                array(
                      'Text',
                      'widget_height',
                      array(
                          'label' => 'Maximum height of the widget block(px)',
                          'value' => '450',
                          'validators' => array(
                              array('Int', true),
                              array('GreaterThan', true, array(-1)),
                          )
                      )
               ),
       ),
    )
  ),
  array(
        'title' => 'Most Active Members',
        'description' => 'Listing the most active group members',
        'category' => 'Advanced Groups',
        'type' => 'widget',
        'name' => 'advgroup.group-top-members',
        'defaultParams' => array(
          'title' => 'Most Active Members',
        ),
        'adminForm' => array(
           'elements' => array(
                array(
                      'Text',
                      'number',
                      array(
                          'label' => 'How many members will be shown',
                          'value' => '5',
                          'validators' => array(
                              array('Int', true),
                              array('GreaterThan', true, array(0)),
                          )
                      )
               ),
       ),
     )
 ),
)
?>
