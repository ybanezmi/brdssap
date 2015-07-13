<?php

class SapRfcComponent extends Component {

    public function open() {
        Configure::load('sap');
        $LOGIN = Configure::read('SapConfig');

        if (!function_exists(Configure::read('SAP.SAPRFC_OPEN'))) {
            $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.100');
            return $response;
        }

        // Make a connection to the SAP server
        $rfc = saprfc_open($LOGIN);

		if (!$rfc) {
		    $response[Configure::read('SAP.ERROR')] = str_replace('{0}', saprfc_error(), Configure::read('SAP.ERROR.101'));
		    return $response;
		}

        return $rfc;
    }

    public function import($rfcfunction, $params = null) {
        $rfc = $this->open();

        if (isset($rfc[Configure::read('SAP.ERROR')])) {
            return $rfc;
        }

        // Locate the function and discover the interface
		$rfchandle = saprfc_function_discover($rfc, $rfcfunction);

		if (!$rfchandle) {
		    $rfcerror = str_replace('{0}', $rfcfunction, Configure::read('SAP.ERROR.102'));
            $response[Configure::read('SAP.ERROR')] = str_replace('{1}', saprfc_error($rfc), $rfcerror);
            return $response;
		}

        // Import by rfc function
        switch($rfcfunction) {
            case Configure::read('SAP.ZBAPI_RECEIVING.FUNCTION_NAME'):
                $response = $this->handleBAPIReceiving($rfchandle, $rfc_rc, $params, $response);
                break;
            case Configure::read('SAP.ZBAPI_CTO_PPTAG.FUNCTION_NAME'):
                $response = $this->handleBAPICT0PPTag($rfchandle, $rfc_rc, $params, $response);
                break;
            case Configure::read('SAP.ZBAPI_POST_GR.FUNCTION_NAME'):
                $response = $this->handleBAPIPostGR($rfchandle, $rfc_rc, $params, $response);
                break;
            default:
                break;
        }

        $this->close($rfchandle, $rfc);

        return $response;
	}

	public function close($rfchandle, $rfc) {
		saprfc_function_free($rfchandle);
		saprfc_close($rfc);
	}

    private function checkErrors($rfchandle, $rfc_rc, $response) {
        if ($rfc_rc != SAPRFC_OK) {
            if ($rfc_rc == SAPRFC_EXCEPTION) {
                $response[Configure::read('SAP.ERROR')] = str_replace('{0}', saprfc_exception($rfchandle), Configure::read('SAP.ERROR.103'));
            } else {
                $response[Configure::read('SAP.ERROR')] = str_replace('{0}', saprfc_error(), Configure::read('SAP.ERROR.104'));
            }
        } else {
            if (isset($response[Configure::read('SAP.EXPORT')]['TONumber']) && $response[Configure::read('SAP.EXPORT')]['TONumber'] === Configure::read('CONST.ZERO')) {
                $response[Configure::read('SAP.ERROR')] = str_replace('{0}', $response[Configure::read('SAP.EXPORT')]['TONumber'], Configure::read('SAP.ERROR.305'));
            } else if (isset($response[Configure::read('SAP.EXPORT')][Configure::read('SAP.ZBAPI_POST_GR.POSTED_IND')]) && Configure::read('SAP.ZBAPI_POST_GR.POSTED_IND') === Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = str_replace('{0}', $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_POST_GR.VBELN')], Configure::read('SAP.ERROR.305'));
            } else if ($response[Configure::read('SAP.EXPORT')]['ol'] <> Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.300');
            } else if ($response[Configure::read('SAP.EXPORT')]['nc'] <> Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.301');
            } else if ($response[Configure::read('SAP.EXPORT')]['wc'] <> Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.302');
            } else if ($response[Configure::read('SAP.EXPORT')]['vc'] <> Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.303');
            } else if ($response[Configure::read('SAP.EXPORT')]['oe'] <> Configure::read('CONST.EMPTY_STRING')) {
                $response[Configure::read('SAP.ERROR')] = Configure::read('SAP.ERROR.304');
            } else {
                // Do nothing
            }
         }
        return $response;
    }

    private function handleBAPIReceiving($rfchandle, $rfc_rc, $params, $response) {
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.RS_INPUT'), $params['import']);

        saprfc_import($rfchandle, Configure::read('SAP.PRINTER'), Configure::read('SAP.PRINTER_VAL'));

        saprfc_table_init($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.ET_PALLETS'));
        saprfc_table_init($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.ET_PALLETS_W_TO'));

        $rfc_rc = saprfc_call_and_receive($rfchandle);
        $response[Configure::read('SAP.EXPORT')]['sn'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.VBELN'));
        $response[Configure::read('SAP.EXPORT')]['ol'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.OBJECT_LOCKED'));
        $response[Configure::read('SAP.EXPORT')]['nc'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.NOT_COMPATIBLE'));
        $response[Configure::read('SAP.EXPORT')]['wc'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.WEIGHT_CAP_ERROR'));
        $response[Configure::read('SAP.EXPORT')]['vc'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.VOLUME_CAP_ERROR'));
        $response[Configure::read('SAP.EXPORT')]['oe'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_RECEIVING.OTHER_ERROR'));

        $response['table_rows']['data_et_pallets'] = saprfc_table_rows($rfchandle, 'SAP.ZBAPI_RECEIVING.ET_PALLETS');
        $response['table_rows']['data_et_pallets_w_to'] = saprfc_table_rows($rfchandle, 'SAP.ZBAPI_RECEIVING.ET_PALLETS_W_TO');

        $response = $this->checkErrors($rfchandle, $rfc_rc, $response);

        return $response;
    }

    private function handleBAPICT0PPTag($rfchandle, $rfc_rc, $params, $response) {
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.I_LENUM'), $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_CTO_PPTAG.I_LENUM')]);
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.I_BWLVS'), $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_CTO_PPTAG.I_BWLVS')]);
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.I_COMMIT_WORK'), $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_CTO_PPTAG.I_COMMIT_WORK')]);

        saprfc_import($rfchandle, Configure::read('SAP.PRINTER'), Configure::read('SAP.PRINTER_VAL'));

        saprfc_table_init($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.T_LTAP_MOVE_SU'));

        saprfc_table_appned($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.T_LTAP_MOVE_SU'), $params[Configure::read('SAP.TABLE')]);

        $rfc_rc = saprfc_call_and_receive($rfchandle);

        $response[Configure::read('SAP.EXPORT')]['TONumber'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.E_TANUM'));
        $response[Configure::read('SAP.EXPORT')]['destinationSType'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.E_NLTYP'));
        $response[Configure::read('SAP.EXPORT')]['destinationSSect'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.E_NLBER'));
        $response[Configure::read('SAP.EXPORT')]['destinationSBin'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.E_NLPLA'));
        $response[Configure::read('SAP.EXPORT')]['destinationSPost'] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_CTO_PPTAG.E_NPPOS'));

        $response = $this->checkErrors($rfchandle, $rfc_rc, $response);

        return $response;
    }

    private function handleBAPIPostGR($rfchandle, $rfc_rc, $params, $response) {
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_POST_GR.VBELN'), $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_POST_GR.VBELN')]);
        saprfc_import($rfchandle, Configure::read('SAP.ZBAPI_POST_GR.WDATU'), $params[Configure::read('SAP.IMPORT')][Configure::read('SAP.ZBAPI_POST_GR.WDATU')]);

        saprfc_import($rfchandle, Configure::read('SAP.PRINTER'), Configure::read('SAP.PRINTER_VAL'));

        $rfc_rc = saprfc_call_and_receive($rfchandle);

        $response[Configure::read('SAP.EXPORT')][Configure::read('SAP.ZBAPI_POST_GR.POSTED_IND')] = saprfc_export($rfchandle, Configure::read('SAP.ZBAPI_POST_GR.POSTED_IND'));

        $response = $this->checkErrors($rfchandle, $rfc_rc, $response);

        return $response;
    }

}
