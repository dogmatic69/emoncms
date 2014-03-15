<?php
$out = '';
foreach ($updates as $update) {
    if ($update['operations']) {
        $done = false;

        foreach ($update['operations'] as &$operation) {
            $operation = sprintf('<tr><td>%s</td></tr>', $operation);
        }

        $out .= implode('', array(
            sprintf('<h4>%s</h4>', $update['title']),
            sprintf('<p>%s</p>', $update['description']),
            sprintf('<table class="table table-striped ">%s</table>', implode('', $update['operations'])),
        ));
    }
}

echo sprintf('<br/><h2>%s</h2>', _("Update database"));
if ($out) {
    global $path;

    if (!$applychanges) {
        echo sprintf('<div class="alert alert-block"><p><b>Todo:</b> - these changes need to be applied</p><br>%s</div>', $out);
        echo sprintf('<a href="%sadmin/db&apply=true" class="btn btn-info">%s</a>', $path, _('Apply changes'));
    } else {
        echo sprintf('<div class="alert alert-success"><p><b>Success:</b> - the following changes have been applied</b></p><br>%s</div>', $out);
        echo sprintf('<a href="%sadmin/db" class="btn btn-info">%s</a>', $path, _('Check for further updates'));
    }
} else {
    echo sprintf('<div class="alert alert-success"><strong>%s</strong> - %s</div>', _('Database is up to date '), _('Nothing to do'));
}
