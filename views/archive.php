<?php
/**
* @file archive.php
* @brief Template per la vista archivio eventi
*
* Variabili disponibili:
* - **instance_name**: string, nome istanza modulo
* - **items**: array, eventi @ref Gino.App.Events.Event
* - **search_form**: html, form di ricerca
* - **open_form**: bool, TRUE se il form deve essere mostrato espanso perché compilato
* - **feed_url**: string, url ai feed RSS
* - **pagination**: html, paginazione
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<section id="archive-events-<?= $instance_name ?>">
    <h1><?= _('Archivio eventi') ?> <a style="margin-left: 20px" class="fa fa-rss" href="<?= $feed_url ?>"></a> <span class="fa fa-search link" style="margin-right: 10px;" onclick="if($('events_form_search').style.display == 'block') $('events_form_search').style.display = 'none'; else $('events_form_search').style.display = 'block';"></span></h1>
    <div id="events_form_search" style="display: <?= $open_form ? 'block' : 'none'; ?>;">
        <?= $search_form ?>
    </div>
    <? if(count($items)): ?>
        <table class='table table-striped table-hover'>
            <tr>
                <th><?= _('Data') ?></th>
                <th><?= _('Durata') ?></th>
                <th><?= _('Evento') ?></th>
            </tr>
            <? foreach($items as $item): ?>
            <tr>
                <td><?= \Gino\dbDateToDate($item->date, '/') ?></td>
                <td><?= $item->duration.' '.($item->duration > 1 ? _('giorni') : _('giorno')) ?></td>
                <td><a href="<?= $item->getUrl() ?>"><?= $item->ml('name') ?></a></td>
            </tr>
            <? endforeach ?>
        </table>
        <?= $pagination ?>
    <? else: ?>
        <p><?= _('Non risultano eventi') ?></p>
    <? endif ?>
</section>
<? // @endcond ?>
