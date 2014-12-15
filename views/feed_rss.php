<?php
/**
* @file feed_rss.php
* @brief Template feed RSS
*
* Variabili disponibili:
* - **title**: string, titolo feed
* - **description**: string, descrizione feed
* - **request**: \Gino\Http\Request, istanza di Gino.Http.Request
* - **events**: array, array di oggetti Gino.App.Events.Event
*
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="<?= $request->absolute_url ?>" rel="self" type="application/rss+xml" />
        <title><?= $title ?></title>
        <link><?= $request->root_absolute_url ?></link>
        <description><?= $description ?></description>
        <language><?= $request->session->lng ?></language>
        <docs>http://blogs.law.harvard.edu/tech/rss</docs>
        <?php if(count($events) > 0): ?>
        <?php foreach($events as $item): ?>
            <?php $id = \Gino\htmlChars($item->id); ?>
            <?php $title = \Gino\htmlChars($item->ml('name')); ?>
            <?php $text = \Gino\htmlChars($item->ml('description')); ?>
            <?php $text = str_replace("src=\"", "src=\"".$request->root_absolute_url, $text); ?>
            <?php $text = str_replace("href=\"", "href=\"".$request->root_absolute_url, $text); ?>
            <?php $date = \date('d/m/Y', strtotime($item->date)); ?>
            <item>
                <title><?= sprintf('%s. %s', $date, $title) ?></title>
                <link><?= $request->root_absolute_url . $item->getUrl() ?></link>
                <description>
                <![CDATA[
                <?= $text ?>
                ]]>
                </description>
                <guid><?= $request->root_absolute_url . $item->getUrl() ?></guid>
            </item>
        <?php endforeach ?>
        <?php endif ?>
    </channel>
</rss>
<? // @endcond ?>
