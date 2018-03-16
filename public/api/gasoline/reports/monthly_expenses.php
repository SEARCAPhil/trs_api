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
$total_amount = 0;
$total_liters = 0;

if($method == 'GET') {
	$from = isset($_GET['from']) ? htmlentities(htmlspecialchars($_GET['from'])) : '';	
	$to = isset($_GET['to']) ? htmlentities(htmlspecialchars($_GET['to'])) : '';
	$driver = isset($_GET['driver']) ? htmlentities(htmlspecialchars($_GET['driver'])) : null;	
	$vehicle = isset($_GET['vehicle']) ? htmlentities(htmlspecialchars($_GET['vehicle'])) : null;
	$station = isset($_GET['station']) ? htmlentities(htmlspecialchars($_GET['station'])) : null;	

	if ($vehicle=='all') $vehicle = null;
	if ($driver=='all') $driver = null;
	if ($station=='all') $station = null;

	if(!empty($from) && !empty($to)) {

		$result = ($Gas->lists_by_date($from, $to, $driver, $vehicle, $station));
	}

}

foreach ($result as $key => $value) {
	// total amount
	$total_amount += $value->amount;
	// overall liters
	$total_liters += $value->liters;

	$am = @number_format($total_amount,2,'.',',');


	$tr .= " 	<tr>
	 		<td>{$value->received_month}-{$value->received_day}-{$value->received_year}</td>
	 		<td>{$value->liters}</td>
	 		<td>PHP {$value->amount}</td>
	 		<td>{$value->tt_number}</td>
	 		<td>{$value->manufacturer} - <small style='color:rgb(100,100,100);'>{$value->plate_no}</small></td>
	 		<td>{$value->receipt}</td>
	 		<td>{$value->station}</td>
	 		<td>{$value->profile_name}</td>
	 	</tr>";
}

if(count($result) < 1) {
	echo '
		<center>
		<br/><br/>
			<h2 style="color:#F44336;">Unable to view PDF</h2>
			<p style="color:rgb(120,120,120);">The records you are trying to view doesn\'t contain any data. Make sure that you have enough<br/>
			 privilege to view this document.
			 </p>
		</center>
	';
	return 0;
}

$table= "
	<div align='center'>
<style>
	.ledger-table {
		cellspacing:0px;
		cellpadding:0px;
		border-collapse:collapse;
		font-size:14px;
		margin-left:20px;
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
<br/>
<br/>
<table class='ledger-table'> 
	<thead>
		<tr>
			<th width='70px;'>Date</th>
			<th width='60px;'>Liters</th>
			<th width='100px;'>Amount</th>
			<th width='60px;'>TR #</th>
			<th width='150px;'>Vehicle.</th>
			<th width='100px;'>Receipt #</th>
			<th width='200px;'>Gasoline Station</th>
			<th width='200px;'>Assigned Driver</th>
		</tr>
	</thead>
 <tbody>
 	{$tr}
 </tbody>
 </table>
 <p style='text-align:left;margin-left:25px;font-size:14px;'>
 	<b>Total : <u>PHP {$am}</u></b><br/>
 	<b>Total number of liters :</b> {$total_liters} L
 </p>
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
  	</p><br/>
  	<h3>SEARCA Gasoline Charges</h3>
  	<p style='float:left;margin-left:27px;text-align:left;'>FROM: {$from}<br/>TO : {$to}</p>
  </header>
  <!--<footer>Page /</footer>-->
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

//page number
$dompdf->getCanvas()->page_text(400, 570, "Page {PAGE_NUM} of {PAGE_COUNT}", '', 10, array(0,0,0));

// Output the generated PDF to Browser
$dompdf->stream("gasoline.pdf", array("Attachment" => false));

exit(0);

?>