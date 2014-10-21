<?php 

/*
@package Custom Error Log
@subpackage Admin

This file holds the output for the admin error log under the 'Tools' menu.
*/

$errors = get_option( 'custom_error_log', true );
$notices = get_option( 'custom_notice_log', true );

/* These variables are used to see if both errors and otices exist... */
$have_errors = $have_notices = $have_both = false;

/* Build the log array... */
$logs = array();

/* If there are any errors logged add them to the array... */
if( $errors && $errors['errors'] ) {

	$errors = $errors['errors'];
	$logs = array_merge_recursive( $logs, $errors );
	$have_errors = true;
	
}

/* If there are any notices logged add them to the array... */
if( $notices && $notices['notices'] ) {
	
	$notices = $notices['notices'];
	$logs = array_merge_recursive( $logs, $notices );
	$have_notices = true;
	
}

/* If both errors and notices exist switch $have_both to true... */
if( $have_errors && $have_notices ) {

	$have_both = true;
	
}

/*
Start building the page...
*/

?>

<h1><?php __( 'Error Logs', 'custom-error-log' ); ?></h1>

<div class="wrap" id="error-log">

	<div id="cel-ajax-message"></div>

	<?php
	
	/* If there are any logs create the log table... */
	if( $logs ) {

		$count = 1;
		$row_number = 1;
		$row_class = 'cel-table-row';
		
		$nonce = wp_create_nonce( 'cel_nonce' );
		
		uasort( $logs, 'cel_sort_by_date' );
		
		/* If there are both notices and errors output filter buttons... */
		if( $have_both == true ) { ?>
			
			<a class="cel-log-filter" filter="all" nonce="<?php echo $nonce; ?>">All</a> |
			
			<a class="cel-log-filter" filter="error" nonce="<?php echo $nonce; ?>">Errors</a> |

			<a class="cel-log-filter" filter="notice" nonce="<?php echo $nonce; ?>">Notices</a>

		<?php } ?>
		
		<a class="cel-delete-all" data-nonce="<?php echo $nonce; ?>">Clear Log</a>
		
		<table class="cel-table">
		
			<thead>
	
				<tr>
			    	
			    	<th class="cel-type"></th>
			    
			    	<th class="cel-date"><?php _e( 'Date', 'custom-error-log' ); ?></th>
			    	
			    	<th class="cel-time"><?php _e( 'Time', 'custom-error-log' ); ?></th>
			    	
			    	<th class="cel-message"><?php _e( 'Message', 'custom-error-log' ); ?></th>
			    	
					<th class="cel-delete"></th>
	
				</tr>
	
			</thead>
	    
			<tbody>
			
				<?php
				
				/*
				Output all logs into the table...
				*/
				
				$output = '';
				
				foreach( $logs as $log ) {
				    
					$output .= '<tr class="' . $row_class . ' cel-' . $log['type'] . '" id="' . $log['type'] . '-' . $log['id'] . '">';
					$output .= '<td class="cel-type-' . $log['type'] . '"></td>';
					$output .= '<td class="cel-date">' . date_i18n( 'd/m/y', $log['date'] ) . '</td>';
					$output .= '<td class="cel-time">' . date_i18n( 'g.i a', $log['date'] ) . '</td>';
					$output .= '<td class="cel-message">' . $log['message'] . '</td>';
					$output .= '<td class="cel-delete">';
					$output .= '<a class="cel-delete-button" rel="' . $log['id'] . '" data-error-code="' . $log['id'] . '" data-nonce="' . $nonce . '">';
					$output .= '</a></td></tr>';
					
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
		
				echo $output;
	
				?>

			</tbody>
	    	
		</table>
        
	<?php 
	
	}
	
	/* If there are no logs output the introduction text from introduction.php... */
	else { 

		include( CEL_DIR . '/admin/introduction.php' );

	} 
	
	?>

</div>