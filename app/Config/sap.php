<?php
/**
 *
 *
 * Copyright (c) MKI, Inc. (http://mki.com.ph)
 *
 *
 * @copyright     Copyright (c) MKI, Inc. (http://mki.com.ph)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         BRDS v 1.0.0
 */

/**
 * This is sap configuration file.
 *
 * Use it to configure SAP of BRDS.
 *
 * SAP configuration class.
 *
 *
 */
$config = array(
    'SapConfig' => array(
        'ASHOST'    => '192.168.1.13', // application server host name
        'SAPSYS'	=> 'DEV',
        'SYSNR'	    => '00',           // system number
        'CLIENT'	=> '110',          // client
        'USER'	    => 'dtcustodian',  // user
        'PASSWD'    => 'bbl821',       // password
        'CODEPAGE'  => '1100',         // codepage
        'LANG'		=> 'EN',           // language
        'TRACE'     => 'X',
    )
);

/**
 * Constants
 */
// common
Configure::write('SAP',
    array(
        'SAPRFC_OPEN'      => 'saprfc_open',
        'PRINTER'          => 'PRINTER',
        'PRINTER_VAL'      => 'ZWI1',
        'IMPORT'           => 'import',
        'EXPORT'           => 'export',
        'TABLE'            => 'table',
        'ERROR'            => 'error',
    )
);

// SAP ZBAPI_RECEIVING constants for Create Receiving
Configure::write('SAP.ZBAPI_RECEIVING',
    array(
        'FUNCTION_CODE'     => 'Z001',
        'FUNCTION_NAME'     => 'ZBAPI_RECEIVING',
        'RS_INPUT'          => 'RS_INPUT',
        'ET_PALLETS'        => 'ET_PALLETS',
        'ET_PALLETS_W_TO'   => 'ET_PALLETS_W_TO',
        'VBELN'             => 'VBELN',
        'OBJECT_LOCKED'     => 'OBJECT_LOCKED',
        'NOT_COMPATIBLE'    => 'NOT_COMPATIBLE',
        'WEIGHT_CAP_ERROR'  => 'WEIGHT_CAP_ERROR',
        'VOLUME_CAP_ERROR'  => 'VOLUME_CAP_ERROR',
        'OTHER_ERROR'       => 'OTHER_ERROR',
    )
);

// SAP ZBAPI_CTO_PPTAG constants for Create T.O.
Configure::write('SAP.ZBAPI_CTO_PPTAG',
    array(
        'FUNCTION_CODE'     => 'Z002',
        'FUNCTION_NAME'     => 'ZBAPI_CTO_PPTAG',
        'I_LENUM'           => 'I_LENUM',
        'I_BWLVS'           => 'I_BWLVS',
        'I_BWLVS_VAL'       => '501',
        'I_COMMIT_WORK'     => 'X',
        'T_LTAP_MOVE_SU'    => 'T_LTAP_MOVE_SU',
        'MATNR'             => 'MATNR',
        'WERKS'             => 'WERKS',
        'LGORT'             => 'LGORT',
        'CHARG'             => 'CHARG',
        'WDATU'             => 'WDATU',
        'VFDAT'             => 'VFDAT',
        'E_TANUM'           => 'E_TANUM',
        'E_NLTYP'           => 'E_NLTYP',
        'E_NLBER'           => 'E_NLBER',
        'E_NLPLA'           => 'E_NLPLA',
        'E_NPPOS'           => 'E_NPPOS',
    )
);

// SAP ZBAPI_POST_GR constants for Close Receiving
Configure::write('SAP.ZBAPI_POST_GR',
    array(
        'FUNCTION_CODE'     => 'Z003',
        'FUNCTION_NAME'     => 'ZBAPI_POST_GR',
        'VBELN'             => 'VBELN',
        'WDATU'             => 'WDATU',
        'POSTED_IND'        => 'POSTED_IND',
    )
);

// SAP ZBAPI_RECEIVING error messages
Configure::write('SAP.ERROR',
    array(
        '100' => 'SAPRFC PHP extension not installed in the server.',
        '101' => 'Failed to connect to the SAP server. {0}',
        '102' => 'Failed to discover the SAP function {0}. {1}',
        '103' => 'SAP exception raised: {0}',
        '104' => 'SAP error occurred: {0}',
        '105' => 'Error calling SAP-BAPI: {0}',
        '200' => 'SAP {RFC_FUNCTION} form data is required.',
        '201' => 'SAP {PARAMS} form data is required.',
        '300' => '[SAP] Unknown error had occurred. Please check material configuration.',
        '301' => '[SAP] Compatibility error. Please check material configuration.',
        '302' => '[SAP] Weight over-capacity error. Please check material configuration.',
        '303' => '[SAP] Volume over-capacity error. Please check material configuration.',
        '304' => '[SAP] Inbound number is currently being processed by another user.',
        '305' => '[SAP] Error in putaway configuration, please check material(s) {0}',
        '306' => '[SAP] Inbound #{0} not successfully closed, please check for errors.',
    )
);
