<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    public function saveAssociated($data = null, $options = array()) {
        foreach ($data as $alias => $modelData) {
            if (!empty($this->hasAndBelongsToMany[$alias])) {

                $habtm = array();
                $emId = false;
                $Model = ClassRegistry::init($this->hasAndBelongsToMany[$alias]['className']);

                if (isset($modelData[$options[$alias]['unique']])) {
                    $searched = $modelData[$options[$alias]['unique']];
                    $unique = ucfirst($options[$alias]['unique']);
                    $fb = 'findBy' . $unique;
                }

                $em = $Model->$fb($searched);
                if (!empty($em)) {
                    $em = $em[$alias];
                    $emId = $em['id'];
                }

                foreach ($modelData as $modelDatum) {

                    if (empty($modelDatum['id'])) {

                        $Model->create();
                    }

                    
                    if ($emId) {
                        $habtm[] = $emId;
                    } else {
                        $Model->save($modelData);
                        $habtm[] = empty($modelDatum['id']) ? $Model->getInsertID() : $modelDatum['id'];
                    }
                }

                $data[$alias] = array($alias => $habtm);
            }
        }
        return parent::saveAssociated($data, $options);
    }

}
