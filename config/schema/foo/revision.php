<?php

/**
 * Revision
 *
 * Creates Table schemafor RevisionablBehavior
 *
 * @package     revisionable
 * @subpackage  revisionable.config.schema
 * @uses                CakeSchema
 * @author      Analogrithems <analogrithems@gmail.com>
 * @license             Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 * @copyright   Copyright (c) 2011 Analogrithems
 */
class Revision extends CakeSchema {

        public $name = 'Revision';

        public  $revisions = array(
                'id' => array('type' => 'char', 'length'=>36, 'null' => false, 'default' => NULL, 'key' => 'primary'),
                'row_id' => array('type' => 'char', 'length'=>36, 'null' => false, 'default' => NULL),
                'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'index'),
                'data' => array('type' => 'longblob', 'null' => false, 'default' => NULL, 'key' => 'index'),
                'created' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'key' => 'index'),
                'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'polymorphic_idx' => array('column' => array('model', 'row_id'), 'unique' => 0)),
                'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB')
        );

}

?>
