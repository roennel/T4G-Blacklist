<div id="alxSystemLog">
<span>alxSystemLog</span>
<table cellspacing="0">
	<tr>
		<th>Date</th>
		<th>Micro</th>
		<th>Diff</th>
		<th>Total &nbsp;&nbsp;&raquo; µs</th>
		<th>Type</th>
		<th>Description</th>
		<th>Data</th>
	</tr>
	<?php 
		$lastMicro = null;
		
		$log = $data->log;
		
		foreach($data->log as $date => $item): 
			list($micro) = explode(' ', $date); 
			
			reset($log);
			$fm = (float) key($log);
			$om = $micro;
			$total = number_format($om - $fm, 5);
			
			$micro = str_replace('0.', null, (string) number_format($micro, 7));
			if(!$lastMicro) $lastMicro = $micro;
			
	?>
	<tr class="<?php echo $item->type ?>">
		<td style="width: 150px"><?php echo date('d.m.Y H:i:s') ?></td>
		<td style="width: 40px; text-align: center"><?php echo $micro ?></td>
		<td style="width: 40px; text-align: center">+<?php echo $micro - $lastMicro ?></td>
		<td style="width: 80px; text-align: center"><?php echo $total ?></td>
		<td style="width: 20px; text-align: center"><?php echo $item->type ?></td>
		<td style="width: 220px"><?php echo $item->description ?></td>
		<td style="width: 330px"><?php var_dump($item->data) ?></td>
	</tr>
	<?php 
		$lastMicro = $micro;
		endforeach;
	?>
</table>
</div>
<style type="text/css">
	
	#body
	{
		background-color: #000000;
	}	
	
	#alxSystemLog
	{
			background-color: #212121;
			max-width: 800px;
			padding: 20px;
			color: #ffffff;
			border-radius: 10px;
	}
	
	#alxSystemLog span
	{
		font-size: 14pt;
		font-weight: bold;
		font-family: tahoma;
	}
	
	#alxSystemLog table
	{
		margin-top: 7px;
		font-family: tahoma;
		font-size: 8pt;
		width: 800px;
	}
	
	#alxSystemLog table tr th, #alxSystemLog table tr td
	{
		text-align: left;
		border-bottom: 1px solid black;
		padding: 5px;
		padding-right: 20px;
		border-radius: 3px 3px 0px 0px;
		color: #ffffff;
		font-size: 11pt;
	}
	
	#alxSystemLog table tr.log td
	{
		background-color: #00CC00;
		color: #000000;
		font-size: 8pt;
		padding-right: 5px;
	}
	
	#alxSystemLog table tr.notice td
	{
		background-color: #CCCC00;
	}
	
	#alxSystemLog table tr.fatal td
	{
		background-color: #CC0000;
	}
	
</style>