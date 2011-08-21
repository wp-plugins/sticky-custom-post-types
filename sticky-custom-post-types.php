<?php
/*
Plugin Name: Sticky Custom Post Types
Plugin URI: http://superann.com/sticky-custom-post-types/
Description: Enables a sticky checkbox on the admin add/edit page of custom post types which will allow the user to stick custom post type entries to the front page. Select custom post types in Settings &rarr; Writing.
Version: 1.1
Author: Ann Oyama
Author URI: http://superann.com
License: GPL2

Copyright 2011 Ann Oyama  (email : wordpress [at] superann.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function super_sticky_description() {
	echo '<p>'.__('Enable selected custom post types to stick to the front page.').'</p>';
}

function super_sticky_settings() {
	$post_types = get_post_types(array('_builtin' => false, 'public' => true), 'names');
	if(!empty($post_types)) {
		$checked_post_types = super_sticky_get();
		foreach($post_types as $post_type) { ?>
			<span><input type="checkbox" id="post_type_<?php echo $post_type; ?>" name="sticky_custom_post_types[]" value="<?php echo $post_type; ?>" <?php checked(in_array($post_type, $checked_post_types)); ?> /> <label for="post_type_<?php echo $post_type; ?>"><?php echo $post_type; ?></label></span><br/><?php
		}
	}
	else
		echo '<p>'.__('No public custom post types found.').'</p>';
}

function super_sticky_admin_init() {
	register_setting('writing', 'sticky_custom_post_types');
	add_settings_section('super_sticky_options', 'Sticky Custom Post Types', 'super_sticky_description', 'writing');
	add_settings_field('sticky_custom_post_types', 'Custom Post Types', 'super_sticky_settings', 'writing', 'super_sticky_options');
}

add_action('admin_init', 'super_sticky_admin_init', 20);

function super_sticky_get($posts=FALSE) {
	$post_types = get_option('sticky_custom_post_types');
	if(!is_array($post_types))
		$post_types = array();
	if($posts)
		$post_types[] = 'post';
	return $post_types;
}

function super_sticky_meta() { ?>
	<input id="super-sticky" name="sticky" type="checkbox" value="sticky" <?php checked(is_sticky($post->ID)); ?> /> <label for="super-sticky" class="selectit"><?php _e('Stick this to the front page') ?></label><?php
}

function super_sticky_add_meta_box() {
	foreach(super_sticky_get() as $post_type)
		if(current_user_can('edit_others_posts'))
			add_meta_box('super_sticky_meta', 'Sticky', 'super_sticky_meta', $post_type, 'side', 'high');
}

add_action('admin_init', 'super_sticky_add_meta_box');

function super_sticky_posts($query) {
	if(is_home() && (false === $query->query_vars['suppress_filters']))
		$query->set('post_type', super_sticky_get(TRUE));
	return $query;
}

add_filter('pre_get_posts', 'super_sticky_posts');
?>