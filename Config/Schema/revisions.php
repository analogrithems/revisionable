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
class RevisionsSchema extends CakeSchema {
        var $name = 'Revisions';

        function before($event = array()) {
                return true;
        }

        function after($event = array()) {
        }


        var $revisions = array(
                'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'primary', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
                'row_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 36, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
                'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
                'data' => array('type' => 'binary', 'null' => false, 'default' => NULL),
                'created' => array('type' => 'timestamp', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'),
                'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'row_id' => array('column' => array('row_id', 'model'), 'unique' => 0)),
                'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
        );
}
