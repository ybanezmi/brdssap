<?php
/**
 * SAP Controller
 *
 * This file is sap controller file. You can put all
 * sap methods here.
 *
 * Copyright (c) MKI, Inc. (http://mki.com.ph)
 *
 *
 * @copyright     Copyright (c) MKI, Inc. (http://mki.com.ph)
 * @package       app.Controller
 * @since         BRDS v 1.0.0
 */

/**
 * Sap Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class SapController extends AppController {

    public $components = array(
        'RequestHandler',
        'SapRfc'
    );

    public function beforeFilter() {
        parent::beforeFilter();
        Configure::load('sap');
    }

    public function index() {
        phpinfo();
    }

    public function import() {
        $this->layout= 'ajax';
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        if (!isset($this->request->data['RFC_FUNCTION'])) {
            $response['error'] = Configure::read('SAP.ERROR.200');
        } else if (!isset($this->request->data['PARAMS'])) {
            $response['error'] = Configure::read('SAP.ERROR.201');
        } else {
        	switch($this->request->data['RFC_FUNCTION']) {
				case Configure::read('SAP.ZBAPI_RECEIVING.FUNCTION_NAME'):
		            $response = $this->SapRfc->callBAPIReceiving($this->request->data['PARAMS']);
					break;
                case Configure::read('SAP.L_TO_CREATE_MOVE_SU.FUNCTION_NAME'):
                    $response = $this->SapRfc->callBAPICreateTO($this->request->data['PARAMS']);
                    break;
                case Configure::read('SAP.ZBAPI_POST_GR.FUNCTION_NAME'):
                    $response = $this->SapRfc->callBAPICloseReceiving($this->request->data['PARAMS']);
                    break;
				default:
					// Do nothing
					break;
        	}
        }

        $this->set(array(
            'response' => $response,
            '_serialize' => array('response')
        ));
    }
}
