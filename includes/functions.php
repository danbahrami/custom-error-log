<?php

/*
@package Custom Error Log
@subpackage Includes

This file does the main work of the plugin

log_error() function, this allows developers to log custom errors in their theme/plugin...
*/

function log_error( $message ) {

	/* Get error logs from the wp_options table... */
	$error_log = get_option( 'custom_error_log', true );

	if( empty( $error_log ) ) {
	
		$error_log = array(
		
			'errors' => array(),
			'next_error' => 1
			
		);
		
	}
	
	/* Insert new error into array... */
	$error_log['errors'][$error_log['next_error']] = array(
	
		'type'			=> 'error',
		'date'			=> current_time( 'timestamp' ),
		'id'			=> $error_log['next_error'],
		'message'		=> sanitize_text_field( $message )
		
	);
	
	/* Increase the error code to use for the next error logged... */
	$error_log['next_error']++;
	
	/* Update the error log in the wp_options table... */
	update_option( 'custom_error_log', $error_log );

}

/*
log_notice() function, this allows developers to log custom notices in their theme/plugin...
*/

function log_notice( $message ) {

	/* Get notice logs from the wp_options table... */
	$notice_log = get_option( 'custom_notice_log', true );

	if( empty( $notice_log ) ) {
	
		$notice_log = array(
		
			'notices' => array(),
			'next_notice' => 1
			
		);
		
	}
	
	/* Insert new notice into array... */
	$notice_log['notices'][$notice_log['next_notice']] = array(
	
		'type'			=> 'notice',
		'date'			=> current_time( 'timestamp' ),
		'id'			=> $notice_log['next_notice'],
		'message'		=> sanitize_text_field( $message )
		
	);
	
	/* Increase the notice code to use for the next error logged... */
	$notice_log['next_notice']++;
	
	/* Update the notice log in the wp_options table... */
	update_option( 'custom_notice_log', $notice_log );

}

/*
cel_delete_single() gets used by the error log table to delete a single error or notice from the array...
*/

function cel_delete_single() {

	/* Check that the nonce is correct to avoid safety issues... */
	if ( !wp_verify_nonce( $_POST['nonce'], 'cel_nonce' ) ) {

		exit( 'Wrong nonce' );
        
	}
	
	/* Get information about the error to delete from the ajax POST... */
	$error_code = $_POST['error_code'];
	$log_type = $_POST['log_type'];
	
	/* Get the correct log from the wp_options table... */
	$logs = get_option( 'custom_' . $log_type . '_log', true );
	
	/* Unset the correct error/notice from the array... */
	foreach( $logs[$log_type . 's'] as $key => $log ) {
		
		if( $log['id'] == $error_code ) {
			
			unset( $logs[$log_type . 's'][$key] );
			
		}
		
	}
	
	/* Update the log in the wp_options table... */
	$update = update_option( 'custom_' . $log_type . '_log', $logs );
	
	/* Build the response... */
	if( $update ) {

		$return = '<div class="updated  ajax-response">';
		$return .= sprintf( __( '%s %d has been successfully deleted.', 'custom-error-log' ), $log_type, $error_code );
		$return .= '</div>';
			
	}

	else {

		$return = '<div class="error  ajax-response">';
		$return .= sprintf( __( '%s %d could not be deleted.', 'custom-error-log' ), $log_type, $error_code );
		$return .= '</div>';

	}
	
	/* Send the response back to the ajax call... */
	die( $return );

}

add_action( 'wp_ajax_nopriv_cel_delete_single', 'cel_delete_single' );
add_action( 'wp_ajax_cel_delete_single', 'cel_delete_single' );

/*
cel_delete_all() gets used by the error log table to clear all errors and notices...
*/

function cel_delete_all() {
	
	/* Check that the nonce is correct to avoid safety issues... */
	if ( !wp_verify_nonce( $_POST['nonce'], 'cel_nonce' ) ) {

		exit( 'Wrong nonce' );
        
	}
	
	/* Empty fields stored in the wp_options table... */
	$error_log = get_option( 'custom_error_log', true );
	$notice_log = get_option( 'custom_notice_log', true );
	
	$error_log['errors'] = array();
	$notice_log['notices'] = array();
    
	$deleted_errors = update_option( 'custom_error_log', $error_log );
	$deleted_notices = update_option( 'custom_notice_log', $notice_log );
	
	/* Build the response */
	if( $deleted_errors && $deleted_notices ) {

		$return = '<div class="updated  ajax-response">';
		$return .= __( 'All errors have been deleted.', 'custom-error-log' );
		$return .= '</div>';

	}

	else {
    
		$return = '<div class="error  ajax-response">';
		$return .= __( 'Errors could not be deleted.', 'custom-error-log' );
		$return .= '</div>';

	}
	
	/* Send response back to ajax call... */
	die( $return );

}

add_action( 'wp_ajax_nopriv_cel_delete_all', 'cel_delete_all' );
add_action( 'wp_ajax_cel_delete_all', 'cel_delete_all' );

/*
cel_sort_by_date() gets used by the error log table to sort all errors and notices by date...
*/

function cel_sort_by_date( $a, $b ) {

	if ( $a['date'] == $b['date'] ) {
	
		return 0;
		
	}

	return ( $a['date'] < $b['date'] ) ? 1 : -1;
	
}

/*
cel_filter_log() filters the error log table so that only errors or notices are displayed...
*/

function cel_filter_log() {

	/* Check that the nonce is correct to avoid safety issues... */
	if ( !wp_verify_nonce( $_POST['nonce'], 'cel_nonce' ) ) {

		exit( 'Wrong nonce' );
	    
	}
	
	$filter = $_POST['filter'];
	
	/* If there is no filter show all logs... */
	if( $filter == 'all' ) {
	
	    $errors = get_option( 'custom_error_log', true );
	    $notices = get_option( 'custom_notice_log', true );
	    
	    $logs = array_merge_recursive( $errors['errors'], $notices['notices'] );
	    
	}
	
	/* Else filter logs based on specific type... */
	else {
	
	    $logs = get_option( 'custom_' . $filter . '_log', true );
	    $logs = $logs[$filter . 's'];
	
	}
	    
	$count = 1;
	$row_number = 1;
	$row_class = 'cel-table-row';
	
	if( !$logs ) {
	
		return __( 'There was an error filtering the log', 'custom-error-log' );
	
	}
	
	/* Sort logs into date order... */
	uasort( $logs, 'cel_sort_by_date' );
	
	/* Create output for each log... */
	$return = '';
	
	foreach( $logs as $log ) {
		        
		$return .= '<tr class="' . $row_class . ' cel-' . $log['type'] . '" id="' . $log['type'] . '-' . $log['id'] . '">';
		$return .= '<td class="cel-type-' . $log['type'] . '"></td>';
		$return .= '<td class="cel-date">' . date_i18n( 'd/m/y', $log['date'] ) . '</td>';
		$return .= '<td class="cel-time">' . date_i18n( 'g.i a', $log['date'] ) . '</td>';
		$return .= '<td class="cel-message">' . $log['message'] . '</td>';
		$return .= '<td class="cel-delete">';
		$return .= '<a class="cel-delete-button" rel="' . $log['id'] . '" data-error-code="' . $log['id'] . '" data-nonce="' . $_POST['nonce'] . '">';
		$return .= '</a></td></tr>';
		
		if( $count == 1 ) {
		
			$row_class = 'cel-table-row cel-dark';
			$count++;
			
		}
		
		else {
		
			$count = 1;
			$row_class = 'cel-table-row';
			
		}
		
		$row_number++;
		
	}
	
	/* Send output back to ajax call... */
	die( $return );
	    
}

add_action( 'wp_ajax_nopriv_cel_filter_log', 'cel_filter_log' );
add_action( 'wp_ajax_cel_filter_log', 'cel_filter_log' );