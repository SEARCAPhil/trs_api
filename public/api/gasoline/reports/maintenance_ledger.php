<?php
header('Access-Control-Allow-Origin: *');
require_once(dirname(__FILE__).'/../../../../src/Automobile/Gasoline.php');
require_once(dirname(__FILE__).'/../../../../config/database.php');
include_once(dirname(__FILE__).'/../../../../src/Auth/Session.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/lib/html5lib/Parser.php');
include_once(dirname(__FILE__).'/../../../../vendor/dompdf/src/Autoloader.php');
Dompdf\Autoloader::register();

use Automobile\Gasoline;
use Auth\Session;
use Dompdf\Dompdf;


$Gas = new Gasoline($DB);
$Sess = new Session($DB);


$method = $_SERVER['REQUEST_METHOD'];
$result = []; 
$tr= '';

if($method == 'GET') {
	$from = isset($_GET['from']) ? htmlentities(htmlspecialchars($_GET['from'])) : '';	
	$to = isset($_GET['to']) ? htmlentities(htmlspecialchars($_GET['to'])) : '';	

	if(!empty($from) && !empty($to)) {
		$result = ($Gas->lists_by_date($from, $to));
	}
}

foreach ($result as $key => $value) {
	# code...


$tr .= " 	<tr>
 		<td>{$value->received_month}-{$value->received_day}-{$value->received_year}</td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>
 		<td></td>

 	</tr>";
}

$table= "
	<div align='center'>
<style>
	.ledger-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:14px;
	}
	.ledger-table td{
		border:1px solid #ccc;	
	}
	.ledger-table th, .ledger-table td{
		border:1px solid #ccc;
		padding:4px;
		text-align:center;
	}
</style>
<table class='ledger-table'> 
	<thead>
		<tr>
			<th width='70px;' rowspan='2'>Date</th>
			<th rowspan='2'>Particulars (Work Done)</th>
			<th rowspan='2'>Job <br/>Request No.</th>
			<th rowspan='2'>PR No./TT <br/>No.</th>
			<th rowspan='2'>OV No.</th>
			<th width='230px;' colspan='3'>Obligated</th>
			<th width='230px;' colspan='3'>EXPENDED</th>
			<th rowspan='2'>Variance <br/> in Amount</th>
			<th rowspan='2'>Remarks</th>
		</tr>
	 	<tr>
	 		<th width='10px'>Quantity</th>
	 		<th width='10px'>Unit Price</th>
	 		<th width='76.6px'>Amount</th>
	 		<th width='10px'>Quantity</th>
	 		<th width='10px'>Unit Price</th>
	 		<th width='76.6px'>Amount</th>
	 	</tr>
	</thead>
 <tbody>
 	{$tr}
 </tbody>
 </table>
";

$html = "<html>
<head>
  <style>
    @page { margin: 80px 25px; }
    header { position: fixed; top: -60px; left: 0px; right: 0px; height: 50px; text-align:center; }
    header p {
    	font-size:12px;
    }
    footer { position: fixed; bottom: -75px; left: 0px; right: 0px; height: 30px; text-align:center; font-size:12px; }
    /*p { page-break-after: always; }
    p:last-child { page-break-after: never; }*/
  </style>
</head>
<body>
  <header>
  	<p>SOUTHEAST ASIAN REGIONAL CENTER FOR GRADUATE STUDY 
	  	<br/>AND RESEARCH IN AGRICULTURE
	  	<br/>College, Laguna, 4031, Philippines
	  	<br/>
  	</p>
  	<h4>SEARCA Vehicle Travel Maintenance Ledger</h4>
  </header>
  <footer>Page /</footer>
  <main>
  	<br/><br/><br/>
    <p>{$table}</p>
  </main>
</body>
</html>";

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser

$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

exit(0);

?>