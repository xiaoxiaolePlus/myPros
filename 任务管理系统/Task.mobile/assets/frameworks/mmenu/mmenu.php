<?php
/**
 * Plugin Name: App look-alike menus
 * Plugin URI: http://mmenu.frebsite.nl/wordpress-plugin.html
 * Description: The best WordPress plugin for app look-alike off-canvas mega menus with sliding submenus for your WordPress website.
 * Version: 2.5.0
 * Author: Fred Heusschen
 * Author URI: http://www.frebsite.nl
 */


require_once( dirname( __FILE__ ) . '/php/mm_adminpage.php' );
require_once( dirname( __FILE__ ) . '/php/wp_autoupdate.php' );



class MmenuAdminPage extends MmAdminPage {
	
	protected $name			= 'mmenu';
	protected $version 		= '2.5.0';
	protected $screen_id 	= 'toplevel_page_mmenu';

	protected $options = array(
		'mm_setup' => array(
			'version',
			'menu',
			'button'
		),
		'mm_menu' => array(
			'position',
			'backgroundcolor',
			'theme',
			'breakpoint'
		),
		'mm_header'	=> array(
			'navigate',
			'navigate_title',
			'navigate_close',

			'image',
			'image_src',
			'image_scale',
			'image_height',

			'searchfield',
			'searchfield_sitesearch',
			'searchfield_placeholder',
			'searchfield_noresults',

			'buttons_html',
			'buttons_selector',
			'button_1_icon', 'button_1_href', 'button_1_target',
			'button_2_icon', 'button_2_href', 'button_2_target',
			'button_3_icon', 'button_3_href', 'button_3_target',
			'button_4_icon', 'button_4_href', 'button_4_target',
			'button_5_icon', 'button_5_href', 'button_5_target'
		),
		'mm_footer'	=> array(
			'buttons_html',
			'buttons_selector',
			'button_1_icon', 'button_1_href', 'button_1_target',
			'button_2_icon', 'button_2_href', 'button_2_target',
			'button_3_icon', 'button_3_href', 'button_3_target',
			'button_4_icon', 'button_4_href', 'button_4_target',
			'button_5_icon', 'button_5_href', 'button_5_target'
		),
		'mm_accessibility' => array(
			'screenreader',
			'keyboard',
			'rtl'
		),
		'mm_advanced' => array(
			'pagedim',
			'pageshadow',
			'slidemenu',
			'indentborder',
			'truncatelistitems',
			'counters',
			'fullsubopen',
			'fullscreen'
		)
	);



	public function __construct()
	{
		parent::__construct();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'header_image_init' ) );
		add_action( 'admin_init', array( $this, 'autoupdate_init' ) );
	}



	/*
		The menu item + page
	*/
	public function add_menu_page()
	{
		add_menu_page( 
			'mmenu',
			'mmenu',
			'manage_options',
			'mmenu',
			array( $this, 'create_admin_page' ),
			'dashicons-menu'
		);
	}

	public function plugin_help( $contextual_help, $screen_id, $screen )
	{
		$this->helptabs = array(
			array(
				'id'        => 'mmenu-help-menu',
				'title'     => __( 'Locate the menu', 'mmenu' ),
				'content'   => '
					<p><strong>' 
						. __( 'Locate the menu', 'mmenu' ) . '</strong><br />'
						. __( 'All menus used by your theme are automatically listed in the "selector" combobox. If the menu you\'re looking for isn\'t listed, you can either specify it manually or use the "locate on website" button.', 'mmenu' ) . '</p>
					<p>' . __( 'The "locate on website" popup makes an educated guess about what HTML element might be the menu, using common HTML patterns used by WordPress.<br />'
						. 'If that also does not find the menu, you can still specify it manually by typing the selector (for example "#my-menu") in the "selector" combobox.', 'mmenu' ) . '</p>'
			),
			array(
				'id'        => 'mmenu-help-button',
				'title'     => __( 'Locate the button', 'mmenu' ),
				'content'   => '
					<p><strong>'
						. __( 'Locate the menu button', 'mmenu' ) . '</strong><br />'
						. __( 'All menu buttons used by your theme are automatically listed in the "selector" combobox. If the button you\'re looking for isn\'t listed, you can either specify it manually or use the "locate on website" button.', 'mmenu' ) . '</p>
					<p>' . __( 'The "locate on website" popup makes an educated guess about what HTML element might be the menu button, using common HTML patterns used by WordPress.<br />'
						. 'If that also does not find the button, you can still specify it manually by typing the selector (for example "#my-button") in the "selector" combobox', 'mmenu' ) . '.</p>'
			),
			array(
				'id'        => 'mmenu-help-icons',
				'title'     => __( 'Help and suboptions', 'mmenu' ) ,
				'content'   => '
					<p><strong>'
						. __( 'Help', 'mmenu' ) . '</strong><br />'
						. __( 'Click on the "help"-icon next to an option (if present) to reveal additional information about the option.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Suboptions', 'mmenu' ) . '</strong><br />'
						. __( 'Click on the "gear"-icon next to an option (if present) to reveal additional (sub)options.', 'mmenu' ) . '</p>'
			),
			array(
				'id'        => 'mmenu-help-styling',
				'title'     => __( 'Additional styling', 'mmenu' ),
				'content'   => '
					<p><strong>' 
						. __( 'Vertical submenu', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Vertical" to a menu item if you want its submenu to expand below it.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Spacers', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Spacer" to a menu item if you want it to have more whitespace at the top.', 'mmenu' ) . '</p>
					<p><strong>'
						. __( 'Fixed elements', 'mmenu' ) . '</strong><br />'
						. __( 'Add the classname "Fixed" to a fixed element on your webpage if you want it to move out of view when opening the menu.', 'mmenu' ) . '</p>'
			)
	    );

		parent::plugin_help( $contextual_help, $screen_id, $screen );
	}

	public function create_admin_page()
	{

	    parent::create_admin_page();

	    $this->checkWritable();

	    add_thickbox();


	    $updated 	= isset( $_SESSION[ 'submit' ] );
	    $preview 	= isset( $_SESSION[ 'preview' ] );
	    $first 		= get_option( 'mm_setup', false );
		$first 		= !is_array( $first );
		$version	= get_option( 'mm_setup', array() );
		$version 	= isset( $version[ 'version' ] ) ? $version[ 'version' ] : 0;

		if ( $updated )
		{
			unset( $_SESSION[ 'submit' ] );
			$this->saveFrontend();
			$this->echo_updated( __( 'Settings have been saved and published.', 'mmenu' ) );
		}
		if ( $preview )
		{
			unset( $_SESSION[ 'preview' ] );
			$this->saveFrontend( '-preview' );
		}

		echo '
		<div class="wrap' . ( $preview ? ' mmenu-preview' : '' ) . ( $first ? ' mmenu-setup' : '' ) . '">';

		$this->echo_title( '<span>mmenu</span> ' . __( 'App look-alike menu for WordPress', 'mmenu' ) . ' <small>'
			. __( 'Version', 'mmenu' ) . ' ' . $this->version . '</small>', 'mmenu' );


		$mm_setup 			= get_option( 'mm_setup'			, array() );
		$mm_menu 			= get_option( 'mm_menu'				, array() );
		$mm_header 			= get_option( 'mm_header'			, array() );
		$mm_footer 			= get_option( 'mm_footer'			, array() );
		$mm_accessibility	= get_option( 'mm_accessibility'	, array() );
		$mm_advanced 		= get_option( 'mm_advanced'			, array() );


		$this->echo_form_opener( 'mmenu-settings' );


		if ( $preview )
		{
			echo '
			<div class="phone">
				<iframe src="' . get_home_url() . '?mmenu=preview"></iframe>
			</div>
			<p class="submit-preview">
				<strong>' . __( 'Happy with the result?', 'mmenu' ) . '</strong><br />
				<input type="submit" name="submit" value="' . __( 'Yes, publish it!', 'mmenu' ) . '" class="button button-primary button-large" /><br />
				<br />
				<a href="#mmenu-settings">' . __( 'Not yet, I need to make some more changes.', 'mmenu' ) . '</a>
			</p>';
		}

		echo '
			<input name="mm_setup[version]" value="' . ( $version + 1 ) . '" type="hidden" />';


		$this->opt_setup( 			$first, $mm_setup  			);
		$this->opt_menu( 			$first, $mm_menu   			);
		$this->opt_header( 			$first, $mm_header 			);
		$this->opt_footer( 			$first, $mm_footer			);
		$this->opt_accessibility( 	$first, $mm_accessibility 	);
		$this->opt_advanced(	 	$first, $mm_advanced 		);


		if ( $first )
		{
			echo '
				<p class="submit">
					<a href="#" class="next button button-primary button-large">' . __( 'Next', 'mmenu' ) . '</a>
					<input type="submit" name="proceed" value="' . __( 'Save & proceed', 'mmenu' ) . '" class="button button-primary button-large" />
				</p>';
		}
		else
		{
			echo '
				<p class="submit-fixed">
					<input type="submit" name="preview" value="' . __( 'Save & preview', 'mmenu' ) . '" class="button" />
					<input type="submit" name="submit" value="' . __( 'Save & publish', 'mmenu' ) . '" class="button button-primary button-large" />
				</p>';
		}

		$this->echo_form_closer();
		$this->locate_popup();

		echo '
		</div>';
	}

	protected function opt_setup( $first, $mm_setup )
	{
		echo '
			<h2>' . __( 'Setup', 'mmenu' ) . '</h2>';

		if ( $first )
		{
			echo '
			<p class="intro"><strong>' . __( 'Great!', 'mmenu' ) . '</strong> ' . __( 'You\'ve successfully downloaded and installed the mmenu WordPress plugin.', 'mmenu' ) . '</p>';

			$this->echo_section_opener();
			echo '
			<p>' . __( 'You are only a few clicks away from creating an app look-alike menu.<br />'
				. 'But first, we need to locate some elements on the website.', 'mmenu' ) . '</p>';
			$this->echo_section_closer();
		}

		$this->opt_setup_menu_selector( $first, $mm_setup );
		$this->opt_setup_button_selector( $first, $mm_setup );
	}
	protected function opt_setup_menu_selector( $first, $mm_setup )
	{
		$inp = $this->selectorInput( 'menu', $mm_setup, 'mm_setup', 'menu', __( 'Locate the menu', 'mmenu' ) );
		if ( $first )
		{
			$inp .= '
				<span class="first-visit-helper fvh-1">' . __( 'Type a selector,'				, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-2">' . __( 'Choose one from the combobox,'	, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-3">' . __( 'Or locate it on the website.'	, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-4">' . __( 'Click the "help"-icon for help.', 'mmenu' ) . '</span>';
		}

		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Locate the menu', 'mmenu' ) . '<br />
				<small>' . __( 'Specify a CSS selector that targets the menu container.', 'mmenu' ) . '</small>',
			$inp,
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'The selector should target the element surrounding the main <code>UL</code>.<br />'
				. 'For example, the HTML below results in the selector <code>#my-menu</code>.', 'mmenu' ) . '</p>
<pre>&lt;nav id="my-menu"&gt;
   &lt;ul&gt;
      &lt;li&gt;&lt;a href="/"&gt;Home&lt;/a&gt;&lt;/li&gt;
   &lt;/ul&gt;
&lt;/nav&gt;</pre>',
			'explanation'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_setup_button_selector( $first, $mm_setup )
	{
		$inp = $this->selectorInput( 'button', $mm_setup, 'mm_setup', 'button', __( 'Locate the button', 'mmenu' ) );
		if ( $first )
		{
			$inp .= '
				<span class="first-visit-helper fvh-1">' . __( 'Type a selector,'				, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-2">' . __( 'Choose one from the combobox,'	, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-3">' . __( 'Or locate it on the website.'	, 'mmenu' ) . '</span>
				<span class="first-visit-helper fvh-4">' . __( 'Click the "help"-icon for help.', 'mmenu' ) . '</span>';
		}
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Locate the menu button', 'mmenu' ) . '<br />
				<small>' . __( 'Specify a CSS selector that targets an anchor or a button.', 'mmenu' ) . '</small>',
			$inp,
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'The selector should target an anchor (<code>&lt;a /&gt;</code>) or a button (<code>&lt;button /&gt;</code>) for opening the menu.<br />'
				. 'For example, the HTML below results in the selector <code>#my-button</code>.', 'mmenu' ) . '</p>

<pre>&lt;a id="my-button" href="#my-menu"&gt;open menu&lt;/a&gt;</pre>

				<p>' . __( 'If it doesn\'t yet look like a hamburger icon, you\'ll have to <a target="_blank" href="http://css-tricks.com/three-line-menu-navicon">do that yourself</a>.', 'mmenu' ) . '</p>',
			'explanation'
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	protected function opt_menu( $first, $mm_menu )
	{
		if ( $first )
		{
			echo '
				<input type="hidden" name="mm_menu[position]" value="left" />
				<input type="hidden" name="mm_menu[backgroundcolor]" value="#f3f3f3" />
				<input type="hidden" name="mm_menu[breakpoint]" value="Never" />';
		}
		else
		{
			echo '
			<h2>Menu options</h2>';

			$this->opt_menu_position( $mm_menu );
			$this->opt_menu_background( $mm_menu );
			$this->opt_menu_breakpoint( $mm_menu );
		}
	}

	protected function opt_menu_position( $mm_menu )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Position the menu', 'mmenu' ) . '<br />
				<small>' . __( 'Select how to position the menu; at the left, at the bottom, at the right or at the top side of the page.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_menu, 'mm_menu', 'position' ),
				array(
					'left' 		=> __( 'At the left'	, 'mmenu' ),
					'bottom'	=> __( 'At the bottom'	, 'mmenu' ),
					'right'		=> __( 'At the right'	, 'mmenu' ),
					'top'		=> __( 'At the top'		, 'mmenu' )
				)
			)
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_menu_background( $mm_menu )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Choose a background color', 'mmenu' ) . '<br />
				<small>' . __( 'Any color will do. Dark, light, black or white, the menu will always look good.', 'mmenu' ) . '</small>',
			$this->html_input( array( $mm_menu,'mm_menu', 'backgroundcolor' ) ) .
			$this->html_input( array( $mm_menu,'mm_menu', 'theme' ), 'hidden' )
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_menu_breakpoint( $mm_menu )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'The menu on wider screens', 'mmenu' ) . '<br />
				<small>' . __( 'As of what screen width should the mobile menu always be visible?', 'mmenu' ) . '</small>',
			'<span class="combobox is-combobox">' . $this->html_input( array( $mm_menu, 'mm_menu', 'breakpoint' ), 'text', 'placeholder="1400px"' ) . '
				<select id="breakpoint_select">
					<option value=""></option>
					<option value="' . __( 'Never', 'mmenu' ) . '">' . __( 'Never show the mobile menu on wider screens', 'mmenu' ) . '</option>
					<optgroup label="' . __( 'Common breakpoints', 'mmenu' ) . '">
						<option value="768px">768px</option>
						<option value="1224px">1224px</option>
						<option value="1824px">1824px</option>
					</optgroup>
					<optgroup id="theme_breakpoints_list" label="' . __( 'Defined by the theme', 'mmenu' ) . '"></optgroup>
				</select>
			</span>',
			null,
			'help'
		);

		//	explanation
		$this->echo_form_table_row(
			'',
			'<p>' . __( 'Type or select a <code>min-width</code> (in pixels). When the visitor resizes his screen larger than the given width, the mobile menu will become visible on the left next to the page.<br />'
				. 'For example, typing <code>1400</code> will result in the media query <code>screen and (min-width: 1400px)</code>.', 'mmenu' ) . '</p>
			<br />
			<p>' . __( 'If you don\'t want the mobile menu to always be visible on wider screens, empty the value or select the "Never" option.', 'mmenu' ) . '</p>',
			'explanation'
		);
		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	protected function opt_header( $first, $mm_header )
	{
		if ( $first )
		{
			echo '
				<input type="hidden" name="mm_header[navigate]" value="button" />
				<input type="hidden" name="mm_header[image]" value="no" />
				<input type="hidden" name="mm_header[searchfield]" value="no" />';
		}
		else
		{
			echo '
			<h2>' . __( 'Header options', 'mmenu' ) . '</h2>';

			$this->opt_header_navigate( $mm_header );
			$this->opt_header_image( $mm_header );
			$this->opt_header_searchfield( $mm_header );
			$this->opt_header_buttons( $mm_header );
		}
	}
	protected function opt_header_navigate( $mm_header )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Select how to navigate', 'mmenu' ) . '<br />
				<small>' . __( 'Select how to navigate between different levels in the menu.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'navigate' ),
				array(
					'button'		=> __( 'With a back-button'							, 'mmenu' ),
					'breadcrumbs'	=> __( 'With breadcrumbs'							, 'mmenu' ),
					'iconpanels'	=> __( 'By showing a small part of the parent level', 'mmenu' ),
					'dropdown'		=> __( 'With dropdown submenus'						, 'mmenu' )
				), 'button'
			),
			null,
			true
		);

		//	header close option
		$this->echo_form_table_row(
			'',
			$this->html_checkbox( array( $mm_header, 'mm_header', 'navigate_close' ) ) .
				'<label for="mm_header_navigate_close">' 
				. __( 'Add a button that closes the menu.', 'mmenu' ) . '</label>',
			'suboptions'
		);

		//	header title option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_navigate_title">'
				. __( 'Specify the title above the main <code>UL</code>:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header, 'mm_header', 'navigate_title' ), 'text', 'placeholder="Menu"' ),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_header_image( $mm_header )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Add a logo or photo', 'mmenu' ) . '<br />
				<small>' . __( 'Personalize the menu by prepending it with a company logo or maybe a photo of yourself.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'image' ),
				array(
					'no'	=> __( 'No thanks'		, 'mmenu' ),
					'yes'	=> __( 'Yes please!'	, 'mmenu' )
				), 'yes'
			) . $this->html_input( array( $mm_header, 'mm_header', 'image_src' ), 'hidden' ),
			null,
			true
		);

		//	header image size option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_image_scale">'
				. __( 'Specify how the image should be scaled.', 'mmenu' ) . '</label><br />' .
				$this->html_select( array( $mm_header, 'mm_header', 'image_scale' ),
					array(
						'contain'	=> __( 'Scale down the image to fit in the available space.'	, 'mmenu' ),
						'cover' 	=> __( 'Strech out the image to cover up the available space.'	, 'mmenu' )
					)
				),
			'suboptions'
		);

		//	header image height option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_image_height">'
				.__( 'Specify the preferred height for the image.', 'mmenu' ) . '<br />
				<small>' . __( 'Note that adding a searchfield or buttons will decrease the available height.', 'mmenu' ) . '</small></label><br />' .
				$this->html_select( array( $mm_header, 'mm_header', 'image_height' ),
					array(
						'4' => '160px',
						'3' => '120px',
						'2' => '80px',
						'1'	=> '40px'
					)
				),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_header_searchfield( $mm_header )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();
		$this->echo_form_table_row(
			__( 'Add a searchfield', 'mmenu' ) . '<br />
				<small>' . __( 'Enable your visitors to search through the menu items by prepending a searchfield to the menu.', 'mmenu' ) . '</small>',
			$this->html_radio_preview(
				array( $mm_header, 'mm_header', 'searchfield' ),
				array(
					'no'	=> __( 'No thanks'		, 'mmenu' ),
					'yes'	=> __( 'Yes please!'	, 'mmenu' )
				), 'yes'
			),
			null,
			true
		);

		//	search site option
		$this->echo_form_table_row(
			'',
			$this->html_checkbox( array( $mm_header, 'mm_header', 'searchfield_sitesearch' ) ) .
				'<label for="mm_header_searchfield_sitesearch">'
					. __( 'Add a submit button to search the website.', 'mmenu' ) . '</label>',
			'suboptions'
		);

		//	placeholder option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_searchfield_placeholder">'
				. __( 'Specify the placeholder text for the searchfield:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header,'mm_header', 'searchfield_placeholder' ), 'text', 'placeholder="Search"' ),
			'suboptions'
		);

		//	no results option
		$this->echo_form_table_row(
			'',
			'<label for="mm_header_searchfield_noresults">'
				. __( 'Specify the text (or HTML) to show when no results are found:', 'mmenu' ) . '</label><br />' .
				$this->html_input( array( $mm_header, 'mm_header', 'searchfield_noresults' ), 'text', 'placeholder="No results found."' ),
			'suboptions'
		);

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}
	protected function opt_header_buttons( $mm_header )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();

		$this->opt_buttons( $mm_header, 'mm_header' );

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	protected function opt_footer( $first, $mm_footer )
	{
		if ( !$first )
		{
			echo '
			<h2>' . __( 'Footer options', 'mmenu' ) . '</h2>';

			$this->opt_footer_buttons( $mm_footer );
		}
	}
	protected function opt_footer_buttons( $mm_footer )
	{
		$this->echo_section_opener();
		$this->echo_form_table_opener();

		$this->opt_buttons( $mm_footer, 'mm_footer' );

		$this->echo_form_table_closer();
		$this->echo_section_closer();
	}

	protected function opt_accessibility( $first, $mm_accessibility )
	{
		if ( $first )
		{
			echo '
				<input type="hidden" name="mm_accessibility[screenreader]" value="yes" />
				<input type="hidden" name="mm_accessibility[keyboard]" value="yes" />
				<input type="hidden" name="mm_accessibility[rtl]" value="yes" />';
		}
		else
		{
			echo '
			<h2>Accessibility</h2>
			<p class="intro">'
				. __( 'Some options to increase the accessibility of your menu.', 'mmenu' ) . '</p>';

			$this->echo_section_opener();

			echo '
			<div class="advanced-options">';

			$this->opt_accessibility_screenreader( 	$mm_accessibility 	);
			$this->opt_accessibility_keyboard( 		$mm_accessibility	);
			$this->opt_accessibility_rtl( 			$mm_accessibility	);

			echo '
			</div>';

			$this->echo_section_closer();
		}
	}
	protected function opt_accessibility_screenreader( $mm_accessibility )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_accessibility, 'mm_accessibility', 'screenreader' ) ) .
					'<label for="mm_accessibility_screenreader">'
						. __( 'Add support for screen readers.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_accessibility_keyboard( $mm_accessibility )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_accessibility, 'mm_accessibility', 'keyboard' ) ) .
					'<label for="mm_accessibility_keyboard">'
						. __( 'Enable navigating with a keyboard.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_accessibility_rtl( $mm_accessibility )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_accessibility, 'mm_accessibility', 'rtl' ) ) .
					'<label for="mm_accessibility_rtl">'
						. __( 'Auto-detect RTL languages.', 'mmenu' ) . '</label>' .
				'</div>';
	}

	protected function opt_advanced( $first, $mm_advanced )
	{
		if ( $first )
		{
			echo '
				<input type="hidden" name="mm_advanced[pageshadow]" value="yes" />
				<input type="hidden" name="mm_advanced[slidemenu]" value="yes" />
				<input type="hidden" name="mm_advanced[indentborder]" value="yes" />
				<input type="hidden" name="mm_advanced[truncatelistitems]" value="yes" />
				<input type="hidden" name="mm_advanced[counters]" value="yes" />
				<input type="hidden" name="mm_advanced[fullsubopen]" value="yes" />';
		}
		else
		{
			echo '
			<h2>' . __( 'Advanced options', 'mmenu' ) . '</h2>
			<p class="intro">'
				. __( 'Some options to finetune the look and feel of your menu.', 'mmenu' ) . '</p>';

			$this->echo_section_opener();

			echo '
			<div class="advanced-options">';

			$this->opt_advanced_pageshadow( 		$mm_advanced );
			$this->opt_advanced_pagedim( 			$mm_advanced );
			$this->opt_advanced_slidemenu( 			$mm_advanced );
			$this->opt_advanced_indentborder( 		$mm_advanced );
			$this->opt_advanced_truncatelistitems( 	$mm_advanced );
			$this->opt_advanced_counters( 			$mm_advanced );
			$this->opt_advanced_fullsubopen( 		$mm_advanced );
			$this->opt_advanced_fullscreen( 		$mm_advanced );

			echo '
			</div>';

			$this->echo_section_closer();
		}
	}
	protected function opt_advanced_pageshadow( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'pageshadow' ) ) .
					'<label for="mm_advanced_pageshadow">'
						. __( 'Add a shadow to the page.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_pagedim( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'pagedim' ) ) .
					'<label for="mm_advanced_pagedim">'
						. __( 'Dim out the page to black.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_slidemenu( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'slidemenu' ) ) .
					'<label for="mm_advanced_slidemenu">'
						. __( 'Slide out the menu a bit.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_indentborder( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'indentborder' ) ) .
					'<label for="mm_advanced_indentborder">'
						. __( 'Indent the menu item borders.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_truncatelistitems( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'truncatelistitems' ) ) .
					'<label for="mm_advanced_truncatelistitems">'
						. __( 'Truncate menu items to a single line.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_counters( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'counters' ) ) .
					'<label for="mm_advanced_counters">'
						. __( 'Add a counter for submenus.', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_fullsubopen( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'fullsubopen' ) ) .
					'<label for="mm_advanced_fullsubopen">'
						. __( '&lt;a href="#"&gt; opens submenu', 'mmenu' ) . '</label>' .
				'</div>';
	}
	protected function opt_advanced_fullscreen( $mm_advanced )
	{
		echo '
				<div>' . 
					$this->html_checkbox( array( $mm_advanced, 'mm_advanced', 'fullscreen' ) ) .
					'<label for="mm_advanced_fullscreen">'
						. __( 'Open the menu fullscreen.', 'mmenu' ) . '</label>' .
				'</div>';
	}


	protected function locate_popup()
	{
		require_once 'lib/locate/locate-popup.php';
	}

	protected function selectorInput( $id, $optn, $ostr = 'mm_setup', $type = null, $titl = null, $plch = null )
	{
		$plch = ( $plch ) ? $plch : '#my-' . $id;
		$type = ( $type ) ? $type : $id;
		$titl = ( $titl ) ? $titl : sprintf( __( 'Locate the %s', 'mmenu' ), $id );

		return '
			<p class="combobox_locate">
				<span class="combobox">' 
					. $this->html_input( array( $optn, $ostr, $id ), 'text', 'placeholder="' . $plch . '"' ) . '
					<select id="' . $id . '_select"></select>
				</span>
				<a href="#TB_inline?width=600&height=500&inlineId=locate" class="button locate thickbox" data-type="' . $type . '" data-title="' . $titl . '">'
					. __( 'Locate on the website', 'mmenu' ) . '</a></p>
			<p class="selector-warning">
				<strong>' 
					. __( 'Uh oh...', 'mmenu' ) . '</strong><br />'
					. __( 'This selector targets more than one element, only the first will be used.', 'mmenu' ) . '<br />
				<a class="button dismiss" href="#">' 
					. __( 'OK, I understand', 'mmenu' ) . '</a></p>
			<p class="selector-error">
				<strong>' 
					. __( 'Oops!', 'mmenu' ) . '</strong><br />'
					. __( 'No element found with this selector! Are you sure it is correct?', 'mmenu' ) . '<br />
				<a class="button dismiss" href="#">'
					. __( 'Yes, I\'m sure', 'mmenu' ) . '</a></p>';
	}

	protected function opt_buttons( $optn, $ostr = 'mm_header' )
	{
		$this->echo_form_table_row(
			__( 'Add buttons', 'mmenu' ) . '<br />
				<small>' . __( 'Type some HTML, specify a jQuery selector that targets a single or multiple anchors and/or create buttons manually.', 'mmenu' ) . '</small>',

			'<div class="buttons_html">' .
				$this->html_input( array( $optn, $ostr, 'buttons_html' ), 'text', 'placeholder="&lt;a href=&quot;/&quot;&gt;Home&lt;/a&gt;"' ) .
			'</div>' .
			$this->selectorInput( 'buttons_selector', $optn, $ostr, 'anchors', __( 'Locate the buttons', 'mmenu' ), 'ul.buttons a' ) .
			'<div class="buttons">' .
				$this->createButtonHead() . 
				$this->createButton( 'button_1', $optn, $ostr ) . 
				$this->createButton( 'button_2', $optn, $ostr ) .
				$this->createButton( 'button_3', $optn, $ostr ) .
				$this->createButton( 'button_4', $optn, $ostr ) .
				$this->createButton( 'button_5', $optn, $ostr ) .
				$this->createButtonFoot() .
			'</div>'
		);
	}
	protected function createButton( $id, $optn, $ostr )
	{
		$icn = ( isset( $optn[ $id . '_icon' ] ) )
			? ' dashicons ' . $optn[ $id . '_icon' ]
			: '';

		return '
			<div class="buttons-button">
				<div data-target="#' . $ostr . '_' . $id . '_icon' . '" class="button dashicons-picker' . $icn . '"></div>
				' . $this->html_input( 	array( $optn, $ostr, $id . '_icon' )	, 'hidden' ) . '
				' . $this->html_input( 	array( $optn, $ostr, $id . '_href' )	, 'text', 'placeholder="http://website.com"' ) . '
				' . $this->html_select( array( $optn, $ostr, $id . '_target' )	,
						array(
							'_self'		=> '_self',
							'_blank' 	=> '_blank'
						)
					) . '
				<a href="#" class="dashicons dashicons-no"></a>
			</div>';
	}
	protected function createButtonHead()
	{
		return '
			<div class="buttons-head">
				<span>href:</span>
				<span>target:</span>
			</div>';
	}
	protected function createButtonFoot()
	{
		return '
			<div class="buttons-foot">
				<a href="#" class="button">'
					. __( 'Add button', 'mmenu' ) . '</a>
			</div>';
	}

	protected function checkWritable()
	{
		$dir = dirname( __FILE__ ) . '/';
		$str = 'wp-content/plugins/mmenu/';
		$err = array();

		foreach(
			array( 
				'css/mmenu.css',
				'css/mmenu-preview.css',
				'js/mmenu.js',
				'js/mmenu-preview.js'
			) as $file
		) {
			if ( !is_writable( $dir . $file ) )
			{
				$err[] = '<p>' . sprintf( __( 'The file <strong>%s</strong> is not writable, you need to chmod its permissions to at least 664.', 'mmenu' ), $str . $file ) . '</p>';
			}
		}

		if ( count( $err ) > 0 )
		{
	        echo '
	        	<div class="error">' . implode( '', $err ) . '</div>';
		}
	}



	/*
		Change text on media-upload
	*/
	public function header_image_init()
	{
		global $pagenow;
	    if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow )
	    {
	        add_filter( 'gettext', array( $this, 'header_image_button_text' ), 1, 3 );
	    }
	}
	public function header_image_button_text( $translated_text, $text, $domain )
	{
		if ( 'Insert into Post' == $text )
		{
			$referer = strpos( wp_get_referer(), 'mmenu-header-image' );
			if ( $referer != '' )
			{
				return __( 'Use this image!', 'mmenu' );
			}
		}
		return $translated_text;
	}



	/*
		Auto update API
	*/
	public function autoupdate_init()
	{
		$url  = 'http://mmenu.frebsite.nl/inc/__wp__update.php';
		$file = dirname( __FILE__ ) . '/LICENSE-key.txt';

		//	To prevent unneeded API calls, only proceed if a key is present
		if ( file_exists( $file ) )
		{
			if ( $key = @file_get_contents( $file ) )
			{
			    new wp_auto_update(
			    	$url,
			    	plugin_basename( __FILE__ ),
			    	$this->version, 
			    	trim( $key )
			    );
			}
		}
	}



	/*
		Save the frontend .js and .css file
	*/
	protected function sanitizeStr( $str )
	{
		$str = str_replace( "'", "\'", $str );
		return $str;
	}
	protected function saveFrontend( $fileAffix = '' )
	{
		$mm = array();
		foreach( $this->options as $option => $suboptions )
		{
			$k = substr( $option, 3 );
			$mm[ $k ] = get_option( $option );
			if ( !isset( $mm[ $k ] ) )
			{
				$mm[ $k ] = array();
			}
			foreach( $suboptions as $suboption )
			{
				if ( !isset( $mm[ $k ][ $suboption ] ) )
				{
					$mm[ $k ][ $suboption ] = '';
				}
				$mm[ $k ][ $suboption ] = $this->sanitizeStr( $mm[ $k ][ $suboption ] );
			}
		}

		if ( $mm[ 'menu' ][ 'breakpoint' ] == __( 'Never', 'mmenu' ) )
		{
			 $mm[ 'menu' ][ 'breakpoint' ] = false;
		}


		//	Create onDocumentReady script
		$o = array();
		$c = array();


		//	Counters
		if ( $mm[ 'advanced' ][ 'counters' ] == 'yes' )
		{
			$o[] = '
			counters: true';
		}


		//	Extensions
		$x = array();

		if ( $mm[ 'menu' ][ 'theme' ] != 'light' )
		{
			$x[] = 'theme-' . $mm[ 'menu' ][ 'theme' ];
		}

		if ( $mm[ 'advanced' ][ 'pageshadow' ] == 'yes' )
		{
			$x[] = 'shadow-page';
		}
		if ( $mm[ 'advanced' ][ 'pagedim' ] == 'yes' )
		{
			$x[] = 'pagedim-black';
		}

		if ( $mm[ 'advanced' ][ 'slidemenu' ] == 'yes' && 
			$mm[ 'menu' ][ 'position' ] != 'bottom' &&
			$mm[ 'menu' ][ 'position' ] != 'top'
		) {
			$x[] = 'effect-slide-menu';
		}

		if ( $mm[ 'advanced' ][ 'indentborder' ] != 'yes' )
		{
			$x[] = 'border-full';
		}

		if ( $mm[ 'advanced' ][ 'truncatelistitems' ] != 'yes' )
		{
			$x[] = 'multiline';
		}

		if ( $mm[ 'advanced' ][ 'fullscreen' ] == 'yes' )
		{
			$x[] = 'fullscreen';
		}

		if ( $mm[ 'menu' ][ 'breakpoint' ] )
		{
			$x[] = 'widescreen';
		}

		if ( count( $x ) > 0 )
		{
			$o[] = '
			extensions: ["' . implode( '", "', $x ) . '"]';
		}


		//	IconPanels
		if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' )
		{
			$o[] = '
			iconPanels: true';
		}


		//	OffCanvas
		$x = array();
		$x[] = 'moveBackground: false';
		
		if ( $mm[ 'menu' ][ 'position' ] != 'left' )
		{
			$x[] = 'position: "' . $mm[ 'menu' ][ 'position' ] . '"';
		}

		if ( $mm[ 'menu' ][ 'position' ] == 'bottom' ||
			$mm[ 'menu' ][ 'position' ] == 'top'
		) {
			$x[] = 'zposition: "front"';
		}

		$o[] = '
			offCanvas: {
				' . implode( ",\n\t\t\t\t", $x ) . '
			}';


		//	Searchfield
		$x = array();
		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' )
		{
			if ( strlen( $mm[ 'header' ][ 'searchfield_placeholder' ] ) > 0 )
			{
				$x[] = 'placeholder: "' . $mm[ 'header' ][ 'searchfield_placeholder' ] . '"';
			}
			if ( strlen( $mm[ 'header' ][ 'searchfield_noresults' ] ) > 0 )
			{
				$x[] = 'noResults: "' . $mm[ 'header' ][ 'searchfield_noresults' ] . '"';
			}
		}

		if ( count( $x ) > 0 )
		{
			$o[] = '
			searchfield: {
				' . implode( ",\n\t\t\t\t", $x ) . '
			}';
		}


		//	Navbar
		$x = array();
		if ( $mm[ 'header' ][ 'navigate' ] == 'button' )
		{
			if ( strlen( $mm[ 'header' ][ 'navigate_title' ] ) > 0 )
			{
				$x[] = 'title: "' . $mm[ 'header' ][ 'navigate_title' ] . '"';
			}
		}
		if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' ||
			 $mm[ 'header' ][ 'navigate' ] == 'dropdown'
		) {
			$x[] = 'add: false';
		}

		if ( count( $x ) > 0 )
		{
			$o[] = '
			navbar: {
				' . implode( ",\n\t\t\t\t", $x ) . '
			}';
		}


		//	Navbars
		$n = array();


		//	Header image
		if ( $mm[ 'header' ][ 'image' ] == 'yes' )
		{
			if ( $mm[ 'header' ][ 'navigate' ] == 'iconpanels' )
			{
				$available = 4;
			}
			else
			{
				$available = 3;
			}
			if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' )
			{
				$available--;
			}
			if ( strlen( $mm[ 'header' ][ 'buttons_html' ] ) > 0 )
			{
				$available--;
			}
			else if ( strlen( $mm[ 'header' ][ 'buttons_selector' ] ) > 0 )
			{
				$available--;
			}
			else
			{
				for ( $i = 1; $i <= 5; $i++ )
				{
					if ( strlen( $mm[ 'header' ][ 'button_' . $i . '_icon' ] ) > 0 )
					{
						$available--;
						break;
					}
				}
			}
			$height = intval( $mm[ 'header' ][ 'image_height' ] );
			if ( $available < $height )
			{
				$height = $available;
			}

			$n[] = '{
					height: ' . $height . ',
					content: [ "<div class=\"wpmm-header-image\" />" ]
				}';
		}


		//	Header searchfield
		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' )
		{
			$n[] = '{
					content: [ "searchfield" ]
				}';
		}

		//	Header breadcrumbs
		if ( $mm[ 'header' ][ 'navigate' ] == 'breadcrumbs' )
		{
			$n[] = '{
					content: [ "breadcrumbs" ]
				}';
		}

		//	Header back, title, close
		if ( $mm[ 'header' ][ 'navigate' ] == 'button' )
		{
			$x = array();
			$x[] = 'prev';
			$x[] = 'title';

			if ( $mm[ 'header' ][ 'navigate_close' ] == 'yes' )
			{
				$x[] = 'close';
			}

			$n[] = '{
					content: [ "' . implode( '", "', $x ) . '" ]
				}';	
		}

		//	Header + footer buttons
		foreach( array( 'header', 'footer' ) as $bar )
		{
			$x = array();
			if ( strlen( $mm[ $bar ][ 'buttons_html' ] ) > 0 )
			{
				$x[] = $mm[ $bar ][ 'buttons_html' ];
			}
			if ( strlen( $mm[ $bar ][ 'buttons_selector' ] ) > 0 )
			{
				$x[] = $mm[ $bar ][ 'buttons_selector' ];
			}
			for ( $i = 1; $i <= 5; $i++ )
			{
				if ( strlen( $mm[ $bar ][ 'button_' . $i . '_icon' ] ) > 0 )
				{
					$x[] = '<a'
						. ' href="' . $mm[ $bar ][ 'button_' . $i . '_href' ] . '"'
						. ' target="' . $mm[ $bar ][ 'button_' . $i . '_target' ] . '">'
						. '<span class=" dashicons ' . $mm[ $bar ][ 'button_' . $i . '_icon' ] . '" >&nbsp;</span>'
						. '</a>';
				}
			}

			if ( count( $x ) > 0 )
			{
				$n[] = '{
						position: "' . ( $bar == 'header' ? 'top' : 'bottom' ) . '",
						content: [ \'' . implode( '\', \'', $x ) . '\' ]
					}';
			}
		}

		if ( count( $n ) > 0 )
		{
			$o[] = '
			navbars: [
				' . implode( ",", $n ) . '
			]';
		}


		//	Sliding submenus
		if ( $mm[ 'header' ][ 'navigate' ] == 'dropdown' )
		{
			$o[] = '
			slidingSubmenus: false';
		}


		//	Accessibility
		if ( $mm[ 'accessibility' ][ 'screenreader' ] == 'yes' )
		{
			$o[] = '
			screenReader: true';
		}
		if ( $mm[ 'accessibility' ][ 'keyboard' ] == 'yes' )
		{
			$o[] = '
			keyboardNavigation: true';
		}
		if ( $mm[ 'accessibility' ][ 'rtl' ] != 'yes' )
		{
			$o[] = '
			rtl: false';
		}


		//	Conf
		$c[] = '
			offCanvas: {
				pageSelector: "> div:not(#wpadminbar)"
			}';

		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' &&
			$mm[ 'header' ][ 'searchfield_sitesearch' ] == 'yes'
		) {
			$c[] = '
			searchfield: {
				form: {
					method: $sform.attr( \'method\' ) || \'get\',
					action: $sform.attr( \'action\' ) || \'/\'
				},
				input: {
					name: \'s\'
				},
				submit: true
			}';
		}



		//	Concatenate mmenu JS and CSS from originals
		
		$dir = dirname( __FILE__ ) . '/';
  		$src = $dir . 'mmenu/';

  		$js = @file_get_contents( $src . 'jquery.mmenu.all.min.js' );
  		$js .= '
jQuery(document).ready(function($) {
	$("#wpadminbar")
		.css( "position", "fixed" )
		.addClass( "mm-slideout" );

	var $menu 	= $("' . $mm[ 'setup' ][ 'menu' ] . '").first().clone(),
		$button = $("' . $mm[ 'setup' ][ 'button' ] . '");';

		if ( $mm[ 'header' ][ 'searchfield' ] == 'yes' &&
			$mm[ 'header' ][ 'searchfield_sitesearch' ] == 'yes'
		) {
			$js .= '

	var $sform = $(\'input[name="s"]\').closest( \'form\' ).first();';
		}

		$js .= '

	var $selected = $menu.find( "li.current-menu-item" );
	var $vertical = $menu.find( "li.Vertical" );
	var $dividers = $menu.find( "li.Divider" );

	$menu.children().not( "ul" ).remove();
	$menu.add( $menu.find( "ul, li" ) )
		.removeAttr( "class" )
		.removeAttr( "id" );

	$menu.addClass( "wpmm-menu" );

	$selected.addClass( "Selected" );
	$vertical.addClass( "Vertical" );
	$dividers.addClass( "Divider" );

	$menu.mmenu(
		{' . implode( ",", $o ) . '
		}, {' . implode( ",", $c ) . '
		}
	);';

	if ( $mm[ 'advanced' ][ 'fullsubopen' ] == 'yes' )
	{
		$js .= '

	$menu
		.find( ".mm-listview" )
		.find( ".mm-next" )
		.next()
		.filter( "[href=\'#\']" )
		.prev()
		.addClass( "mm-fullsubopen" );';
	}

		$js .= '

	var api = $menu.data( "mmenu" );

	$button
		.addClass( "wpmm-button" )
		.off( "click" )
		.on( "click", function( e ) {
			e.preventDefault();
			e.stopImmediatePropagation();
			api.open();
		});';

		if ( $fileAffix == '-preview' ) //	:/
		{
			$js .= '
	$("body").on(
		"click",
		"a",
		function( e ) {
			if ( !e.isDefaultPrevented() )
			{
				if ( !confirm( "You are leaving the preview, changes you\'ve made to the mobile menu will no longer take effect." ) ) {
					e.preventDefault();
				}
			}
		}
	);
	setTimeout(function() {
		api.open();
	}, 1000);';
		}

		$js .= '

	function mm_hasBg( $e )
	{
		var bg = true;
		switch( $e.css( "background-color" ) )
		{
			case "":
			case "none":
			case "inherit":
			case "undefined":
			case "transparent":
			case "rgba(0,0,0,0)":
			case "rgba( 0,0,0,0 )":
			case "rgba(0, 0, 0, 0)":
			case "rgba( 0, 0, 0, 0 )":
				bg = false;
				break;
		}
		return bg;
	}

	var $node = $(".mm-page");
	if ( !mm_hasBg( $node ) )
	{
		$node.addClass( "wpmm-force-bg" );
		$node = $("body");
		if ( !mm_hasBg( $node ) )
		{
			$node.addClass( "wpmm-force-bg" );
			$node = $("html");
			if ( !mm_hasBg( $node ) )
			{
				$node.addClass( "wpmm-force-bg" );
			}
		}
	}
});';



  		$css = @file_get_contents( $src . 'jquery.mmenu.all.css' );
  		$css .= '
.wpmm-menu
{
	background-color: ' . $mm[ 'menu' ][ 'backgroundcolor' ] . ' !important;
}
.wpmm-force-bg
{
	background-color: inherit;
}
html.wpmm-force-bg
{
	background-color: #fff;
}

.wpmm-menu .mm-navbar .dashicons
{
	font-size: 20px;
	line-height: 20px;
	height: 20px
}

.wpmm-menu .mm-listview > li > .dropdown-toggle
{
	display: none;
}';

		//	Header
		if ( $mm[ 'header' ][ 'image' ] == 'yes' )
		{
			$pos = ( $mm[ 'header' ][ 'image_scale' ] == 'cover' )
				? '0'
				: '20px';

			$css .= '

.wpmm-header-image
{
	background: url(' . $mm[ 'header' ][ 'image_src' ] . ') center center / ' . $mm[ 'header' ][ 'image_scale' ] . ' no-repeat transparent;
	padding: 0 !important;
	position: absolute;
	top: ' . $pos . ';
	right: ' . $pos . ';
	bottom: ' . $pos . ';
	left: ' . $pos . ';
}';
		}

  		//	Widescreen
  		if ( $mm[ 'menu' ][ 'breakpoint' ] )
		{
			$css .= '
@media screen and (min-width:' . intval( $mm[ 'menu' ][ 'breakpoint' ], 10 ) . 'px) {
' . @file_get_contents( $src . 'jquery.mmenu.widescreen.css' ) . '

	.wpmm-button ,
	' . $mm[ 'setup' ][ 'menu' ] . '
	{
		display: none !important;
	}
}
';
		}


		@file_put_contents( $dir . 'js/mmenu' . $fileAffix . '.js', $js );
		@file_put_contents( $dir . 'css/mmenu' . $fileAffix . '.css', $css );
	}


	public function admin_css_js( $screen_id )
	{

		if ( $screen_id == $this->screen_id )
		{
		    wp_enqueue_style(	'wp-color-picker' );
    		wp_enqueue_script(	'wp-color-picker' );

   			wp_enqueue_style( 	'dashicons' );

			wp_enqueue_script(	'media-upload' );


			$translation_array = array(
				'save' => __( 'Save', 'mmenu' )
			);

			wp_register_style(	'mmenu', plugin_dir_url( __FILE__ ) . 'mmenu/jquery.mmenu.all.css' );
			wp_enqueue_style( 	'mmenu' );

			wp_register_script( 'mmenu', plugin_dir_url( __FILE__ ) . 'mmenu/jquery.mmenu.all.min.js' );
			wp_localize_script( 'mmenu', 'mmenu_i18n', $translation_array );
			wp_enqueue_script(	'mmenu' );
		}

		parent::admin_css_js( $screen_id );
	}

	public function load_textdomain()
	{
		load_plugin_textdomain( 'mmenu', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}

}



class MmenuFrontend {

	public function __construct()
	{
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'wp_nav_menu_args', array( $this, 'nav_menu_args' ) );
	}

	public function nav_menu_args( $args )
	{
		if ( !$args[ 'container_id' ] )
		{
			$args[ 'container_id' ] = 'menu-location-' . $args[ 'theme_location' ];
		}
		return $args;
	}

	public function enqueue_scripts()
	{
		$version = get_option( 'mm_setup', array() );
		$version = isset( $version[ 'version' ] ) ? $version[ 'version' ] : 0;

		wp_enqueue_style( 'dashicons' );

		if ( current_user_can( 'manage_options' ) &&
			isset( $_GET[ 'mmenu' ] )
		) {
			if ( $_GET[ 'mmenu' ] == 'locate' )
			{
				wp_enqueue_script( 	'mm-locate', plugin_dir_url( __FILE__ ) . 'lib/locate/admin-locate.js', array( 'jquery' ) );
		   		wp_enqueue_style( 	'mm-locate', plugin_dir_url( __FILE__ ) . 'lib/locate/admin-locate.css' );
			}
			else if ( $_GET[ 'mmenu' ] == 'breakpoint' )
			{
				wp_enqueue_script( 	'mm-admin-setup', plugin_dir_url( __FILE__ ) . 'lib/breakpoint/admin-breakpoint.js', array( 'jquery' ) );
			}
			else if ( $_GET[ 'mmenu' ] == 'preview' )
			{
		   		wp_enqueue_script( 	'mmenu', plugin_dir_url( __FILE__ ) . 'js/mmenu-preview.js', array( 'jquery' ), $version );
		   		wp_enqueue_style( 	'mmenu', plugin_dir_url( __FILE__ ) . 'css/mmenu-preview.css', '', $version );
			}

			add_action( 'wp_footer', array( $this, 'echo_mmenu_js' ) );
		}
		else
		{
			$menu = get_option( 'mm_menu', false );
			if ( $menu )
			{
			   	wp_enqueue_script( 	'mmenu', plugin_dir_url( __FILE__ ) . 'js/mmenu.js', array( 'jquery' ), $version );
			   	wp_enqueue_style( 	'mmenu', plugin_dir_url( __FILE__ ) . 'css/mmenu.css', '', $version );
			}
		}
    }
	public function echo_mmenu_js()
	{
		echo '
<script type="text/javascript">
	window.mmenu = {};
</script>';
	}
}


// Instantiate the class.
if ( is_admin() )
{
	new MmenuAdminPage();
}
else
{
	new MmenuFrontend();
}