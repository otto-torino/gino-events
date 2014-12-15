<?php
/**
* @file detail.php
* @brief Template per la vista dettaglio evento
*
* Variabili disponibili:
* - **instance_name**: string, nome istanza modulo
* - **item**: \Gino\App\Events\Event istanza evento Gino.App.Events.Event
* - **related_contents_list**: html, lista di link a risorse correlate
* - **social**: html, condivisione social
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<section id="detail-events-<?= $instance_name ?>" itemscope itemtype="http://schema.org/Event">
    <h1 itemprop="name"><?= \Gino\htmlChars($item->ml('name')) ?></h1>
    <div class='row'>
    <? if($item->img or ($item->lng and $item->lat)): ?>
        <div class='col-md-4 col-sm-12'>
            <? if ($item->img): ?>
                <img class="img-responsive" style="margin-bottom: 20px;" src="<?= $item->getImgUrl() ?>" alt="<?= 'img' ?>"/>
            <? endif ?>
            <? if($item->lat and $item->lng): ?>
                <div id="event-map" style="width: 100%; height: 200px; margin-bottom: 20px;"></div>
                <script>
                    options = {
                        center: new google.maps.LatLng('<?= $item->lat ?>', '<?= $item->lng ?>'),
                        zoom: 12
                    };
                    var map = new google.maps.Map(document.getElementById('event-map'), options);
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng('<?= $item->lat ?>', '<?= $item->lng ?>'),
                        map: map
                    });
                </script>
            <? endif ?>
            <? if($item->attachment): ?>
                <p><span class="fa fa-paperclip"></span> <a href="<?= $item->getAttachmentUrl() ?>"><?= $item->attachment ?></a></p>
            <? endif ?>
        </div>
        <div class='col-md-8 col-sm-12'>
    <? else: ?>
        <div class='col-md-12'>
    <? endif ?>
            <p class="events-time">
                <time itemprop="startDate" content="<?= $item->startDateIso() ?>"><?= \Gino\htmlChars($item->beginLetterDate()); ?></time>
                <? if($item->duration > 1): ?>
                    - <time itemprop="endDate" content="<?= $item->endDateIso() ?>"><?= \Gino\htmlChars($item->endLetterDate()); ?></time>
                <? endif ?>
        </p>
        <div itemprop="description"><?= \Gino\htmlChars($item->ml('description')) ?></div>
        <? if(!$item->img and (!$item->lat or !$item->lng) and $item->attachment): ?>
            <p style="margin-top: 20px;"><span class="fa fa-paperclip"></span> <a href="<?= $item->getAttachmentUrl() ?>"><?= $item->attachment ?></a></p>
        <? endif ?>
        <? if($social): ?>
            <?= $social ?>
        <? endif ?>
        <? if($related_contents_list): ?>
            <h2><?= _('Potrebbe interessarti anche...') ?></h2>
            <?= $related_contents_list ?>
        <? endif ?>
        </div>
</div>
</section>
<? // @endcond ?>
