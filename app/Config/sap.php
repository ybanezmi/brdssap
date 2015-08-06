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
        'PASSWD'    => 'bbl821',      // password
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
    )
);

// SAP ZBAPI_RECEIVING constants
Configure::write('SAP.ZBAPI_RECEIVING',
    array(
        'FUNCTION_CODE'     => 'Z001',
        'FUNCTION_NAME'     => 'ZBAPI_RECEIVING',
        'RS_INPUT'          => 'RS_INPUT',
        'PRINTER'           => 'PRINTER',
        'PRINTER_VAL'       => 'ZWI1',
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

// SAP L_TO_CREATE_MOVE_SU constants
Configure::write('SAP.L_TO_CREATE_MOVE_SU',
    array(
        'FUNCTION_CODE'     => 'Z002',
        'FUNCTION_NAME'     => 'L_TO_CREATE_MOVE_SU',
        'I_LENUM'           => 'I_LENUM',               // Pallet Number
        'I_BWLVS'           => 'I_BWLVS',               // Movement Type 501 for Putaway
        'I_BWLVS_VAL'       => '501',                   // Movement Type 501 for Putaway (value)
        'I_COMMIT_WORK'     => 'I_COMMIT_WORK',         // Commit to Work X - optional
        'I_BNAME'           => 'I_BNAME',               // Creator name - optional
        'I_BNAME_VAL'       => 'DTCUSTODIAN',           // Creator name - optional (value)
        'E_TANUM'           => 'E_TANUM',               // Transfer Order
        'E_NLTYP'           => 'E_NLTYP',               // Destination Storage Type
        'E_NLBER'           => 'E_NLBER',               // Destination Storage Section
        'E_NLPLA'           => 'E_NLPLA',               // Destination Storage Bin
        'E_NPPOS'           => 'E_NPPOS',               // Destination Storage Position
        'TRANSFER_ORDER'    => 'transfer_order',        // Transfer Order
        'STORAGE_TYPE'      => 'storage_type',          // Destination Storage Type
        'STORAGE_SECTION'   => 'storage_section',       // Destination Storage Section
        'STORAGE_BIN'       => 'storage_bin',           // Destination Storage Bin
        'STORAGE_POSITION'  => 'storage_position',      // Destination Storage Position
    )
);

// SAP ZBAPI_RECEIVING constants
Configure::write('SAP.ZBAPI_POST_GR',
    array(
        'FUNCTION_CODE'     => 'Z003',
        'FUNCTION_NAME'     => 'ZBAPI_POST_GR',
        'VBELN'             => 'VBELN',                 // Inbound Number
        'WDATU'             => 'WDATU',                 // Date
        'PRINTER'           => 'PRINTER',
        'PRINTER_VAL'       => 'ZWI1',
        'POSTED_IND'        => 'POSTED_IND',
    )
);

// SAP error messages
Configure::write('SAP.ERROR',
    array(
        '100' => 'SAPRFC PHP extension not installed in the server.',
        '101' => 'Failed to connect to the SAP server. {0}',
        '102' => 'Failed to discover the SAP function {0}. {1}',
        '103' => 'SAP exception raised: {0}',
        '104' => 'SAP error occurred: {0}',
        '200' => 'SAP {RFC_FUNCTION} form data is required.',
        '201' => 'SAP {PARAMS} form data is required.',
        '300' => '[SAP] Unknown error had occurred. Please check material configuration.',
        '301' => '[SAP] Compatibility error. Please check material configuration.',
        '302' => '[SAP] Weight over-capacity error. Please check material configuration.',
        '303' => '[SAP] Volume over-capacity error. Please check material configuration.',
        '304' => '[SAP] Inbound number is currently being processed by another user.',
        '305' => '[SAP] Error in putaway configuration, please check material(s).',
        '306' => '[SAP] Inbound No. {0} not successfully closed. Please check for errors.',
    )
);
