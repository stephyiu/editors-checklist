<?php
/*
Plugin Name: Editor's Checklist
Plugin URI:
Description: Hides the 'Publish' button on the Edit Post page unless certain fields are completed, such as Headline, Excerpt, and Featured image. Users can set which fields are required in Settings > Editor's Checklist Options.
Author: crushgear
Version: 0.1
Author URI: http://hoppycow.com/
License: GPL2
*/

 class CG_Editors_Checklist {
    function __construct() {
    	add_action( 'post_submitbox_misc_actions', array( $this, 'add_editors_checklist' ) );
    	add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_print_styles' ) );
      add_action( 'admin_menu' , array( $this, 'add_checklist_menu' ) );
      add_action ( 'admin_init', array( $this, 'on_options_submit' ) );
      register_activation_hook( __FILE__, array( $this, 'activate_editors_checklist' ) );
    }

	public function enqueue_admin_scripts() {
      //only load this if on edit post screen
      $screen = get_current_screen();
      if ( 'post' != $screen->base ) {
        return;
      }

      //call scripts
    	$script_url = plugins_url( '/js/admin.js', __FILE__ );
    	wp_enqueue_script( 'pluginpug_admin_script', $script_url, array( 'jquery' ) );
	}

  public function enqueue_print_styles() {
    //only load this if in edit post screen
    $screen = get_current_screen();
    if ( 'post' != $screen->base ) {
      return;
    }
    
    // calls styles
    $plugin_style_url = plugins_url( '/styles.css', __FILE__ );
    wp_register_style( 'editors_checklist_styles', $plugin_style_url );
    wp_enqueue_style( 'editors_checklist_styles' );

  }

  public function add_editors_checklist( $post_ID ) {

      // only show this checklist on posts, not pages
      $post = get_post($post_ID);

      if ( $post->post_type == 'post') {

        //grab array from options table
        $shouldchecks = get_option( 'enablechecks' );

        //array of what should print in the publish metabox
     		$checklist = array(
     				array('questions'=>' Is there a headline?', 'names'=>'headlinecheck'),
     				array('questions'=>' Is there a featured image?', 'names'=>'featuredcheck'),
     				array('questions'=>' Is there at least one tag?', 'names'=>'tagcheck'),
            array('questions'=>' Is there at least one real category?', 'names'=>'catcheck'),
            array('questions'=>' Is there an excerpt?', 'names'=>'excerptcheck')

      			);

        echo "<div class='editorschecklist'>";

        //prints the checklist
      	foreach ( (array) $checklist as $item ) {
      		if( in_array( $item['names'], $shouldchecks ) ) {
           echo "<input name='" . esc_attr( $item['names'] ) . "' type='checkbox'/>" . esc_html( $item['questions'] ) . "<br/>";
          }
      	}

        echo "</div>";
      }

    }

  public function add_checklist_menu() {
    // creates the options page
     add_options_page('Checklist Options', "Editor's Checklist Options", 'manage_options', 'checklist-options', array( $this, 'editors_checklist_options' ) );
  }

  public function editors_checklist_options() {
    //puts content on the options page
      $shouldchecks = get_option( 'enablechecks' );

      echo '<div class="wrap">';
      echo "<h2>Editor's Checklist Settings</h2>";
      echo "<h4>This plugin hides the 'Publish' button on the Edit Post page unless certain fields are fulfilled. Please select which fields are required below.</h4>";

      $checklist_options = array(
        array( 'value'=>'headlinecheck', 'words'=>'Check for a headline?'),
        array( 'value'=>'featuredcheck', 'words'=>'Check for a featured image?'),
        array( 'value'=>'tagcheck', 'words'=>'Check for at least one tag?'),
        array( 'value'=>'catcheck', 'words'=>'Check for at least one real category?'),
        array( 'value'=>'excerptcheck', 'words'=>'Check for an excerpt?')

        );

      echo '<form method="post">';
      //adding invisible nonce check
        wp_nonce_field('submit_checklist_options','checklist_options');

        //checks for which boxes should already be checked based on what's stored in the options table
        foreach ( (array) $checklist_options as $item ) {
          $checked = ' ';
          if( in_array( $item['value'], $shouldchecks ) ) {
            $checked = 'checked';
          }
          // totally crazy echoing thing to print out a few darn checkboxes
          echo '<fieldset><input name="enablecheck[]" id="' . esc_attr( $item['value'] ) . '" type="checkbox" value="' . esc_attr( $item['value'] ) . '"' . $checked . ' /> <label for="'  . esc_attr( $item['value'] ) . '"> ' .  esc_html( $item['words'] ) . "</label></fieldset>" ;
        }

    submit_button();

    echo '</form>';
    echo '</div>';
  }

  public function on_options_submit() {
    if ( ! isset( $_POST['checklist_options'] ) || ! wp_verify_nonce( $_POST['checklist_options'], 'submit_checklist_options' ) ) {
      return;
    }

    // grab options data save it to enablecheck
    $enablecheck = $_POST['enablecheck'];

    // only save it if enablecheck has data to save
    if ($enablecheck){
      //josh's helpful sanitize array tip
      array_map( 'sanitize_key', $enablecheck );
      update_option('enablechecks', $enablecheck);
    }

  }

  public function activate_editors_checklist() {
    //when plugin is first installed, activate all checks
    //if plugin has previously been installed, maintain user's options
    $enablecheck = get_option('enablechecks');

    if (!$enablecheck) {
      $enablecheck = array('headlinecheck', 'featuredcheck', 'tagcheck', 'catcheck', 'excerptcheck' );
      update_option('enablechecks', $enablecheck);
    }
  }

}

$cg_editors_checklist = new CG_Editors_Checklist();
