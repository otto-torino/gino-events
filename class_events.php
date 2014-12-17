<?php
/**
 * @file class_events.php
 * @brief Contiene la definizione della classe Gino.App.Events.events
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti
 * @author abidibo
 */

/**
 * @namespace Gino.App.Events
 * @description Namespace dell'applicazione Eventi
 */
namespace Gino\App\Events;

use \Gino\Error;
use \Gino\Loader;
use \Gino\View;
use \Gino\GTag;
use \Gino\Session;
use \Gino\Javascript;
use \Gino\Registry;
use \Gino\App\Module\ModuleInstance;

require_once('class.Event.php');
require_once('class.Category.php');

/**
 * @brief Classe di tipo Gino.Controller del modulo Eventi
 *
 * @version 1.0.0
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author Marco Guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */
class events extends \Gino\Controller
{

    /* options */
    private $_monday_first_week_day,
            $_day_chars,
            $_open_link_in_layer,
            $_ifp,
            $_showcase_events_number,
            $_showcase_events_category,
            $_img_width,
            $_thumb_width,
            $_newsletter_events_number;

    private $_tbl_opt;

    /**
     * @brief Costruttore
     * @param $instance_id id istanza
     * @return istanza di Gino.App.Events.events
     */
    public function __construct($instance_id)
    {
        parent::__construct($instance_id);

        /* options */
        $this->_tbl_opt = 'events_opt';
        $this->_optionsValue = array(
            'monday_first_week_day' => 1,
            'day_chars' => 1,
            'open_link_in_layer' => 0,
            'ifp' => 10,
            'showcase_events_number' => 5,
            'showcase_events_category' => '',
            'img_width' => '600',
            'thumb_width' => '120',
            'newsletter_events_number' => 10
        );
        $this->_monday_first_week_day = $this->setOption('monday_first_week_day', array('value' => $this->_optionsValue['monday_first_week_day']));
        $this->_day_chars = $this->setOption('day_chars', array('value' => $this->_optionsValue['day_chars']));
        $this->_open_link_in_layer = $this->setOption('open_link_in_layer', array('value' => $this->_optionsValue['open_link_in_layer']));
        $this->_ifp = $this->setOption('ifp', array('value' => $this->_optionsValue['ifp']));
        $this->_showcase_events_number = $this->setOption('showcase_events_number', array('value' => $this->_optionsValue['showcase_events_number']));
        $this->_showcase_events_category = $this->setOption('showcase_events_category', array('value' => $this->_optionsValue['showcase_events_category']));
        $this->_img_width = $this->setOption('img_width', array('value' => $this->_optionsValue['img_width']));
        $this->_thumb_width = $this->setOption('thumb_width', array('value' => $this->_optionsValue['thumb_width']));
        $this->_newsletter_events_number = $this->setOption('newsletter_events_number', array('value' => $this->_optionsValue['newsletter_events_number']));

        $this->_options = loader::load('Options', array($this));
        $this->_optionsLabels = array(
            'monday_first_week_day'=>array('label' => _('Lunedì primo giorno della settimana'), 'section' => true, 'section_title' => _('Calendario'), 'value' => $this->_monday_first_week_day),
            'day_chars' => array('label' => _('Numero di caratteri rappresentazione giorno'), 'value' => $this->_day_chars),
            'open_link_in_layer' => array('label' => _('Apri link su layer'), 'value' => $this->_open_link_in_layer),
            'ifp'=>array('label' => _('Eventi per pagina'), 'section' => true, 'section_title' => _('Archivio'), 'value' => $this->_ifp),
            'showcase_events_number'=>array('label' => _('Numero di eventi mostrati'), 'section' => true, 'section_title' => _('Vetrina'), 'value' => $this->_showcase_events_number),
            'showcase_events_category' => array('label' => array(_('ID Categoria eventi'), _('Se non impostato gli eventi verranno pescati da tutte le categorie')), 'value' => $this->_showcase_events_category),
            'img_width'=>array('label' => _('Larghezza massima'), 'section' => true, 'section_title' => _('Immagini'), 'value' => $this->_img_width),
            'thumb_width' => array('label' => _('Larghezza thumb'), 'value' => $this->_thumb_width),
            'newsletter_events_number' => array('label' => _('Numero di eventi esportati'), 'section' => true, 'section_title' => _('Newsletter'), 'section_description' => _('Queste opzioni vengono utilizzate dal modulo newsletter di gino. Il modulo deve essere installato separatamente.'), 'value' => $this->_newsletter_events_number)
        );
    }

    /**
     * @brief Restituisce alcune proprietà della classe utili per la generazione di nuove istanze
     * @return lista delle proprietà utilizzate per la creazione di istanze di tipo events (tabelle, css, viste, folders)
     */
    public static function getClassElements() 
    {
        return array(
            "tables"=>array(
                'events_event',
                'events_event_category',
                'events_category',
                'events_opt',
            ),
            "css"=>array(
                'events.css',
            ),
            "views" => array(
                'archive.php' => _('Archivio'),
                'detail.php' => _('Dettaglio'),
                'calendar.php' => _('Calendario'),
                'showcase.php' => _('Vetrina'),
                'feed_rss.php' => _('Feed RSS'),
                'newsletter.php' => _('Eventi esportati in newsletter'),
            ),
            "folderStructure"=>array (
                CONTENT_DIR.OS.'events'=> array(
                    'attachment' => null,
                    'img' => null
                )
            )
        );
    }

    /**
     * @brief Metodo invocato quando viene eliminata un'istanza di tipo eventi
     * Si esegue la cancellazione dei dati da db e l'eliminazione di file e directory 
     * @return TRUE
     */
    public function deleteInstance() 
    {
        $this->requirePerm('can_admin');

        /* eliminazione eventi */
        Event::deleteInstance($this);
        /* eliminazione categorie */
        Category::deleteInstance($this);

        /*
         * delete record and translation from table events_opt
         */
        $opt_id = $this->_db->getFieldFromId($this->_tbl_opt, "id", "instance", $this->_instance);
        \Gino\Translation::deleteTranslations($this->_tbl_opt, $opt_id);
        $result = $this->_db->delete($this->_tbl_opt, "instance=".$this->_instance);

        /*
         * delete css files
         */
        $classElements = $this->getClassElements();
        foreach($classElements['css'] as $css) {
            unlink(APP_DIR.OS.$this->_class_name.OS.\Gino\baseFileName($css)."_".$this->_instance_name.".css");
        }

        /* eliminazione views */
        foreach($classElements['views'] as $k => $v) {
            unlink($this->_view_dir.OS.\Gino\baseFileName($k)."_".$this->_instance_name.".php");
        }

        /*
         * delete folder structure
         */
        foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
            \Gino\deleteFileDir($fld.OS.$this->_instance_name, TRUE);
        }

        return TRUE;
    }

    /**
     * @brief Metodi pubblici disponibili per inserimento in layout (non presenti nel file events.ini) e menu (presenti nel file events.ini)
     * @return lista metodi NOME_METODO => array('label' => LABEL, 'permissions' = PERMISSIONS)
     */
    public static function outputFunctions()
    {
        $list = array(
            "archive" => array("label"=>_("Archivio eventi"), "permissions"=>array()),
            "showcase" => array("label"=>_("Vetrina eventi"), "permissions"=>array()),
            "calendar" => array("label"=>_("Calendario eventi"), "permissions"=>array()),
            "feedRSS" => array("label"=>_("Feed RSS"), "permissions"=>array()),
        );

        return $list;
    }

    /**
     * @brief Getter dell'opzione larghezza massima immagini
     * @return larghezza massima immagini
     */
    public function getImgWidth()
    {
        return $this->_img_width;
    }

    /**
     * @brief Getter dell'opzione larghezza THUMB
     * @return larghezza THUMB
     */
    public function getThumbWidth()
    {
        return $this->_thumb_width;
    }

    /**
     * @brief Esegue il download clientside del documento indicato da url ($doc_id)
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @throws Gino.Exception.Exception404 se il documento non viene trovato
     * @throws Gino.Exception.Exception403 se il documento è associato ad una news che non si può visualizzare
     * @return Gino.Http.ResponseFile
     */
    public function download(\Gino\Http\Request $request) {

        $doc_id = \Gino\cleanVar($request->GET, 'id', 'int');

        if(!empty($doc_id)) {
            $e = new Event($doc_id, $this);
            if(!$e->id) {
                throw new \Gino\Exception\Exception404();
            }
            if(!$this->userHasPerm('can_view_private') && $e->private) {
                throw new \Gino\Exception\Exception403();
            }

            $attachment = $e->attachment;
            if($attachment) {
                $full_path = $this->getBaseAbsPath().OS.'attachment'.OS.$attachment;
                return \Gino\download($full_path);
            }
            else {
                throw new \Gino\Exception\Exception404();
            }
        }

        throw new \Gino\Exception\Exception404();
    }

   /**
     * @brief showcase
     * @return html, vista vetrina
     */
    public function showcase()
    {
        $this->_registry->addJs($this->_class_www.'/events.js');
        $this->_registry->addJs($this->_class_www.'/events_locale.js');
        $this->_registry->addCss($this->_class_www.'/events_'.$this->_instance_name.'.css');

        $where = array(
            "instance='".$this->_instance."'",
            "published='1'"
        );
        if(!$this->userHasPerm('can_view_private')) {
            $where[] = "private='0'";
        }
        if($this->_showcase_events_category) {
            $where[] = "id IN (SELECT event_id FROM ".Event::$table_ctgs." WHERE category_id = '".$this->_showcase_events_category."')";
        }

        $limit = array(0, $this->_showcase_events_number);
        $items = Event::objects($this, array('where' => implode(' AND ', $where), 'limit' => $limit, 'order' => 'date ASC'));

        $view = new View($this->_view_dir, 'showcase_'.$this->_instance_name);
        $dict = array(
            'items' => $items,
            'instance' => $this->_instance,
            'instance_name' => $this->_instance_name,
            'feed_url' => $this->link($this->_instance_name, 'feedRSS')
        );

        return $view->render($dict);
    }

    /**
     * @brief Calendario
     * @return html, vista calendario
     */
    public function calendar()
    {

        $this->_registry->addJs($this->_class_www.'/events.js');
        $this->_registry->addJs($this->_class_www.'/events_locale.js');
        $this->_registry->addCss($this->_class_www.'/events_'.$this->_instance_name.'.css');

        $form = Loader::load('Form', array('', '', ''));
        $select = $form->select('category', '', Category::getForSelect($this), array(
            'noFirst' => true,
            'firstVoice' => _('tutte le categorie'),
            'firstValue' => 0,
            'js' => "onchange=\"calendar.setRequestData('ctg=' + $(this).value); calendar.requestMonthData()\"",
            'id' => 'category_filter'
        ));

        $view = new View($this->_view_dir, 'calendar_'.$this->_instance_name);
        $dict = array(
            'json_url' => $this->link($this->_instance_name, 'getMonthEventsJSON'),
            'feed_url' => $this->link($this->_instance_name, 'feedRSS'),
            'instance_name' => $this->_instance_name,
            'router' => \Gino\Router::instance(),
            'select' => $select
        );

        return $view->render($dict);
    }

    /**
     * @brief Json eventi mese, anno e categoria dati
     * @param \Gino\Http\Redirect $redirect istanza di Gino.Http.Redirect
     * @return Gino.Http.ResponseJson
     */
    public function getMonthEventsJSON(\Gino\Http\Request $request)
    {
        $ctg_id = \Gino\cleanVar($request->POST, 'ctg', 'int');
        $month = \Gino\cleanVar($request->POST, 'month', 'int');
        $year = \Gino\cleanVar($request->POST, 'year', 'int');
        $month = $month < 10 ? '0'.$month : $month;
        $month_days = date('t', mktime(0, 0, 0, $month, 1, $year));

        $where = array(
            "instance='".$this->_instance."'",
            "published='1'",
            "date <= '".$year."-".$month."-".$month_days."' AND DATE_ADD(date, INTERVAL duration DAY) >= '".$year."-".$month."-1'"
        );

        if(!$this->userHasPerm('can_view_private')) {
            $where[] = "private='0'";
        }

        if($ctg_id != 0) {
            $where[] = "id IN (SELECT event_id FROM ".Event::$table_ctgs." WHERE category_id='".$ctg_id."')";
        }

        $events = Event::objects($this, array(
            'where' => implode(' AND ', $where),
            'order' => 'date ASC'
        ));

        $items = array();
        foreach($events as $event) {
            $ctgs = array();
            foreach($event->categories as $ctg_id) {
                $ctg = new Category($ctg_id, $this);
                $ctgs[] = \Gino\htmlChars($ctg->ml('name'));
            }
            $items[] = array(
                'name' => \Gino\htmlChars($event->ml('name')),
                'description' => implode(',', $ctgs),
                'date' => $event->date,
                'day' => substr($event->date, 8, 2),
                'url' => $event->getUrl(),
                'onclick' => $this->_open_link_in_layer
                    ? "var w = new gino.layerWindow({ title: '"._('Dettaglio evento')."', width: 800, reloadZindex: true, url: '".$event->getAjaxUrl()."' }); w.display();"
                    : ''
            );

            if($event->duration > 1) {
                $date = new \Datetime($event->date);
                for($i = 1; $i < $event->duration; $i++) {
                    $date->modify('+1 days');
                    $items[] = array(
                        'name' => \Gino\htmlChars($event->ml('name')),
                        'description' => implode(',', $ctgs),
                        'date' => $date->format('Y-m-d'),
                        'day' => $date->format('d'),
                        'url' => $event->getUrl(),
                        'onclick' => $this->_open_link_in_layer
                            ? "var w = new gino.layerWindow({ title: '"._('Dettaglio evento')."', width: 800, reloadZindex: true, url: '".$event->getAjaxUrl()."' }); w.display();"
                            : ''
                    );
                }
            }
        }

        \Gino\Loader::import('class/http', '\Gino\Http\ResponseJson');
        return new \Gino\Http\ResponseJson($items);
    }

    /**
     * @brief Vista dettaglio evento
     * @description Il metodo puo' essere chiamato con parametro GET ajax=1 per non stampare tutto il documento
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @throws Gino.Exception.Exception404 se l'evento non viene trovato
     * @throws Gino.Exception.Exception403 se non si hanno i permessi per visualizzare l'evento
     * @return Gino.Http.Response
     */
    public function detail(\Gino\Http\Request $request)
    {

        $this->_registry->addJs("http://maps.googleapis.com/maps/api/js?key=AIzaSyArAE-uBvCZTRaf_eaFn4umUdESmoUvoxM&sensor=true");
        $this->_registry->addCss($this->_class_www.'/events_'.$this->_instance_name.'.css');
        $slug = \Gino\cleanVar($request->GET, 'id', 'string');
        $event = Event::getFromSlug($slug, $this);

        $ajax = \Gino\cleanVar($request->GET, 'ajax', 'int');

        if(!$event->id) {
            throw new \Gino\Exception\Exception404();
        }

        if(!$this->userHasPerm('can_view_private') and $event->private) {
            throw new \Gino\Exception\Exception403();
        }

        $view = new View($this->_view_dir, 'detail_'.$this->_instance_name);

        if($event->social) {
            $social = \Gino\shareAll('st_all_large', $this->link($this->_instance_name, 'detail', array('id'=>$event->slug), array(), array('abs' => TRUE)), \Gino\htmlChars($event->ml('title')));
        }
        else {
            $social = null;
        }

        $dict = array(
            'instance_name' => $this->_instance_name,
            'item' => $event,
            'related_contents_list' => $this->relatedContentsList($event),
            'social' => $social
        );

        if($ajax) {
            return new \Gino\Http\Response($view->render($dict));
        }

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Lista di contenuti correlati per tag
     * @param \Gino\App\Events\Event $item istanza di Gino.App.Events.Event
     * @return html, lista contenuti correlati
     */
    public function relatedContentsList($item)
    {
        $related_contents = GTag::getRelatedContents('Event', $item->id);
        if(count($related_contents)) {
            $view = new View(null, 'related_contents_list');
            return $view->render(array('related_contents' => $related_contents));
        }
        else return '';
    }

    /**
     * @brief Vista archivio eventi
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Response
     */
    public function archive(\Gino\Http\Request $request)
    {

        $ctg_id = \Gino\cleanVar($request->GET, 'ctg', 'int');
        $month = \Gino\cleanVar($request->GET, 'month', 'int');
        $year = \Gino\cleanVar($request->GET, 'year', 'int');

        $where = array(
            "instance='".$this->_instance."'",
            "published='1'"
        );
        if(!$this->userHasPerm('can_view_private')) {
            $where[] = "private='0'";
        }

        if($month and $year) {
            $month_days = date('t', mktime(0, 0, 0, $month, 1, $year));
            $date_from = "$year-".($month < 10 ? '0'.$month : $month).'-01';
            $date_to = "$year-".($month < 10 ? '0'.$month : $month).'-'.$month_days;
        }
        else {
            $date_from = null;
            $date_to = null;
        }
        $session = Session::instance();
        $this->sessionSearch($ctg_id, $date_from, $date_to);

        $open_form = false;
        if($session->{'eventsSearch'.$this->_instance}['category']) {
            $where[] = "id IN (SELECT event_id FROM ".Event::$table_ctgs." WHERE category_id='".$session->{'eventsSearch'.$this->_instance}['category']."')";
            $open_form = true;
        }
        if($session->{'eventsSearch'.$this->_instance} and $session->{'eventsSearch'.$this->_instance}['text']) {
            $where[] = "(name LIKE '%".$session->{'eventsSearch'.$this->_instance}['text']."%' OR description LIKE '%".$session->{'eventsSearch'.$this->_instance}['text']."%')";
            $open_form = true;
        }
        if($session->{'eventsSearch'.$this->_instance}['date_from']) {
            $where[] = "date >= '".\Gino\dateToDbDate($session->{'eventsSearch'.$this->_instance}['date_from'], '/')."'";
            $open_form = true;
        }
        if($session->{'eventsSearch'.$this->_instance}['date_to']) {
            $where[] = "date <= '".\Gino\dateToDbDate($session->{'eventsSearch'.$this->_instance}['date_to'], '/')."'";
            $open_form = true;
        }

        $items_number = Event::getCount($this, array('where'=>implode(' AND ', $where)));

        $paginator = Loader::load('Paginator', array($items_number, $this->_ifp));
        $limit = $paginator->limitQuery();
        $items = Event::objects($this, array('where' => implode(' AND ', $where), 'limit' => $limit, 'order' => 'date ASC'));

        $view = new View($this->_view_dir, 'archive_'.$this->_instance_name);
        $dict = array(
            'instance_name' => $this->_instance_name,
            'items' => $items,
            'search_form' => $this->formSearch(),
            'open_form' => $open_form,
            'feed_url' => $this->link($this->_instance_name, 'feedRSS'),
            'pagination' => $paginator->pagination()
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Form di ricerca in archivio 
     * @return html, form ricerca archivio
     */
    private function formSearch() {

        $session = Session::instance();

        $myform = Loader::load('Form', array('form_search_events', 'post', FALSE));
        $form_search = $myform->open($this->Link($this->_instance_name, 'archive'), FALSE, '');
        $form_search .= $myform->cselect('search_category', $session->{'eventsSearch'.$this->_instance}['category'], Category::getForSelect($this), _('Categoria'), array());
        $form_search .= $myform->cinput_date('search_from', $session->{'eventsSearch'.$this->_instance}['date_from'], _('Da'), array());
        $form_search .= $myform->cinput_date('search_to', $session->{'eventsSearch'.$this->_instance}['date_to'], _('A'), array());
        $form_search .= $myform->cinput('search_text', 'text', \Gino\htmlInput($session->{'eventsSearch'.$this->_instance}['text']), _('Titolo/Descrizione'), array('size'=>20, 'maxlength'=>40));
        $submit_all = $myform->input('submit_search_all', 'submit', _('tutti'), array('classField'=>'submit'));
        $form_search .= $myform->cinput('submit_search', 'submit', _('cerca'), '', array('classField'=>'submit', 'text_add'=>' '.$submit_all));
        $form_search .= $myform->close();

        return $form_search;
    }

    /**
     * @brief Imposta la ricerca in sessione 
     * @param $ctg_id id categoria
     * @return void
     */
    private function sessionSearch($ctg_id = null, $date_from = null, $date_to = null) {

        $session = Session::instance();
        $request = \Gino\Http\Request::instance();

        if(isset($request->POST['submit_search_all'])) {
            $search = null;
            $session->{'eventsSearch'.$this->_instance} = $search;
        }

        if(!$session->{'eventsSearch'.$this->_instance}) {
            $search = array(
                'category' => null,
                'text' => null,
                'date_from' => null,
                'date_to' => null
            );
        }

        if(isset($request->POST['submit_search']) or $ctg_id or $date_from or $date_to) {
            if($ctg_id or $date_from or $date_to) {
                $search['category'] = $ctg_id;
                $search['text'] = null;
                $search['date_from'] = $date_from ? \Gino\dbDateToDate($date_from) : null;
                $search['date_to'] = $date_to ? \Gino\dbDateToDate($date_to) : null;
            }

            if(isset($request->POST['search_category'])) {
                $search['category'] = \Gino\cleanVar($request->POST, 'search_category', 'int');
            }
            if(isset($request->POST['search_text'])) {
                $search['text'] = \Gino\cleanVar($request->POST, 'search_text', 'string');
            }
            if(isset($request->POST['search_from'])) {
                $search['date_from'] = \Gino\cleanVar($request->POST, 'search_from', 'string');
            }
            if(isset($request->POST['search_to'])) {
                $search['date_to'] = \Gino\cleanVar($request->POST, 'search_to', 'string');
            }
            $session->{'eventsSearch'.$this->_instance} = $search;
        }

    }

    /**
     * @brief Interfaccia amministrazione modulo
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Response
     */
    public function manageDoc(\Gino\Http\Request $request)
    {
        $this->requirePerm(array('can_admin', 'can_publish', 'can_write'));

        $block = \Gino\cleanVar($request->GET, 'block', 'string');

        $link_frontend = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=frontend'), _('Frontend'));
        $link_options = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=options'), _('Opzioni'));
        $link_ctg = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=ctg'), _('Categorie'));
        $link_dft = sprintf('<a href="%s">%s</a>', $this->linkAdmin(), _('Eventi'));
        $sel_link = $link_dft;

        if($block == 'frontend' && $this->userHasPerm('can_admin')) {
            $backend = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        elseif($block=='options') {
            $backend = $this->manageOptions();
            $sel_link = $link_options;
        }
        elseif($block=='ctg') {
            $backend = $this->manageCategory($request);
            $sel_link = $link_ctg;
        }
        else {
            $backend = $this->manageEvent($request);
        }

        if(is_a($backend, '\Gino\Http\Response')) {
            return $backend;
        }

        // groups privileges
        if($this->userHasPerm('can_admin')) {
            $links_array = array($link_frontend, $link_options, $link_ctg, $link_dft);
        }
        else {
            $links_array = array($link_ctg, $link_dft);
        }

        $view = new View(null, 'tab');
        $dict = array(
          'title' => _('Gestione eventi'),
          'links' => $links_array,
          'selected_link' => $sel_link,
          'content' => $backend
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Interfaccia di amministrazione eventi
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Redirect oppure html, interfaccia di back office
     */
    public function manageEvent(\Gino\Http\Request $request)
    {
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $edit = \Gino\cleanVar($request->GET, 'edit', 'int');
        $insert = \Gino\cleanVar($request->GET, 'insert', 'int');

        $buffer = '';
        if($insert or $edit) {
            // geolocalization
            \Gino\Loader::import('class', '\Gino\Javascript');
            $buffer .= \Gino\Javascript::abiMapLib();
            $buffer .= "<script type=\"text/javascript\">";
            $buffer .= "function convert() {
                    var addressConverter = new AddressToPointConverter('map_coord', 'lat', 'lng', $('map_address').value, {'canvasPosition':'over'});
                    addressConverter.showMap();
                }\n";
            $buffer .= "</script>";
            $onclick = "onclick=\"Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=convert')\"";
            $gform = Loader::load('Form', array('', '', ''));
            $convert_button = $gform->input('map_coord', 'button', _("converti"), array("id"=>"map_coord", "classField"=>"generic", "js"=>$onclick));

            $add_cell = array(
                'lat'=>array(
                    'name' => _('geolocalization'),
                    'field' => $gform->cinput('map_address', 'text', '', array(_("Indirizzo localizzazione evento"), _("es: torino, via mazzini 37<br />utilizzare 'converti' per calcolare latitudine e longitudine")), array("size"=>40, "maxlength"=>200, "id"=>"map_address", "text_add"=>"<p>".$convert_button."</p>"))
                )
            );

            $remove_fields = array();
            if(!$this->userHasPerm(array('can_publish', 'can_admin'))) {
                $remove_fields = array('published');
            }
        }
        else {
            $add_cell = array();
            $remove_fields = array();
        }

        // dft duration
        if(isset($request->POST['duration']) and !$request->POST['duration']) {
            $request->POST['duration'] = 1;
        }

        $backend = $admin_table->backOffice(
            'Event',
            array(
                'list_display' => array('id', 'name', 'date', 'duration', 'categories'),
                'list_title'=>_("Elenco eventi")
            ),
            array(
                'addCell' => $add_cell,
                'removeFields' => $remove_fields
            ),
            array(
                'date' => array(
                    'id' => 'date'
                ),
                'lat' => array(
                    'id' => 'lat'
                ),
                'lng' => array(
                    'id' => 'lng'
                ),
                'description' => array(
                    'widget'=>'editor',
                    'notes'=>TRUE,
                    'img_true'=>FALSE,
                )
            )
        );

        return (is_a($backend, '\Gino\Http\Response')) ? $backend : $buffer.$backend;
    }

    /**
     * @brief Interfaccia di amministrazione Category
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return Gino.Http.Response
     */
    public function manageCategory(\Gino\Http\Request $request)
    {
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $buffer = $admin_table->backOffice(
            'Category',
            array(
                'list_display' => array('id', 'name', 'slug'),
                'list_title'=>_("Elenco categorie"),
                'list_description'=>"<p>"._('Ciascun evento inserito potrà essere associato ad una o più categorie qui definite.')."</p>",
            ),
            array(),
            array(
                'description' => array(
                    'widget'=>'editor',
                    'notes'=>FALSE,
                    'img_preview'=>FALSE,
                )
            )
        );

        return $buffer;
    }

    /**
     * @brief Metodo per la definizione di parametri da utilizzare per il modulo "Ricerca nel sito"
     *
     * Il modulo "Ricerca nel sito" di Gino base chiama questo metodo per ottenere informazioni riguardo alla tabella, campi, pesi etc...
     * per effettuare la ricerca dei contenuti.
     *
     * @return array[string]mixed array associativo contenente i parametri per la ricerca
     */
    public function searchSite() {

        return array(
            "table"=>Event::$table,
            "selected_fields"=>array("id", "slug", "date", array("highlight"=>true, "field"=>"name"), array("highlight"=>true, "field"=>"description")), 
            "required_clauses"=>array("instance"=>$this->_instance), 
            "weight_clauses"=>array("name"=>array("weight"=>3), "description"=>array("weight"=>1))
        );
    }

    /**
     * @brief Definisce la presentazione del singolo item trovato a seguito di ricerca (modulo "Ricerca nel sito")
     *
     * @param array $results array associativo contenente i risultati della ricerca
     * @return html, presentazione item tra i risultati della ricerca
     */
    public function searchSiteResult($results) {

        $obj = new Event($results['id'], $this);

        $buffer = "<div>".\Gino\dbDatetimeToDate($results['date'], "/")." <a href=\"".$this->link($this->_instance_name, 'detail', array('id'=>$results['slug']))."\">";
        $buffer .= $results['name'] ? \Gino\htmlChars($results['name']) : \Gino\htmlChars($obj->ml('name'));
        $buffer .= "</a></div>";

        if($results['description']) $buffer .= "<div class=\"search_text_result\">...".\Gino\htmlChars($results['description'])."...</div>";

        return $buffer;

    }

    /**
     * @brief Adattatore per la classe newsletter 
     * @return array di elementi esportabili nella newsletter
     */
    public function systemNewsletterList() {

        $events = Event::objects($this, array('where' => "instance='".$this->_instance."' AND date>='".date('Y-m-d')."'", 'order'=>'date ASC', 'limit'=>array(0, $this->_newsletter_events_number)));

        $items = array();
        foreach($events as $e) {
            $items[] = array(
                    _('id') => $e->id,
                    _('nome') => \Gino\htmlChars($e->ml('name')),
                    _('privato') => $e->private ? _('si') : _('no'),
                    _('pubblicato') => $e->published ? _('si') : _('no'),
                    _('data') => \Gino\dbDateToDate($e->date),
            );
        }

        return $items;
    }

    /**
     * @brief Contenuto di un evento quanto inserito in una newsletter
     * @param int $id identificativo dell'evento
     * @return html, contenuto evento
     */
    public function systemNewsletterRender($id) {

        $e = new Event($id, $this);

        $view = new View($this->_view_dir, 'newsletter_'.$this->_instance_name);

        $dict = array(
            'event' => $e,
        );

        return $view->render($dict);

    }

    /**
     * @brief Genera un feed RSS standard che presenta i prossimi eventi
     * @param \Gino\Http\Request $request istanza di Gino.Http.Request
     * @return \Gino\Http\Response, feed RSS
     */
    public function feedRSS() {

        $title_site = $this->_registry->sysconf->head_title;
        $module = new ModuleInstance($this->_instance);
        $title = $module->label.' | '.$title_site;
        $description = $module->description;

        $items = Event::objects($this, array('where' => "instance='".$this->_instance."' AND published='1' AND private='0' AND date>='".date('Y-m-d')."'", 'order'=>'date DESC'));

        $view = new \Gino\View($this->_view_dir, 'feed_rss_'.$this->_instance_name);
        $dict = array(
            'title' => $title,
            'description' => $description,
            'request' => $this->_registry->request,
            'events' => $items
        );

        $response = new \Gino\Http\Response($view->render($dict));
        $response->setContentType('text/xml');
        return $response;

    }

}
