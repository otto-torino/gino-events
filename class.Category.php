<?php
/**
 * @file class.Category.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.Events.Category
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 */

namespace Gino\App\Events;

use \Gino\Db;
use \Gino\SlugField;

/**
 * @brief Classe tipo Gino.Model che rappresenta una categoria di eventi.
 *
 * @version 1.0.0
 * @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class Category extends \Gino\Model
{
    public static $table = 'events_category';

    /**
     * @brief Costruttore
     * @param int $id id della categoria
     * @param \Gino\App\Events\events $instance istanza del controller Gino.App.Events.events
     * @return istanza di Gino.App.Events.Category
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            'name' => _('nome'),
            'slug' => array(_('slug'), _('url parlante')),
            'description' => _('descrizione'),
        );

        parent::__construct($id);

        $this->_model_label = _('Categoria');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     * @return nome categoria
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
            'autofill' => array('name')
        ));

        return $structure;
    }

    /**
     * @brief Restituisce una lista id=>name da utilizzare per un menu a tendina 
     *
     * @param \Gino\App\Events\events $controller istanza del controller Gino.App.Events.events
     * @param array $options array associativo di opzioni (where, order)
     * @return array associativo id=>name
     */
    public static function getForSelect($controller, $options = null) {

        $res = array();

        $where_q = \Gino\gOpt('where', $options, '');
        $order = \Gino\gOpt('order', $options, 'name');

        $db = Db::instance();
        $selection = 'id, name';
        $table = self::$table;
        $where_arr = array("instance='".$controller->getInstance()."'");
        if($where_q) {
            $where_arr[] = $where_q;
        } 
        $where = implode(' AND ', $where_arr);

        $rows = $db->select($selection, $table, $where, array('order'=>$order));
        if(count($rows)) {
            foreach($rows as $row) {
                $session = \Gino\Session::instance();
        		$trd = new \Gino\Translation($session->lng, $session->lngDft);
        		$res[$row['id']] = \Gino\htmlChars($trd->selectTXT($table, 'name', $row['id']));
            }
        }

        return $res;
    }

}
