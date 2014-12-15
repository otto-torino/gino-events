<?php
/**
* @file showcase.php
* @brief Template per la vista vetrina eventi
*
* Variabili disponibili:
* - **instance_name**: string, nome istanza modulo
* - **instance**: int, id istanza modulo
* - **items**: array, oggetti Gino.App.Events.Event
* - **feed_url**: string, url ai feed RSS
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<section id="showcase-events-<?= $instance_name ?>">
    <h1>
        <?= _('Vetrina') ?> 
        <a href="<?= $feed_url ?>" class="fa fa-rss"></a>
    </h1>
    <? if(count($items)): ?>
        <div id="showcase-wrapper-events-<?= $instance_name ?>">
        <? $tot = count($items); $i = 0; ?>
        <? foreach($items as $item): ?>
            <div class='showcase-item' style='display: block;z-index:<?= ($tot-$i) ?>' id="events-<?= $instance ?>-<?= $i ?>">
                <article>
                    <h1><?= \Gino\htmlChars($item->ml('name')); ?></h1>
                    <?= \Gino\htmlChars(\Gino\cutHtmlText($item->ml('description'), 150, '...', false, false, true, array('endingPosition'=>'in'))) ?>
                </article>
            </div>
            <? $i++ ?>
        <? endforeach ?>
        </div>
        <table>
            <tr>
            <? $tot = count($items); $i = 0; ?>
            <? foreach($items as $item): ?>
                <td>
                <div id="sym-<?= $instance ?>-<?= $i ?>" class="scase-sym" onclick="events_slider.set(<?= $i ?>)"><span></span></div>
                </td>
                <? $i++ ?>
            <? endforeach ?>
            </tr>
        </table>
        <script type="text/javascript">
            var events_slider;
            window.addEvent('load', function() {
                events_slider = new events.Slider('showcase-wrapper-events-<?= $instance_name ?>', 'sym-<?= $instance ?>-', {auto_start: true, auto_interval: 5000});
            });
        </script>
    <? endif ?>
</section>
<? // @endcond ?>
