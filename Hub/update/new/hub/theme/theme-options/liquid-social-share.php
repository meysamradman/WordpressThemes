<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$this->sections[] = array(
	'title'  => esc_html__( 'Social Share Providers', 'hub' ),
	'icon'   => 'el el-share',
	'fields' => array(
		array(
			'id'           => 'social-share-providers',
			'type'         => 'sorter',
			'title'        => esc_html__( 'Social Share Links', 'hub' ),
			'sortable'     => true,
			'group_values' => false,
			'options'      => array(
				'enabled'  => array(
					'facebook'  => esc_html__( 'Facebook', 'hub' ),
					'x'         => esc_html__( 'X (Twitter)', 'hub' ),
					'pinterest' => esc_html__( 'Pinterest', 'hub' ),
					'linkedin'  => esc_html__( 'Linkedin', 'hub' ),
				),
				'disabled' => array(
					'email'      => esc_html__( 'Email', 'hub' ),
					'bluesky'    => esc_html__( 'Bluesky', 'hub' ),
					'threads'    => esc_html__( 'Threads', 'hub' ),
					'whatsapp'   => esc_html__( 'Whatsapp', 'hub' ),
					'telegram'   => esc_html__( 'Telegram', 'hub' ),
					'viber'      => esc_html__( 'Viber', 'hub' ),
					'tumblr'     => esc_html__( 'Tumblr', 'hub' ),
					'hackernews' => esc_html__( 'Hackernews', 'hub' ),
					'reddit'     => esc_html__( 'Reddit', 'hub' ),
					'vk'         => esc_html__( 'VK.com', 'hub' ),
					'buffer'     => esc_html__( 'Buffer', 'hub' ),
					'xing'       => esc_html__( 'Xing', 'hub' ),
					'line'       => esc_html__( 'Line', 'hub' ),
					'instapaper' => esc_html__( 'Instapaper', 'hub' ),
					'pocket'     => esc_html__( 'Pocket', 'hub' ),
					'flipboard'  => esc_html__( 'Flipboard', 'hub' ),
					'weibo'      => esc_html__( 'Weibo', 'hub' ),
					'blogger'    => esc_html__( 'Blogger', 'hub' ),
					'baidu'      => esc_html__( 'Baidu', 'hub' ),
					'okru'       => esc_html__( 'Ok.ru', 'hub' ),
					'evernote'   => esc_html__( 'Evernote', 'hub' ),
					'skype'      => esc_html__( 'Skype', 'hub' ),
					'trello'     => esc_html__( 'Trello', 'hub' ),
					'diaspora'   => esc_html__( 'Diaspora', 'hub' ),
				),
			),
		),
	),
);
