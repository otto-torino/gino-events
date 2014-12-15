<?php
/**
* @file calendar.php
* @brief Template per la vista calendario
*
* Variabili disponibili:
* - **instance_name**: string, nome istanza modulo
* - **select**: html, input select per la scelta categoria
* - **router**: \Gino\Router, istanza di Gino.Router
* - **feed_url**: string, url ai feed RSS
* - **json_url**: string, url per ricavare eventi mese anno in json
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<section id="calendar-events-<?= $instance_name ?>">
    <h1><?= _('Calendario eventi') ?></h1>
    <div id="calendar"></div>
    <div id="calendar-controllers" style="display: none;">
        <span class="fa fa-2x fa-calendar" id="month_view"></span>
        <span class="link fa fa-2x fa-bars" onclick="location.href='<?= $router->link($instance_name, 'archive') ?>month/' + (calendar.getCurrentMonth() + 1) + '/year/' + calendar.getCurrentYear() + '/ctg/' + $('category_filter').get('value')"></span>
        <a href="<?= $feed_url ?>" class="fa fa-2x fa-rss icon"></a>
        <div class="right"><?= $select ?></div>
        <div class='clear'></div>
    </div>
    <script>
        var calendar = new events.Calendar({
            json_url: '<?= $json_url ?>',
            month_view_ctrl: 'month_view',
            onComplete: function() { $('calendar-controllers').style.display = 'block'; }
        });
        calendar.render('calendar');
    </script>
</section>
<? // @endcond ?>
