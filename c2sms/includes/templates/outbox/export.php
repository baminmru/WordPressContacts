<div class="wrap">
	<h2><?php _e('Export', 'wp-c2sms'); ?></h2>
	<form id="export-filters" method="post" action="<?php echo plugins_url('../../../export.php',__FILE__); ?>">
		<table>
			<tr valign="top">
				<th scope="row">
					<label for="export-file-type"><?php _e('Export To', 'wp-c2sms'); ?>:</label>
				</th>
			
				<td>
					<select id="export-file-type" name="export-file-type" style="width:300px;">
						<option value="0"><?php _e('Please select.', 'wp-c2sms'); ?></option>
						<option value="excel">Excel</option>
						<option value="xml">XML</option>
						<option value="csv">CSV</option>
						<option value="tsv">TSV</option>
					</select>
					<p class="description"><?php _e('Select the output file type.', 'wp-c2sms'); ?></p>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<a href="admin.php?page=wp-c2sms-outbox" class="button"><?php _e('Back', 'wp-c2sms'); ?></a>
					<input type="submit" class="button-primary" name="wps_export_outbox" value="<?php _e('Export', 'wp-c2sms'); ?>" />
				</td>
			</tr>
		</table>
	</form>
</div>