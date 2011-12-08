<?php
/**
 * RevisionableBehavior
 *
 * Create a revisionable behavior that when attached to a model will have a copy or each  row before it updates
 * uses the current date to identify the revision and adds functions to restore the data if nessecary.  
 * this behavior is able to backup data by using serialize and globs.  This means revisions aren't really searchable
 * but they are atleast stored perfectly
 *
 * @package     revisionable
 * @subpackage  revisionable.models.behaviors
 * @author      Analogrithems <analogrithems@gmail.com>
 */
class RevisionableBehavior extends ModelBehavior {

	protected $_defaults = array(
		'revisionableModel'=>'Revisionable.Revision'
	);
	public $_disabled = false;
	private $revModel;

	/**
	* Behavior configuration
	*
	* @param   object  $Model
	* @param   array   $config
	* @return  void
	*/
	public function setup(&$Model, $config = array()) {

		$config = (is_array($config) && !empty($config))
		    ? Set::merge($this->_defaults, $config)
		    : $this->_defaults;

		$this->settings[$Model->alias] = $config;

		$this->revModel = $this->getModel($this->settings[$Model->alias]['revisionableModel']);
	}


	/**
	 * Returns a reference to the model object specified, and attempts
	 * to load it if it is not found.
	 *
	 * @param string $name Model name (defaults to AuthComponent::$userModel)
	 * @return object A reference to a model object
	 * @access public
	 */
        function &getModel($name = null) {
                $model = null;
                if (!$name) {
                        $name = $this->userModel;
                }

                if (PHP5) {
                        $model = ClassRegistry::init($name);
                } else {
                        $model =& ClassRegistry::init($name);
                }

                if (empty($model)) {
                        trigger_error(__('Auth::getModel() - Model is not set or could not be found', true), E_USER_WARNING);
                        return null;
                }

                return $model;
        }

	/**
	 * beforeSave model callback
	 *
	 * Used to grab a copy of the current row before the save, this is how be make the revision
	 *
	 *
	 * @param  object $Model
	 * @return boolean
	 */
	 public function beforeSave(&$Model,$options = null) {

		//If we are disabled, or if this is not an update but a create, then dont make a revision yet
		if ($this->_disabled || !isset($Model->id) ) {
			return true;
		}

		//Set recurision to 1
		$Model->recursive = 1;

		$revision[$this->revModel->alias]['row_id'] = $Model->id;
		$revision[$this->revModel->alias]['model'] = $Model->alias;
		$revision[$this->revModel->alias]['data'] = serialize($Model->read());
	
		if($revisioned = $this->revModel->save($revision)){
			$this->log("Created a revision of {$Model->alias} / {$Model->id}",'debug');
			return true;
		}else{
			$this->log("Failed to created a revision of {$Model->alias} / {$Model->id} with:".print_r($revision,1),'error');
			return false;
		}
	 }
		
	/**
	 * revisions list a model/row's revisions
	 *
	 * Search through the revisions table for a list of all revisions for a model/row combination
	 *
	 * @param object $Model
	 * @param string/int $row_id  if using uuid this is a string, if using auto increment this is int
	 * @return mixed // array('YYYY-MM-DD HH:MM:SS'=>array('Model->alias'=>$data))
	 */
	 function revisions(&$Model, $row_id = null){
		if(!$row_id || !$Model){
			return false;
		}
		$results = array();
		$name = $this->revModel->alias;

		if($revisions = $this->revModel->find('all',array('conditions'=>array($name.'.model'=>$Model->alias, $name.'.row_id'=>$row_id)))){
			if(is_array($revisions)){
				foreach($revisions as $rev){
					$results[$rev[$name]['created']] = unserialize($rev[$name]['data']);
				}
				return($results);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * restoreVersionByDate restore a version based off it's date
	 *
	 * Restores a version back
	 * @param object $Model
	 * @param string/int $row_id  if using uuid this is a string, if using auto increment this is int
	 * @param string $date
	 * @return boolean
	 */
	 function restoreVersionByDate(&$Model, $row_id = null, $date = null){
		if(!$row_id || !$Model){
			return false;
		}
		$results = array();
		$name = $this->revModel->alias;
		if($revision = $this->revModel->find('first',array('conditions'=>array($name.'.model'=>$Model->alias, $name.'.row_id'=>$row_id, $name.'.date'=>$date)))){
			$revision = unserialize($revision[$this->revModel->alias]['data']);
			if($Model->saveAll($revision)){
				return true;
			}else{
				$this->log("Failed to Save revision:".print_r($revision,1),'error');
				return false;
			}
		}else{
			$this->log("Failed to find revision for {$Model->alias}:{$row_id} on {$date}.",'error');
			return false;
		}
	}

}
