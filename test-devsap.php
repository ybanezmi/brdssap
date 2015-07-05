<?php
// This script is a simple example of
// how to connect to R3 using hardcoded credentials

$connection_options = array (
	'ASHOST'	=>'192.168.1.14',
	'SYSNR'		=>'00',
	'CLIENT'	=>'400',
    'USER'		=>'dtcustodian', 
	'PASSWD'	=>'bigblue',
	'LANG'		=>'EN'
);

$rfc = saprfc_open($connection_options);

if (! $rfc) {
	echo 'An error occured: <pre>';
	print_r(saprfc_error());
	exit;
}

// sap info
//$info = saprfc_attributes($rfc);
//echo '<pre>';
//print_r($info);
//exit;

$fce = saprfc_function_discover($rfc, 'ZBAPI_RECEIVING');
if(! $fce) {
	echo "Discovering interface of function module ZBAPI_RECEIVING failed";
	exit;
}
saprfc_import($fce,"RS_INPUT",array(
		"ZEX_VBELN"	=>	"0522151903",//always null in first submit (transaction number)
		"KUNNR" => "ANGELITO", //ship to party
		"MATNR" => "ANGELITO0002", // Material
		"LFIMG" => "5.000", // Deliv Qty
		"CHARG" => "1427279260", // batch #
		"WERKS" => "BBL2", //location
		"LFART" => "ZEL", //fixed value
		"LGORT" => "B201", // storage
		"XABLN" => "ZXY12345", 
		"WADAT" => "04/25/2015", //current date
		"WDATU" => "04/25/2015", // submitted date
		"HSDAT" => "04/03/2014", // manuf. date
		"VFDAT" => "04/05/2015", // expiry date
		"CRATES_IND" => "", // null (already exist in BAPI)
		"EXIDV" => "", //handling unit (range)
		"EXIDV2" => "6200000000",
		"VHILM" => "", //Packaging material (pack icon) select allowed material >> (hu) empty or same as vhilm2.
		"VHILM2" => "36", // packing Materials (always has an entry)
		"REMARKS" => "TEST", // Extras > headers > text
		"LAST_ITEM_IND" => " ",
));

saprfc_import($fce,'PRINTER','ZWI6');
saprfc_table_init($fce,"ET_PALLETS");
saprfc_table_init($fce,"ET_PALLETS_W_TO");

echo "<pre>";
print_r(saprfc_server_import($rfchandle, 'RS_INPUT'));
print_r(saprfc_server_import($rfchandle, 'PRINTER'));
echo "</pre>";

$rfccar = saprfc_call_and_receive($fce);

$sn = saprfc_export($fce, 'VBELN');
$ol = saprfc_export($fce, 'OBJECT_LOCKED');
$nc = saprfc_export($fce, 'NOT_COMPATIBLE');
$vc = saprfc_export($fce, 'VOLUME_CAP_ERROR');
$oe = saprfc_export($fce, 'OTHER_ERROR'); 

if($rfccar == SAPRFC_OK){
	echo "<h2>Pulled Data</h2>";
	echo "SAP #:". $sn ."<br />";
	echo "Object Locked:". $ol ."<br />";
	echo "Not Compatible:". $nc ."<br />";
	echo "Volume Cap Error:". $vc ."<br />";
	echo "Other Error:". $oe ."<br />";
} 

$data_et_pallets = saprfc_table_rows ($fce,"ET_PALLETS");
$data_et_pallets_w_to = saprfc_table_rows ($fce,"ET_PALLETS_W_TO");

saprfc_function_free($fce);
saprfc_close($rfc);

?>