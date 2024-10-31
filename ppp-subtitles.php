<?php
/*
Plugin Name: Post Promoter Pro - Subtitles
Plugin URI: https://www.postpromoterpro.com
Description: Integrates Post Promoter Pro with the Subtitles Plugin
Version: 1.0
Author: Chris Klosowski
Author URI: http://www.kungfugrep.com
License: GPLv2 or later
*/

class PPP_Subtitles {

	/**
	 * Registers our function to load our hooks and filters
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'add_all_the_things' ), 99 );
	}

	/**
	 * Registers our actions and filters in plugins loaded to be sure that Subtitles is loaded
	 */
	public function add_all_the_things() {

		add_action( 'ppp_share_on_publish', array( $this, 'save_subtitle' ), 1, 3 );
		add_filter( 'ppp_text_tokens', array( $this, 'ppp_subtitles_token' ), 10, 1 );
		add_filter( 'ppp_replace_token-post_subtitle', array( $this, 'ppp_subtitles_replace_token' ), 10, 2 );

	}

	/**
	 * Register the {post_subtitle} token for replacement
	 *
	 * @param  array $tokens The registered tokens
	 * @return array         The tokens with post_subtitle added
	 */
	public function ppp_subtitles_token( $tokens ) {

		$tokens[] = array( 'token' => 'post_subtitle', 'description' => __( 'Subtitle provided by the Subtitles plugin', 'ppp-txt' ) );

		return $tokens;

	}

	/**
	 * Runs the replacement of {post_subtitle} when publishing a post to a social network
	 *
	 * @param  string $string The text string being shared
	 * @param  array  $args   The arguements for this particular string
	 * @return string         The share string with {post_subtitle} replaced
	 */
	public function ppp_subtitles_replace_token( $string, $args ) {

		if ( !isset( $args['post_id'] ) ) {
			return $string;
		}

		return preg_replace( '"\{post_subtitle\}"', get_the_subtitle( (int)$args['post_id'] ), $string );

	}

	/**
	 * Verifies that the Post Subtitle is saved during the inital publish since post_meta isn't saved until after the save_post action is run
	 * @param  string $new_status The New status of the post
	 * @param  string $old_status The Old status of the post
	 * @param  object $post       The Post oject
	 * @return void
	 */
	public function save_subtitle( $new_status, $old_status, $post ) {

		if ( ! class_exists( 'Subtitles_Admin' ) ) { return; }

		$post_id = $post->ID;
		$subtitles_admin = Subtitles_Admin::getinstance();

		$subtitles_admin->update_subtitle_data( $post_id, $post, null );

	}

}

new PPP_Subtitles;
