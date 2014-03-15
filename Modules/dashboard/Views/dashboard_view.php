<?php

/*
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org
*/

    global $session, $path; ?>

    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/dashboard_langjs.php"></script>
    <link href="<?php echo $path; ?>Modules/dashboard/Views/js/widget.css" rel="stylesheet">

    <script type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/Views/js/widgetlist.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/Views/js/render.js"></script>

    <script type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>

    <?php require_once 'Modules/dashboard/Views/loadwidgets.php'; ?>

    <div id="page-container" style="height:<?php echo $dashboard['height']; ?>px; position:relative;">
        <div id="page"><?php echo $dashboard['content']; ?></div>
    </div>

<script type="application/javascript">
    var dashid = <?php echo $dashboard['id']; ?>,
        path = "<?php echo $path; ?>",
        widget = <?php echo json_encode($widgets); ?>,
        apikey = "<?php echo get('apikey'); ?>",
        userid = <?php echo $session['userid']; ?>;

    for (z in widget) {
        var fn = window[widget[z] + '_widgetlist'];
        $.extend(widgets,fn());
    }

    var redraw = 1,
        reloadiframe = 0;

    show_dashboard();
    setInterval(function() { 
        update(); 
    }, 10000);
    setInterval(function() { 
        fast_update(); 
    }, 30);
</script>
