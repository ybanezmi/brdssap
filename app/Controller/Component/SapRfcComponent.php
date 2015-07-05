<?php

class SapRfcComponent extends Component {

    public function open() {
        Configure::load('sap');
        $LOGIN = Configure::read('SapConfig');

        if (!function_exists(Configure::read('SAP.SAPRFC_OPEN'))) {
            $response['error'] = Configure::read('SAP.ERROR.100');
            return $response;
        }

        // Make a connection to the SAP server
        $rfc = saprfc_open($LOGIN);

		if (!$rfc) {
		    $response['error'] = str_replace('{0}', saprfc_error(), Configure::read('SAP.ERROR.101'));
		    return $response;
		}

        return $rfc;
    }

    public function import($rfcfunction, $params = null) {
        $rfc = $this->open();

        if (isset($rfc['error'])) {
            return $rfc;
        }

        // Locate the function and discover the interface
		$rfchandle = saprfc_function_discover($rfc, $rfcfunction);

		if (!$rfchandle) {
		    $rfcerror = str_replace('{0}', $rfcfunction, Configure::read('SAP.ERROR.102'));
            $response['error'] = str_replace('{1}', saprfc_error($rfc), $rfcerror);
            return $response;
		}

        // Import by rfc function
        switch($rfcfunction) {
            case Configure::read('SAP.ZBAPI_RECEIVING.FUNCTION_NAME'):
                saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.RS_INPUT'), $params);
                break;
            default:
                break;
        }

        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.PRINTER'), Configure::read('SAP.ZBAPI_RECEIVING.PRINTER_VAL'));

		saprfc_table_init($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.ET_PALLETS'));
		saprfc_table_init($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.ET_PALLETS_W_TO'));

        $rfc_rc = saprfc_call_and_receive($rfchandle);
        $sn = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.VBELN'));
		$ol = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.OBJECT_LOCKED'));
		$nc = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.NOT_COMPATIBLE'));
        $wc = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.WEIGHT_CAP_ERROR'));
		$vc = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.VOLUME_CAP_ERROR'));
		$oe = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.OTHER_ERROR'));

        $data_et_pallets = saprfc_table_rows($rfchandle, 'SAP.ZBAPI_RECEIVING.ET_PALLETS');
		$data_et_pallets_w_to = saprfc_table_rows($rfchandle, 'SAP.ZBAPI_RECEIVING.ET_PALLETS_W_TO');

        if ($rfc_rc != SAPRFC_OK) {
            if ($rfc_rc == SAPRFC_EXCEPTION) {
                $response['error'] = str_replace('{0}', saprfc_exception($rfchandle), Configure::read('SAP.ERROR.103'));
            } else {
                $response['error'] = str_replace('{0}', saprfc_error(), Configure::read('SAP.ERROR.104'));
            }
        } else {
            // SAP inbound no successfully retrieved
            if ($sn <> Configure::read('CONST.EMPTY_STRING')) {
                $response['sap_inbound_no'] = $sn;
                $response['pallet_no'] = $data_et_pallets;
            // SAP bapi function error occurred
            } else if ($ol <> Configure::read('CONST.EMPTY_STRING')) {
                $response['error'] = Configure::read('SAP.ERROR.300');
            } else if ($nc <> Configure::read('CONST.EMPTY_STRING')) {
                $response['error'] = Configure::read('SAP.ERROR.301');
            } else if ($wc <> Configure::read('CONST.EMPTY_STRING')) {
                $response['error'] = Configure::read('SAP.ERROR.302');
            } else if ($vc <> Configure::read('CONST.EMPTY_STRING')) {
                $response['error'] = Configure::read('SAP.ERROR.303');
            } else if ($oe <> Configure::read('CONST.EMPTY_STRING')) {
                $response['error'] = Configure::read('SAP.ERROR.304');
            } else {
                // Do nothing
            }
         }

        saprfc_exception($rfchandle);
        saprfc_function_debug_info($rfchandle, false);
        $this->close($rfchandle, $rfc);

        return $response;
	}

	public function close($rfchandle, $rfc) {
		saprfc_function_free($rfchandle);
		saprfc_close($rfc);
	}

}
