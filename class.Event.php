<?php
/**
 * @file class.Event.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.Events.Event
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 */

namespace Gino\App\Events;

use \Gino\Loader;
use \Gino\TagField;
use \Gino\ImageField;
use \Gino\FileField;
use \Gino\BooleanField;
use \Gino\ManyToManyField;
use \Gino\SlugField;
use \Gino\Db;

/**
 * @brief Classe tipo Gino.Model che rappresenta un evento.
 *
 * @version 1.0.0
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author Marco Guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */
class Event extends \Gino\Model
{
    public static $table = 'events_event';
    public static $table_ctgs = 'events_event_category';

    private static $_extension_img = array('jpg', 'jpeg', 'png');
    private static $_extension_attachment = array('txt', 'doc', 'docx', 'odt', 'pdf', 'xls', 'xlsx', 'zip', 'jpg', 'png');

    /**
     * @brief Costruttore
     * @param int $id id dell'evento
     * @param \Gino\App\Events\events $instance istanza del controller Gino.App.Events.events
     * @return istanza di Gino.App.Events.Event
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            'name' => _('nome'),
            'slug' => array(_('slug'), _('url parlante, viene calcolato automaticamente inserendo prima la data e poi il nome.')),
            'date' => _('data'),
            'duration' => _('durata'),
            'description' => _('descrizione'),
            'tags'=>array(_('Tag'), _("elenco tag separati da virgola")),
            'img' => _('immagine'),
            'attachment' => _('allegato'),
            'private' => _('privato'),
            'lat' => array(_('latitudine'), _('utilizzare il tool di conversione indirizzo')),
            'lng' => array(_('longitudine'), _('utilizzare il tool di conversione indirizzo')),
            'social' => _('condivisione social'),
            'published' => _('pubblicato'),
            'categories' => _('categorie'),
        );

        parent::__construct($id);

        $this->_model_label = _('Evento');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     * @return nome evento
     */
    function __toString()
    {
        return (string) $this->ml('name');
    }

    /**
     * @brief Definizione della struttura del modello
     *
     * @see Gino.Model::structure()
     * @param $id id dell'istanza
     * @return array, struttura del modello
     */
    public function structure($id)
    {
        $structure = parent::structure($id);

        $structure['slug'] = new SlugField(array(
            'name' => 'slug',
            'model' => $this,
            'required' => TRUE,
            'autofill' => array('date', 'name')
        ));

        $structure['tags'] = new TagField(array(
            'name' => 'tags',
            'model' => $this,
            'model_controller_class' => 'events',
            'model_controller_instance' => $this->_controller->getInstance()
        ));

        $base_path = $this->_controller->getBaseAbsPath() . OS . 'img';
        $structure['img'] = new ImageField(array(
            'name'=>'img',
            'model' => $this,
            'extensions'=>self::$_extension_img,
            'path'=>$base_path,
            'resize'=>true,
            'thumb'=>true,
            'width'=>$this->_controller->getImgWidth(),
            'thumb_width'=>$this->_controller->getThumbWidth()
        ));

        $base_path = $this->_controller->getBaseAbsPath() . OS . 'attachment';
        $structure['attachment'] = new FileField(array(
            'name'=>'attachment',
            'model' => $this,
            'extensions'=>self::$_extension_attachment,
            'path'=>$base_path
        ));

        $structure['private'] = new BooleanField(array(
            'name'=>'private',
            'model'=>$this,
            'enum'=>array(1 => _('si'), 0 => _('no'))
        ));

        $structure['social'] = new BooleanField(array(
            'name'=>'social',
            'model'=>$this,
            'enum'=>array(1 => _('si'), 0 => _('no'))
        ));

        $structure['published'] = new BooleanField(array(
            'name'=>'published',
            'model'=>$this,
            'enum'=>array(1 => _('si'), 0 => _('no'))
        ));

        $structure['categories'] = new ManyToManyField(array(
            'name'=>'categories',
            'model'=>$this,
            'm2m'=>'\Gino\App\Events\Category',
            'join_table'=>self::$table_ctgs,
            'm2m_where'=>'instance=\''.$this->_controller->getInstance().'\'',
            'm2m_controller'=>$this->_controller,
            'add_related' => true,
            'add_related_url' => $this->_controller->linkAdmin(array(), 'block=ctg&insert=1')
        ));

        //$structure['lat']->setWidget('hidden');
        //$structure['lng']->setWidget('hidden');

        return $structure;
    }

    /**
     * @brief Restituisce il numero di eventi che soddisfano le condizioni date 
     * 
     * @param \Gino\App\Events\events $controller istanza del controller Gino.App.Events.events
     * @param array $options array associativo di opzioni 
     * @return numero di eventi
     */
    public static function getCount($controller, $options = null) {

        $res =0;

        $where = \Gino\gOpt('where', $options, '');

        $db = Db::instance();
        return $db->getNumRecords(self::$table, $where);

    }

    /**
     * @brief Url dettaglio evento
     * @return url
     */
    public function getUrl()
    {
        return $this->_registry->router->link($this->_controller, 'detail', array('id' => $this->slug));
    }

    /**
     * @brief Url dettaglio evento per chiamate ajax
     * @return url
     */
    public function getAjaxUrl()
    {
        return $this->_registry->router->link($this->_controller, 'detail', array('id' => $this->slug), "ajax=1");
    }

    /**
     * @brief Url immagine
     * @return url
     */
    public function getImgUrl() {

        return $this->_controller->getBasePath().'/img/'.$this->img;

    }

    /**
     * @brief Path assoulto immagine
     * @return path
     */
    public function getImgPath() {

        return $this->_controller->getBaseAbsPath().OS.'img'.OS.$this->img;

    }

    /**
     * @brief Url allegato
     * @return url
     */
    public function getAttachmentUrl() {

        return $this->_controller->link($this->_controller, 'download', array('id' => $this->id));
    }

    /**
     * @brief Data nel formato 'domenica 5 febbraio 2014'
     * @param \Datetime $date oggetto Datetime
     * @return data
     */
    private function letterDate($date) {
        $days = array(_('domenica'), _('lunedì'), _('martedì'), _('mercoledì'), _('giovedì'), _('venerdì'), _('sabato'));
        $months = array(_('gennaio'), _('febbraio'), _('marzo'), _('aprile'), _('maggio'), _('giugno'), _('luglio'), _('agosto'), _('settembre'), _('ottobre'), _('novembre'), _('dicembre'));
        return sprintf('%s <span>%d %s</span> %d', $days[$date->format('w')], $date->format('j'), $months[$date->format('n') - 1], $date->format('Y'));
    }

    /**
     * @brief Data inizio evento in formato letterale
     * @see self::letterDate
     * @return data
     */
    public function beginLetterDate() {
        return $this->letterDate(new \Datetime($this->date));
    }

    /**
     * @brief Data fine evento in formato letterale
     * @see self::letterDate
     * @return data
     */
    public function endLetterDate() {
        $end_date = new \Datetime($this->date);
        $end_date->modify('+'.($this->duration - 1).'days');
        return $this->letterDate($end_date);
    }

    /**
     * @brief Data inizio in formato iso 8601
     *
     * @return data iso 8601
     */
    public function startDateIso()
    {
        $datetime = new \Datetime($this->date);
        return $datetime->format('c');
    }

    /**
     * @brief Data fine in formato iso 8601
     *
     * @return data iso 8601
     */
    public function endDateIso()
    {
        $end_date = new \Datetime($this->date);
        $end_date->modify('+'.($this->duration - 1).'days');
        return $end_date->format('c');
    }

}
