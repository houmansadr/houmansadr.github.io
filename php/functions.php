<?php

////////////////////////////////////////////////////
/// Update the lines below to fit your situation ///
////////////////////////////////////////////////////


/**
  * List noisy ips here e.g. bots
  */
function get_noisy_visitors() {
 
	return array(	"google-bot-1" => "66.249.72.89",
		      "google-bot-2" => "66.249.71.206",
			"google-bot-3" => "66.249.73.231",
			"google-bot-4"=>"66.249.75.135", 
		     "yahoo-bot" => "72.30.142.253",
			"hawker" => "128.83.141.3",
			"msnbot" => "157.55.32.88",
			"msnbot2" => "157.55.32.110",
			"msnbot3" => "157.55.35.88",
			"exabot" =>"178.255.215.79",
			"baidu" =>"220.181.108.172", 
			"webcrawl-umass" =>"128.119.100.152"                 
                );	
}

/**
  * The path to the log file. 
  * Don't mess with the log file manually! 
  * You'll get auto-reports for your website visit every day (given there's at least one visitor per day)
  */
function get_log_file() {

	return "./php/log.txt";
}

/**
  * Get the e-mail address the report is e-mailed from
  */
function get_website_agent() {

	return "amir@cs.umass.edu";
}

/**
  * Get the e-mail address where you will receive the report
  */
function get_report_recipient() {

	return "houmansadr@gmail.com";
}

/////////////////////////////////////////////////////////////////////
/// Modify below this line only if you know what you are doing!!! ///
/////////////////////////////////////////////////////////////////////


/**
  * Log the time, ip and hostname of a visitor
  */
function log_visitor( $ip, $page ) {

	if( should_disregard_visitor( $ip ) ) {
		return;
	}

	// static set up
	$log = get_log_file();
	$log_file_permission_set = 'aw';

	// this is hardcoded to this content provider. Other providers will work in a different way
/**	$content_provider = "http://aruljohn.com/track.pl?host=";	**/
	$content_provider = "http://aruljohn.com/ip/";
	$hostname_prefix = "<tr><td>Hostname</td><td>";
	$hostname_suffix = "</td></tr>";
	$isp_prefix = "<tr><td>ISP</td><td>";
	$isp_suffix = "</td></tr>";
	$country_prefix = "<tr><td>Country</td><td>";

	// get the time and date of this visit
	$time = date('l jS F Y h:i:s A');

	// hold your breath ... we'll get you the hostname of the visitor
	$hostname = "";
	$isp = "";
	$country = "";


	// find out who's visiting us
	$contents = file_get_contents( $content_provider . $ip );

	// break down the string to individual lines
	$page_arr = explode("\n", $contents);

	// go over the array and find the hostname of our visitor
	foreach ( $page_arr as $line ) {
	    	
		// is this the line containing the hostname?
		$pos = strpos( $line, $hostname_prefix );

		if( $pos !== false ) {
		
			// remove garbage tags
			$hostname = substr( $line, strlen( $hostname_prefix ) );
			$hostname = substr( $hostname, 0, strlen( $hostname ) - strlen( $hostname_suffix ) );
		
		}

		// is this the line containing the isp?
		$isp_pos = strpos( $line, $isp_prefix );

		if( $isp_pos !== false ) {
		
			// remove garbage tags
			$isp = substr( $line, strlen( $isp_prefix ) );
			$isp = substr( $isp, 0, strlen( $isp ) - strlen( $isp_suffix ) );

		}

		// is this the line containing the country?
		$country_pos = strpos( $line, $country_prefix );

		if( $country_pos !== false ) {
		
			// remove garbage tags
			$country = substr( $line, strlen( $country_prefix ) );

			$img_tag_pos = strpos( $country, "<img" );

			$country = substr( $country, 0, $img_tag_pos );

			// found the country. break outta the loop. we don't need anything else from this page
			break;
		}
	}

	// build up the log description for this visitor
	$visitor = "IP = " . $ip . "\n" . 
		   "Host = " .$hostname . "\n" . 
		   "ISP = " . $isp . "\n" .
		   "Country = " . $country . "\n" .
		   "Viewed: " . $page . "\n" . 
                   "On: " . $time . "\n\n";


	// check when the last visitor was seen at the scene
	$time_of_last_visit = date( "d", filemtime( $log ) );
	$time_now = date("d");

	if( $time_of_last_visit != $time_now ) {
		
		// +1 day. email report and clear the log
		
		// get the report
		$report = file_get_contents( $log );

		// mail the report		
		mailer( $report );

		// clear the report file
		$log_file_permission_set = 'w';
	}

	// get a file descriptor to the log file
	$oFile = fopen( $log, $log_file_permission_set ) or die( "" );

	// remember this visit
	fwrite( $oFile, $visitor );

	// make sure that we won't get screwed from the Internet
	chmod( $log, 0666 );

	// release the file descriptor
	fclose( $oFile );
}


/**
  * Email the report from last night
  */
function mailer( $message ) {
	
	$to      = get_report_recipient();
	$subject = 'Website Visitor Report For ' . date("F j, Y");
	$headers = 'From: Website Agent <' . get_website_agent() . '>' . "\r\n" . 'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
}


/**
  * Disregard noisy visitors
  */
function should_disregard_visitor( $ip ) {
	
	$ips =  get_noisy_visitors();	

	if ( !in_array( $ip, $ips ) ) {
		return false;
	} else {
		return true;
	}
}
?>
