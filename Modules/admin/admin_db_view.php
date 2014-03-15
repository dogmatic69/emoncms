<?php
echo implode('', array(
	sprintf('<h2>%s</h2>', _('Database setup, update and status check')),
	sprintf('<p>%s</p>', _('This page displays the output of the database setup and update process which checks the database requirements of each module installed and enter any new table or fields if required.')),
	sprintf('<p>%s</p>', _('If all the item statuses below show ok that means your database is setup correctly.')),
));
?>
<br>
<table class="table" >
    <?php
    	echo sprintf('<tr><th>%s</th><th>%s</th><th>%s</th></tr>', _('Schema item'), _('Name'), _('Status'));
    	$i = 0;
    	foreach ($out as $line) { 
    		$i++; 
    		if ($line[0] == 'Table') { 
    			echo sprintf('<tr class="d%s"><th>%s</th><th>%s</th><th>%s</th></tr>', $i & 1, $line[0], $line[1], $line[2]);
    		} 
    		if ($line[0] == 'field') {
    			echo sprintf('<tr class="d%s"><td><i>%s</i></td><td><i>%s</i></td><td><i>%s</i></td></tr>', $i & 1, $line[0], $line[1], $line[2]);
    		}
    	}
    ?>
</table>
