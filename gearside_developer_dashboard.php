<?php
/*
Plugin Name: Gearside Developer Dashboard
Plugin URI: http://gearside.com/wordpress-developer-information-dashboard/
Description: Developer Metaboxes for server information and TODO Manager for the WordPress Admin Dashboard.
Version: 1.0.61
Author: Chris Blakley
Author URI: http://gearside.com/
License: GPL2
*/
/*
Copyright 2013  Chris Blakley  (email : chris@gearside.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if( !class_exists('Gearside_Developer_Dashboard') ) {
	class Gearside_Developer_Dashboard {
		/* Activate the plugin */
		public static function activate() {
			// Do nothing
		}

		/* Deactivate the plugin */
		public static function deactivate() {
			// Do nothing
		}
	}
}

if ( class_exists('Gearside_Developer_Dashboard') ) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Gearside_Developer_Dashboard', 'activate'));
	register_deactivation_hook(__FILE__, array('Gearside_Developer_Dashboard', 'deactivate'));

	// instantiate the plugin class
	$gearside_developer_dashboard = new Gearside_Developer_Dashboard();

	//Init WP Core Functions (if not already)
	if ( !function_exists('wp_get_current_user') ) {
	    include(ABSPATH . "wp-includes/pluggable.php");
	}


	add_action('admin_enqueue_scripts', 'enqueue_gdd');
	function enqueue_gdd() {
		$localize_bloginfo = array(
			'admin_ajax' => admin_url('admin-ajax.php')
		);

		wp_register_script('gdd_script', plugins_url() . '/gearside-developer-dashboard/gearside_developer_dashboard.js', array(), null, true);
		wp_enqueue_script('gdd_script');
		wp_localize_script('gdd_script', 'bloginfo', $localize_bloginfo);

		wp_register_style('gdd_style', plugins_url() . '/gearside-developer-dashboard/gearside_developer_dashboard.css', array(), null);
		wp_register_style('gdd_font_awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css', array(), '4.1.0');
		wp_enqueue_style('gdd_font_awesome');
		wp_enqueue_style('gdd_style');
	}




	//TODO Metabox
	add_action('wp_dashboard_setup', 'todo_metabox');
	function todo_metabox() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget('todo_manager', '@TODO Manager', 'dashboard_todo_manager');
	}

	function dashboard_todo_manager() {

		echo '<p class="todoresults_title"><strong>Active @TODO Comments</strong> <a class="todo_help_icon" href="http://gearside.com/wordpress-dashboard-todo-manager/" target="_blank"><i class="fa fw fa-question-circle"></i> Documentation &raquo;</a></p>';

		echo '<div class="todo_results">';
		$todo_last_filename = '';
		$todo_dirpath = get_template_directory();
		$todo_file_counter = 0;
		$todo_instance_counter = 0;
		foreach ( gearside_glob_r($todo_dirpath . '/*') as $todo_file ) {
			$todo_counted = 0;
			$todo_hidden = 0;
			if ( is_file($todo_file) ) {
			    if ( strpos(basename($todo_file), '@TODO') !== false ) {
				    echo '<p class="resulttext">' . str_replace($todo_dirpath, '', dirname($todo_file)) . '/<strong>' . basename($todo_file) . '</strong></p>';
				    $todo_file_counter++;
				    $todo_counted = 1;
			    }

			    $todo_skipExtensions = array('.jpg', '.jpeg', '.png', '.gif', '.ico', '.tiff', '.psd', '.ai',  '.apng', '.bmp', '.otf', '.ttf', '.ogv', '.flv', '.fla', '.mpg', '.mpeg', '.avi', '.mov', '.woff', '.eot', '.mp3', '.mp4', '.wmv', '.wma', '.aiff', '.zip', '.zipx', '.rar', '.exe', '.dmg', '.swf', '.pdf', '.pdfx', '.pem');
			    $todo_skipFilenames = array('README.md', 'nebula_admin_functions.php', 'error_log', 'Mobile_Detect.php', 'class-tgm-plugin-activation.php');

			    if ( !gearside_contains(basename($todo_file), $todo_skipExtensions) && !gearside_contains(basename($todo_file), $todo_skipFilenames) ) {
				    foreach ( file($todo_file) as $todo_lineNumber => $todo_line ) {
						$todo_hidden = 0;

				        if ( stripos($todo_line, '@TODO') !== false ) {
				            $todo_actualLineNumber = $todo_lineNumber+1;

							$the_full_todo = substr($todo_line, strpos($todo_line, "@TODO"));
							$the_todo_meta = current(explode(":", $the_full_todo));

							//Get the priority
							preg_match_all('!\d+!', $the_todo_meta, $the_todo_ints);
							$todo_hidden = 0;
							if ( $the_todo_ints[0][0] != '' ) {
								switch ( true ) {
									case ( $the_todo_ints[0][0] >= 5 ) :
										$todo_hidden = 0;
										$the_todo_icon_color = '#d92827';
										break;
									case ( $the_todo_ints[0][0] == 4 ) :
										$todo_hidden = 0;
										$the_todo_icon_color = '#e38a2c';
										break;
									case ( $the_todo_ints[0][0] == 3 ) :
										$todo_hidden = 0;
										$the_todo_icon_color = '#dda65c';
										break;
									case ( $the_todo_ints[0][0] == 2 ) :
										$todo_hidden = 0;
										$the_todo_icon_color = '#d3bd9f';
										break;
									case ( $the_todo_ints[0][0] == 1 ) :
										$todo_hidden = 0;
										$the_todo_icon_color = '#ccc';
										break;
									case ( $the_todo_ints[0][0] == 0 ) :
										$todo_hidden = 1;
										$the_todo_icon_color = '#0098d7';
										break;
									default :
										$todo_hidden = 0;
										$the_todo_icon_color = '#999';
										break;
								}
							} else {
								$todo_hidden = 0;
							}

							if ( $todo_hidden == 1 ) {
								$todo_hidden_style = 'style="display: none;"';
								$todo_hidden_class = 'hidden_todo';
							} else {
								$todo_hidden_style = '';
								$todo_hidden_class = '';
							}

							//Get the category
							preg_match_all('/".*?"|\'.*?\'/', $the_todo_meta, $the_todo_quote_check);
							if ( $the_todo_quote_check[0][0] != '' ) {
								$the_todo_category = substr($the_todo_quote_check[0][0], 1, -1);
								$the_todo_category_html = '<span class="todocategory" style="background: ' . $the_todo_icon_color . ';">' . $the_todo_category . '</span>';
							} else {
								$the_todo_quote_check = '';
								$the_todo_category = '';
								$the_todo_category_html = '';
							}

							//Get the message
							$the_todo_message_full = substr($the_full_todo, strpos($the_full_todo, ":") + 1);
							$end_todo_message_strings = array('-->', '?>', '*/');
							$the_todo_message = explode($end_todo_message_strings[0], str_replace($end_todo_message_strings, $end_todo_message_strings[0], $the_todo_message_full));


							$todo_this_filename = str_replace($todo_dirpath, '', dirname($todo_file)) . '/' . basename($todo_file);
							if ( $todo_last_filename != $todo_this_filename ) {
								if ( $todo_last_filename != '' ) {
									echo '</div><!--/todofilewrap-->';
								}


								echo '<div class="todofilewrap">';
								echo '<p class="todofilename">' . str_replace($todo_dirpath, '', dirname($todo_file)) . '/<strong>' . basename($todo_file) . '</strong></p>';
							}

							echo '<div class="linewrap ' . $todo_hidden_class . '" ' . $todo_hidden_style . '>
									<p class="todoresult"> ' . $the_todo_category_html . ' <a class="linenumber" href="#">Line ' . $todo_actualLineNumber . '</a> <span class="todomessage">' . $the_todo_message[0] . '</span></p>
									<div class="precon"><pre class="actualline">' . trim(htmlentities($todo_line)) . '</pre></div>
								</div>';

							$todo_last_filename = $todo_this_filename;

							$todo_instance_counter++;
							if ( $todo_counted == 0 ) {
								$todo_file_counter++;
								$todo_counted = 1;
							}
				        }
				    }
			    }
			}
		}

		if ( $todo_counted > 0 ) {
			echo '</div><!--/todofilewrap-->';
		} else {
			echo 'No active @TODO tasks!';
			echo '<style>.todo_results {height: auto !important; resize: none;}</style>';
		}
		echo '</div><!--/todo_results--></div>';
	}








	//Gearside Developer Metabox
	add_action('wp_dashboard_setup', 'gearside_dev_metabox');
	function gearside_dev_metabox() {
		global $wp_meta_boxes;
		wp_add_dashboard_widget('gearside_developer_info', 'Developer Info', 'dashboard_developer_info');
	}
	function dashboard_developer_info() {

		$whois = getwhois(gearside_url_components('sld'), ltrim(gearside_url_components('tld'), '.'));

		//Get Expiration Date
		if ( gearside_contains($whois, array('Registrar Registration Expiration Date: ')) ) {
			$domain_exp_detected = substr($whois, strpos($whois, "Registrar Registration Expiration Date: ")+40, 10);
		} elseif ( gearside_contains($whois, array('Registry Expiry Date: ')) ) {
			$domain_exp_detected = substr($whois, strpos($whois, "Registry Expiry Date: ")+22, 10);
		} else {
			$domain_exp_detected = '';
		}

		$domain_exp_unix = strtotime(trim($domain_exp_detected));
		$domain_exp = date("F j, Y", $domain_exp_unix);
		$domain_exp_style = ( $domain_exp_unix < strtotime('+1 month') ) ? 'color: red; font-weight: bold;' : 'color: inherit;' ;
		$domain_exp_html = ( $domain_exp_unix > strtotime('March 27, 1986') ) ? ' <small style="' . $domain_exp_style . '">(Expires: ' . $domain_exp . ')</small>' : '';


		//Get Registrar URL
		if ( gearside_contains($whois, array('Registrar URL: ')) && gearside_contains($whois, array('Updated Date: ')) ) {
			$domain_registrar_url_start = strpos($whois, "Registrar URL: ")+15;
			$domain_registrar_url_stop = strpos($whois, "Updated Date: ")-$domain_registrar_url_start;
			$domain_registrar_url = substr($whois, $domain_registrar_url_start, $domain_registrar_url_stop);
		} elseif ( gearside_contains($whois, array('Registrar URL: ')) && gearside_contains($whois, array('Update Date: ')) ) {
			$domain_registrar_url_start = strpos($whois, "Registrar URL: ")+15;
			$domain_registrar_url_stop = strpos($whois, "Update Date: ")-$domain_registrar_url_start;
			$domain_registrar_url = substr($whois, $domain_registrar_url_start, $domain_registrar_url_stop);
		} elseif ( gearside_contains($whois, array('URL: ')) && gearside_contains($whois, array('Relevant dates:')) ) { //co.uk
			$domain_registrar_url_start = strpos($whois, "URL: ")+5;
			$domain_registrar_url_stop = strpos($whois, "Relevant dates: ")-$domain_registrar_url_start;
			$domain_registrar_url = substr($whois, $domain_registrar_url_start, $domain_registrar_url_stop);
		}


		//Get Registrar Name
		$domain_registrar_start = '';
		$domain_registrar_stop = '';
		if ( gearside_contains($whois, array('Registrar: ')) && gearside_contains($whois, array('Sponsoring Registrar IANA ID:')) ) {
			$domain_registrar_start = strpos($whois, "Registrar: ")+11;
			$domain_registrar_stop = strpos($whois, "Sponsoring Registrar IANA ID:")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		} elseif ( gearside_contains($whois, array('Registrar: ')) && gearside_contains($whois, array('Registrar IANA ID: ')) ) {
			$domain_registrar_start = strpos($whois, "Registrar: ")+11;
			$domain_registrar_stop = strpos($whois, "Registrar IANA ID: ")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		} elseif ( gearside_contains($whois, array('Registrar: ')) && gearside_contains($whois, array('Registrar IANA ID: ')) ) {
			$domain_registrar_start = strpos($whois, "Registrar: ")+11;
			$domain_registrar_stop = strpos($whois, "Registrar IANA ID: ")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		} elseif ( gearside_contains($whois, array('Sponsoring Registrar:')) && gearside_contains($whois, array('Sponsoring Registrar IANA ID:')) ) {
			$domain_registrar_start = strpos($whois, "Sponsoring Registrar:")+21;
			$domain_registrar_stop = strpos($whois, "Sponsoring Registrar IANA ID:")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		} elseif ( gearside_contains($whois, array('Registrar:')) && gearside_contains($whois, array('Number: ')) ) {
			$domain_registrar_start = strpos($whois, "Registrar:")+17;
			$domain_registrar_stop = strpos($whois, "Number: ")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		} elseif ( gearside_contains($whois, array('Registrar:')) && gearside_contains($whois, array('URL:')) ) { //co.uk
			$domain_registrar_start = strpos($whois, "Registrar: ")+11;
			$domain_registrar_stop = strpos($whois, "URL: ")-$domain_registrar_start;
			$domain_registrar = substr($whois, $domain_registrar_start, $domain_registrar_stop);
		}


		//Get Reseller Name
		$domain_reseller = '';
		if ( gearside_contains($whois, array('Reseller: ')) && gearside_contains($whois, array('Domain Status: ')) ) {
			$reseller1 = strpos($whois, 'Reseller: ');
			$reseller2 = strpos($whois, 'Reseller: ', $reseller1 + strlen('Reseller: '));
			if ( $reseller2 ) {
				$domain_reseller_start = strpos($whois, "Reseller: ")+10;
				$domain_reseller_stop = $reseller2-$domain_reseller_start;
				$domain_reseller = substr($whois, $domain_reseller_start, $domain_reseller_stop);
			} else {
				$domain_reseller_start = strpos($whois, "Reseller: ")+10;
				$domain_reseller_stop = strpos($whois, "Domain Status: ")-$domain_reseller_start;
				$domain_reseller = substr($whois, $domain_reseller_start, $domain_reseller_stop);
			}
		}


		//Construct Registrar info to be echoed
		if ( $domain_registrar_url && strlen($domain_registrar_url) < 70 ) {
			$domain_registrar_html = ( $domain_registrar && strlen($domain_registrar) < 70 ) ? '<li><i class="fa fa-info-circle fa-fw"></i> Registrar: <strong><a href="//' . trim($domain_registrar_url) . '" target="_blank">' . $domain_registrar . '</a></strong>': '';
		} else {
			$domain_registrar_html = ( $domain_registrar && strlen($domain_registrar) < 70 ) ? '<li><i class="fa fa-info-circle fa-fw"></i> Registrar: <strong>' . trim($domain_registrar) . '</strong>': '';
		}
		if ( trim($domain_registrar_html) != '' && $domain_reseller && strlen($domain_reseller) < 70 ) {
			$domain_registrar_html .= '<small>(via ' . trim($domain_reseller) . ')</small></li>';
		} else {
			$domain_registrar_html .= '</li>';
		}



		//Get last modified filename and date
		$dir = gearside_glob_r( get_template_directory() . '/*');
		$last_date = 0;
		$skip_files = array();

		foreach( $dir as $file ) {
			if( is_file($file) ) {
				$mod_date = filemtime($file);
				if ( $mod_date > $last_date && !gearside_contains(basename($file), $skip_files) ) {
					$last_date = $mod_date;
					$last_filename = basename($file);
					$last_file_path = str_replace(get_template_directory(), '', dirname($file)) . '/' . $last_filename;
				}
			}
		}
		$nebula_size = gearside_foldersize(get_template_directory());
		$upload_dir = wp_upload_dir();
		$uploads_size = gearside_foldersize($upload_dir['basedir']);

		$secureServer = '';
		if ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ) {
			$secureServer = '<small><i class="fa fa-lock fa-fw"></i>Secured Connection</small>';
		}

		function top_domain_name($url){
			$alldomains = explode(".", $url);
			return $alldomains[count($alldomains)-2] . "." . $alldomains[count($alldomains)-1];
		}

		if ( function_exists('gethostname') ){
			$dnsrecord = ( dns_get_record(top_domain_name(gethostname()), DNS_NS) ) ? dns_get_record(top_domain_name(gethostname()), DNS_NS) : '';
		}

		function initial_install_date(){
			$install_date = '<strong>' . date("F j, Y", getlastmod()) . '</strong> <small>@</small> <strong>' . date("g:ia", getlastmod()) . '</strong>';
			return $install_date;
		}

		if ( strpos(strtolower(PHP_OS), 'linux') !== false ) {
			$php_os_icon = 'fa-linux';
		} else if ( strpos(strtolower(PHP_OS), 'windows') !== false ) {
			$php_os_icon = 'fa-windows';
		} else {
			$php_os_icon = 'fa-upload';
		}

		if ( function_exists('wp_max_upload_size') ) {
			$upload_max = '<small>(Max upload: <strong>' . strval(round((int) wp_max_upload_size()/(1024*1024))) . 'mb</strong>)</small>';
		} else if ( ini_get('upload_max_filesize') ) {
			$upload_max = '<small>(Max upload: <strong>' . ini_get('upload_max_filesize') . '</strong>)</small>';
		} else {
			$upload_max = '';
		}

		if ( function_exists('mysqli_connect') ){
			$mysqli_connect = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
			$mysql_version = mysqli_get_server_info($mysqli_connect);
		}

		$safe_mode = ( ini_get('safe_mode') ) ? '<small><strong><em>Safe Mode</em></strong></small>': '';

		echo '<div id="testloadcon" style="pointer-events: none; opacity: 0; visibility: hidden; display: none;"></div>';
		echo '<script id="testloadscript">
				jQuery(window).on("load", function(){
					jQuery(".loadtime").css("visibility", "visible");
					beforeLoad = (new Date()).getTime();
					var iframe = document.createElement("iframe");
					iframe.style.width = "1200px";
					iframe.style.height = "0px";
					jQuery("#testloadcon").append(iframe);
					iframe.src = "' . home_url('/') . '";
					jQuery("#testloadcon iframe").on("load", function(){
						stopTimer();
					});
				});

				function stopTimer(){
				    var afterLoad = (new Date()).getTime();
				    var result = (afterLoad - beforeLoad)/1000;
				    jQuery(".loadtime").html(result + " seconds");
				    if ( result > 5 ) { jQuery(".slowicon").addClass("fa-warning"); }
				    jQuery(".serverdetections .fa-spin, #testloadcon, #testloadscript").remove();
				}
				</script>';

		echo '<p style="margin: 0; padding: 0; text-align: right; font-size: 10px;"><a href="http://gearside.com/wordpress-developer-information-dashboard/" target="_blank"><i class="fa fw fa-question-circle"></i> Documentation &raquo;</a></p>';

		echo '<ul class="serverdetections">';
			if ( WP_DEBUG ) {
				echo '<li style="color: red;"><i class="fa fa-exclamation-triangle fa-fw"></i> <strong>Warning:</strong> WP_DEBUG is Enabled!</li>';
			}
			echo '<li><i class="fa fa-info-circle fa-fw"></i> <a href="http://whois.domaintools.com/' . $_SERVER['SERVER_NAME'] . '" target="_blank" title="WHOIS Lookup">Domain</a>: <strong>' . $_SERVER['SERVER_NAME'] . '</strong>' . $domain_exp_html . '</li>';

			echo $domain_registrar_html;

			if ( function_exists('gethostname') && !empty($dnsrecord) ) {
				echo '<li><i class="fa fa-hdd-o fa-fw"></i> Host: <strong>' . top_domain_name(gethostname()) . '</strong> <small>(' . top_domain_name($dnsrecord[0]['target']) . ')</small></li>';
			}
			echo '<li><i class="fa fa-upload fa-fw"></i> Server IP: <strong><a href="http://whatismyipaddress.com/ip/' . $_SERVER['SERVER_ADDR'] . '" target="_blank">' . $_SERVER['SERVER_ADDR'] . '</a></strong> ' . $secureServer . '</li>';
			echo '<li><i class="fa ' . $php_os_icon . ' fa-fw"></i> Server OS: <strong>' . PHP_OS . '</strong> <small>(' . $_SERVER['SERVER_SOFTWARE'] . ')</small></li>';
			echo '<li><i class="fa fa-wrench fa-fw"></i> PHP Version: <strong>' . PHP_VERSION . '</strong> ' . $safe_mode . '</li>';
			echo '<li><i class="fa fa-cogs fa-fw"></i> PHP Memory Limit: <strong>' . WP_MEMORY_LIMIT . '</strong> ' . $safe_mode . '</li>';
			echo ( !empty($mysql_version) ) ? '<li><i class="fa fa-database fa-fw"></i> MySQL Version: <strong>' . $mysql_version . '</strong></li>' : '';
			echo '<li><i class="fa fa-code"></i> Theme directory size: <strong>' . round($nebula_size/1048576, 2) . 'mb</strong> </li>';
			echo '<li><i class="fa fa-picture-o"></i> Uploads directory size: <strong>' . round($uploads_size/1048576, 2) . 'mb</strong> ' . $upload_max . '</li>';
			echo '<li><i class="fa fa-clock-o fa-fw"></i> <span title="' . get_home_url() . '" style="cursor: help;">Homepage</span> load time: <a href="http://developers.google.com/speed/pagespeed/insights/?url=' . home_url('/') . '" target="_blank" title="Time is specific to your current environment and therefore may be faster or slower than average."><strong class="loadtime" style="visibility: hidden;"><i class="fa fa-spinner fa-fw fa-spin"></i></strong></a> <i class="slowicon fa" style="color: maroon;"></i></li>';
			echo '<li><i class="fa fa-calendar-o fa-fw"></i> Initial Install: ' . initial_install_date() . '</li>';
			echo '<li><i class="fa fa-calendar fa-fw"></i> Last modified: <strong>' . date("F j, Y", $last_date) . '</strong> <small>@</small> <strong>' . date("g:ia", $last_date) . '</strong> <small title="' . $last_file_path . '" style="cursor: help;">(' . $last_filename . ')</small></li>';
		echo '</ul>';

		echo '<i id="searchprogress" class="fa fa-search fa-fw"></i> <form id="theme" class="searchfiles"><input class="findterm" type="text" placeholder="Search files" /><select class="searchdirectory"><option value="theme">Theme</option><option value="plugins">Plugins</option><option value="uploads">Uploads</option></select><input class="searchterm button button-primary" type="submit" value="Search" /></form><br/>';

		echo '<div class="search_results"></div>';
	}




	//Search theme or plugin files via Gearside Developer Metabox
	add_action('wp_ajax_gearside_search_theme_files', 'gearside_search_theme_files');
	add_action('wp_ajax_nopriv_gearside_search_theme_files', 'gearside_search_theme_files');
	function gearside_search_theme_files() {
		if ( strlen($_POST['data'][0]['searchData']) < 3 ) {
			echo '<p><strong>Error:</strong> Minimum 3 characters needed to search!</p>';
			die();
		}

		if ( $_POST['data'][0]['directory'] == 'theme' ) {
			$dirpath = get_template_directory();
		} elseif ( $_POST['data'][0]['directory'] == 'plugins' ) {
			$dirpath = WP_PLUGIN_DIR;
		} elseif ( $_POST['data'][0]['directory'] == 'uploads' ) {
			$uploadDirectory = wp_upload_dir();
			$dirpath = $uploadDirectory['basedir'];
		} else {
			echo '<p><strong>Error:</strong> Please specify a directory to search!</p>';
			die();
		}

		echo '<p class="resulttext">Search results for <strong>"' . $_POST['data'][0]['searchData'] . '"</strong> in the <strong>' . $_POST['data'][0]['directory'] . '</strong> directory:</p><br/>';

		$file_counter = 0;
		$instance_counter = 0;
		foreach ( gearside_glob_r($dirpath . '/*') as $file ) {
			$counted = 0;
			if ( is_file($file) ) {
			    if ( strpos(basename($file), $_POST['data'][0]['searchData']) !== false ) {
				    echo '<p class="resulttext">' . str_replace($dirpath, '', dirname($file)) . '/<strong>' . basename($file) . '</strong></p>';
				    $file_counter++;
				    $counted = 1;
			    }

				$skipExtensions = array('.jpg', '.jpeg', '.png', '.gif', '.ico', '.tiff', '.psd', '.ai',  '.apng', '.bmp', '.otf', '.ttf', '.ogv', '.flv', '.fla', '.mpg', '.mpeg', '.avi', '.mov', '.woff', '.eot', '.mp3', '.mp4', '.wmv', '.wma', '.aiff', '.zip', '.zipx', '.rar', '.exe', '.dmg', '.swf', '.pdf', '.pdfx', '.pem');
				$skipFilenames = array('error_log');
			    if ( !gearside_contains(basename($file), $skipExtensions) && !gearside_contains(basename($file), $skipFilenames) ) {
				    foreach ( file($file) as $lineNumber => $line ) {
				        if ( stripos($line, $_POST['data'][0]['searchData']) !== false ) {
				            $actualLineNumber = $lineNumber+1;
							echo '<div class="linewrap">
									<p class="resulttext">' . str_replace($dirpath, '', dirname($file)) . '/<strong>' . basename($file) . '</strong> on <a class="linenumber" href="#">line ' . $actualLineNumber . '</a>.</p>
									<div class="precon"><pre class="actualline">' . trim(htmlentities($line)) . '</pre></div>
								</div>';
							$instance_counter++;
							if ( $counted == 0 ) {
								$file_counter++;
								$counted = 1;
							}
				        }
				    }
			    }
			}
		}
		echo '<br/><p class="resulttext">Found ';
		if ( $instance_counter ) {
			echo '<strong>' . $instance_counter . '</strong> instances in ';
		}
		echo '<strong>' . $file_counter . '</strong> file';
		if ( $file_counter == 1 ) {
			echo '.</p>';
		} else {
			echo 's.</p>';
		}
		exit();
	}




	/*==========================
	 Utility Functions
	 ===========================*/


	 //Get the full URL. Not intended for secure use ($_SERVER var can be manipulated by client/server).
	function gearside_requested_url($host="HTTP_HOST") { //Can use "SERVER_NAME" as an alternative to "HTTP_HOST".
		$protocol = ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ) ? 'https' : 'http';
		$full_url = $protocol . '://' . $_SERVER["$host"] . $_SERVER["REQUEST_URI"];
		return $full_url;
	}


	//Separate a URL into it's components.
	function gearside_url_components($segment="all", $url=null) {
		if ( !$url ) {
			$url = gearside_requested_url();
		}

		$url_compontents = parse_url($url);
		if ( empty($url_compontents['host']) ) {
			return;
		}
		$host = explode('.', $url_compontents['host']);

		//Best way to get the domain so far. Probably a better way by checking against all known TLDs.
		preg_match("/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/", parse_url($url, PHP_URL_HOST), $domain);
		$sld = substr($domain[0], 0, strpos($domain[0], '.'));
		$tld = substr($domain[0], strpos($domain[0], '.'));

		switch ($segment) {
			case ('all') :
				return $url;
				break;

			case ('protocol') : //Protocol and Scheme are aliases and return the same value.
			case ('scheme') : //Protocol and Scheme are aliases and return the same value.
			case ('schema') :
				if ( $url_compontents['scheme'] != '' ) {
					return $url_compontents['scheme'];
				} else {
					return false;
				}
				break;

			case ('host') : //In http://something.example.com the host is "something.example.com"
			case ('hostname') :
				return $url_compontents['host'];
				break;

			case ('www') :
				if ( $host[0] == 'www' ) {
					return 'www';
				} else {
					return false;
				}
				break;

			case ('subdomain') :
			case ('sub_domain') :
				if ( $host[0] != 'www' && $host[0] != $sld ) {
					return $host[0];
				} else {
					return false;
				}
				break;

			case ('domain') : //In http://example.com the domain is "example.com"
				return $domain[0];
				break;

			case ('basedomain') : //In http://example.com/something the basedomain is "http://example.com"
			case ('base_domain') :
				return $url_compontents['scheme'] . '://' . $domain[0];
				break;

			case ('sld') : //In example.com the sld is "example"
			case ('second_level_domain') :
			case ('second-level_domain') :
				return $sld;
				break;

			case ('tld') : //In example.com the tld is ".com"
			case ('top_level_domain') :
			case ('top-level_domain') :
				return $tld;
				break;

			case ('filepath') : //Filepath will be both path and file/extension
				return $url_compontents['path'];
				break;

			case ('file') : //Filename will be just the filename/extension.
			case ('filename') :
				if ( gearside_contains(basename($url_compontents['path']), array('.')) ) {
					return basename($url_compontents['path']);
				} else {
					return false;
				}
				break;

			case ('path') : //Path should be just the path without the filename/extension.
				if ( gearside_contains(basename($url_compontents['path']), array('.')) ) { //@TODO "Nebula" 0: This will possibly give bad data if the directory name has a "." in it
					return str_replace(basename($url_compontents['path']), '', $url_compontents['path']);
				} else {
					return $url_compontents['path'];
				}
				break;

			case ('query') :
			case ('queries') :
				return $url_compontents['query'];
				break;

			default :
				return $url;
				break;
		}
	}


	//Traverse multidimensional arrays
	function gearside_in_array_r($needle, $haystack, $strict = true) {
	    foreach ($haystack as $item) {
	        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && gearside_in_array_r($needle, $item, $strict))) {
	            return true;
	        }
	    }
	    return false;
	}

	//Recursive Glob
	function gearside_glob_r($pattern, $flags = 0) {
	    $files = glob($pattern, $flags);
	    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
	        $files = array_merge($files, gearside_glob_r($dir . '/' . basename($pattern), $flags));
	    }
	    return $files;
	}

	//Add up the filesizes of files in a directory (and it's sub-directories)
	function gearside_foldersize($path) {
		$total_size = 0;
		$files = scandir($path);
		$cleanPath = rtrim($path, '/') . '/';
		foreach($files as $t) {
			if ($t<>"." && $t<>"..") {
				$currentFile = $cleanPath . $t;
				if (is_dir($currentFile)) {
					$size = gearside_foldersize($currentFile);
					$total_size += $size;
				} else {
					$size = filesize($currentFile);
					$total_size += $size;
				}
			}
		}
		return $total_size;
	}

	//Checks to see if an array contains a string.
	function gearside_contains($str, array $arr) {
	    foreach( $arr as $a ) {
	        if ( stripos($str, $a) !== false ) {
	        	return true;
	        }
	    }
	    return false;
	}



	if ( !class_exists( 'Whois' ) ) {

		class Whois {

		    var $m_status = 0;
		    var $m_domain = '';
		    var $m_servers = array();
		    var $m_data = array();
		    var $m_connectiontimeout = 5;
		    var $m_sockettimeout = 30;
		    var $m_redirectauth = true;
		    var $m_usetlds = array();
		    var $m_supportedtlds = array();
		    var $m_serversettings = array();

			    function Whois(){
			    $this->readconfig();
		    }

		    function readconfig(){

			    $this->m_serversettings = array();
			    $this->m_tlds = array();
			    $this->m_usetlds = array();

		        $servers = array("whois.crsnic.net#domain |No match for |Whois Server:|>NOTICE: The expiration date |Registrar:#Status:#Expiration Date:", "whois.afilias.net|NOT FOUND||<you agree to abide by this policy.|Expiration Date:#Status:#Registrant Email:#Admin Name:#Billing Name:#Billing Email#Tech Name:#Tech Email:#Registrant Name:#Admin Email:#Name Server:", "whois.nic.us|Not found:||>NeuStar, Inc., the Registry Administrator|Domain Expiration Date:#Domain Status:#Sponsoring Registrar:#Registrant Name:#Registrant Email:#Administrative Contact Name:#Administrative Contact Email:#Billing Contact Name:#Billing Contact Email:#Technical Contact Name:#Technical Contact Email:#Name Server:", "whois.internic.net|No match for |Whois Server:", "whois.publicinterestregistry.net|NOT FOUND||<you agree to abide by this policy.|Expiration Date:#Status:#Name Server:#Registrant Name:#Registrant Email:#Admin Name:#Admin Email:#Tech Name:#Tech Email:#Billing Name:#Billing Email:", "whois.neulevel.biz|Not found:||>NeuLevel, Inc., the Registry|Domain Expiration Date:#Domain Status:#Sponsoring Registrar:#Registrant Name:#Registrant Email:#Administrative Contact Name:#Administrative Contact Email:#Billing Contact Name:#Billing Contact Email:#Technical Contact Name:#Technical Contact Email:#Name Server:", "whois.nic.uk|No match for|||Registration Status:#Registrant:#Registrant's Address:#Renewal Date:#Name servers", "rs.domainbank.net|||<of the foregoing policies.|Administrative Contact:#Record expires on #Technical Contact:#Registrant:#Zone Contact:#Domain servers in ", "whois.moniker.com|||<you agree to abide by this policy.|Administrative Contact:#Registrant:#Domain Servers#Billing Contact:#Technical Contact:#Domain Expires on", "whois.networksolutions.com|||<right to modify these terms at any time.|Registrant:#Administrative Contact:#Record expires on #Domain servers in listed order:", "whois.enom.com|||>The data in this whois database |Registrant Contact:#Technical Contact:#Billing Contact:#Administrative Contact:#Status:#Name Servers:#Expiration date:", "whois.opensrs.net|||>The Data in the Tucows Registrar|Registrant:#Administrative Contact:#Technical Contact:#Record expires on#Domain servers in listed order:", "whois.godaddy.com|||<domain names listed in this database.|Registrant:#Expires On:#Administrative Contact:#Technical Contact:#Domain servers in listed order:", "whois.aunic.net|No Data Found|||Status:#Registrant Contact Name:#Registrant Email:#Name Server:#Tech Name:#Tech Email:", "whois.denic.de|free", "whois.worldsite.ws|No match for|||Registrant:#Name Servers:", "whois.nic.tv|", "whois.nic.tm|No match for", "whois.cira.ca|AVAIL", "whois.nic.cc|No match|Whois Server:|>The Data in eNIC Corporation|Whois Server:#Updated:", "whois.domainzoo.com|||<you agree to abide by these terms.", "whois.domaindiscover.com|||<you agree to abide by this policy.", "whois.markmonitor.com|||<you agree to abide by this policy.", "whois2.afilias-grs.net|NOT FOUND||<abide by this policy.");
		        $tlds = array("com=whois.crsnic.net", "net=whois.crsnic.net", "org=whois.publicinterestregistry.net", "info=whois.afilias.net", "biz=whois.neulevel.biz", "us=whois.nic.us", "co.uk=whois.nic.uk", "org.uk=whois.nic.uk", "ltd.uk=whois.nic.uk", "ca=whois.cira.ca", "cc=whois.nic.cc", "edu=whois.crsnic.net", "com.au=whois.aunic.net", "net.au=whois.aunic.net", "de=whois.denic.de", "ws=whois.worldsite.ws", "sc=whois2.afilias-grs.net");

		        $cnt = count($servers);

			    foreach( $servers as $server){
				    $server = trim($server);
				    $bits = explode('|', $server);
				    if( count($bits) > 1 ){
					    for( $i = count($bits); $i < 5; $i++){
						    if( !isset($bits[$i]) ) $bits[$i] = '';
					    }
					    $server = explode("#", $bits[0]);
					    if( !isset($server[1]) ) $server[1] = '';

					    $this->m_serversettings[$server[0]] = array('server'=>$server[0], 'available'=>$bits[1], 'auth'=>$bits[2], 'clean'=>$bits[3], 'hilite'=>$bits[4], 'extra'=>$server[1]);
				    }
			    }
			    foreach( $tlds as $tld ){
				    $tld = trim($tld);
				    $bits = explode('=', $tld);
				    if( count($bits) == 2 && $bits[0] != '' && isset($this->m_serversettings[$bits[1]])){
					    $this->m_usetlds[$bits[0]] = true;
					    $this->m_tlds[$bits[0]] = $bits[1];
				    }
			    }

		    }

		    function SetTlds($tlds = 'com,net,org,info,biz,us,co.uk,org.uk'){
			    $tlds = strtolower($tlds);
			    $tlds = explode(',',$tlds);
			    $this->m_usetlds = array();
			    foreach( $tlds as $t ){
				    $t = trim($t);
				    if( isset($this->m_tlds[$t]) ) $this->m_usetlds[$t] = true;
			    }
			    return count($this->m_usetlds);
		    }

		    function Lookup($domain){
			    $domain = strtolower($domain);
			    $this->m_servers = array();
			    $this->m_data = array();
			    $this->m_tld = $this->m_sld = '';
			    $this->m_domain = $domain;
			    if( $this->splitdomain($this->m_domain, $this->m_sld, $this->m_tld) ){
				    $this->m_servers[0] = $this->m_tlds[$this->m_tld];
				    $this->m_data[0] = $this->dolookup($this->m_serversettings[$this->m_servers[0]]['extra'].$domain, $this->m_servers[0]);
				    if( $this->m_data[0] != '' ){
					    if( $this->m_serversettings[$this->m_servers[0]]['auth'] != '' && $this->m_redirectauth && $this->m_status == STATUS_UNAVAILABLE){
						    if( preg_match('/'.$this->m_serversettings[$this->m_servers[0]]['auth'].'(.*)/i', $this->m_data[0], $match) ){
							    $server = trim($match[1]);
							    if( $server != '' ){
								    $this->m_servers[1] = $server;
								    $command = isset($this->m_serversettings[$this->m_servers[1]]['extra']) ? $this->m_serversettings[$this->m_servers[1]]['extra'] : '';
								    $dt = $this->dolookup($command.$this->m_domain, $this->m_servers[1]);
								    $this->m_data[1] = $dt;
							    }
						    }
					    }
					    return true;
				    }else{
					    return false;
				    }
			    }
			    return false;
		    }


		    function ValidDomain($domain){
			    $domain = strtolower($domain);
			    return $this->splitdomain($domain, $sld, $tld);
		    }

		    function GetDomain(){
			    return $this->m_domain;
		    }

		    function GetServer($i = 0){
			    return isset($this->m_servers[$i]) ? $this->m_servers[$i] : '';
		    }

		    function GetData($i = -1){
			    if( $i != -1 && isset($this->m_data[$i])){
				    $dt = htmlspecialchars(trim($this->m_data[$i]));
				    $this->cleandata($this->m_servers[$i], $dt);
				    return $dt;
			    }else{
				    return trim(join("\n", $this->m_data));
			    }
			    return '';
		    }


		    function splitdomain($domain, &$sld, &$tld){
			    $domain = strtolower($domain);
			    $sld = $tld = '';
			    $domain = trim($domain);
			    $pos = strpos($domain, '.');
			    if( $pos != -1){
				    $sld = substr($domain, 0, $pos);
				    $tld = substr($domain, $pos+1);
				    if( isset($this->m_usetlds[$tld]) && $sld != '' ) return true;
			    }else{
				    $tld = $domain;
			    }
			    return false;
		    }

		    function whatserver($domain){
			    $sld = $tld = '';
			    $this->splitdomain($domain, $sld, $tld);
			    $server = isset($this->m_usetlds[$tld]) ? $this->m_tlds[$tld] : '';
			    return $server;
		    }

		    function dolookup($domain, $server){
			    $domain = strtolower($domain);
			    $server = strtolower($server);
			    if( $domain == '' || $server == '' ) return false;

			    $data = "";
			    $fp = @fsockopen($server, 43,$errno, $errstr, $this->m_connectiontimeout);
			    if( $fp ){
				    @fputs($fp, $domain."\r\n");
				    @socket_set_timeout($fp, $this->m_sockettimeout);
				    while( !@feof($fp) ){
					    $data .= @fread($fp, 4096);
				    }
				    @fclose($fp);

				    return $data;
			    }else{
				    return "\nError - could not open a connection to $server\n\n";
			    }
		    }

		    function cleandata($server, &$data){
			    if( isset($this->m_serversettings[$server]) ){
				    $clean = $this->m_serversettings[$server]['clean'];
				    if( $clean != '' ){
					    $from = $clean[0];
					    if( $from == '>' || $from == '<' ){
						    $clean = substr($clean,1);
						    $pos = strpos(strtolower($data), strtolower($clean));
						    if( $pos !== false ){
							    if( $from == '>' ){
								    $data = trim(substr($data, 0, $pos));
							    }else{
								    $data = trim(substr($data, $pos+strlen($clean)));
							    }
						    }
					    }
				    }
			    }
		    }


		}
	}

	function getwhois($domain, $tld) {
		$whois = new Whois(); //@TODO: Broken

		if( !$whois->ValidDomain($domain . '.' . $tld) ) {
			return 'Sorry, "' . $domain . '.' . $tld . '" is not valid or not supported.';
		}

		if ( $whois->Lookup($domain . '.' . $tld) ) {
			return $whois->GetData(1);
		} else {
			return 'A WHOIS error occurred.';
		}
	}


}