<?php
/**
* @file newsletter.php
* @brief Template per la visualizzazione di eventi all'interno di newsletter
*
* Variabili disponibili:
* - **event**: \Gino\App\Events\Event oggetto evento Gino.App.Events.Event
*
* @version 2.1.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\Events; ?>
<? //@cond no-doxygen ?>
<section>
    <h1><?= \Gino\htmlChars($event->ml('name')) ?></h1>
    <time><?= \Gino\dbDateToDate($event->date) ?></time>
    <?= \Gino\htmlChars($event->ml('description')) ?>
 </section>
<? // @endcond ?>
