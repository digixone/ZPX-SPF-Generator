<?php
/*
		SPF Generator, created by Roy Hochstenbach
		@version 0.3 beta
		
		Please read the disclaimer (disclaimer.txt).
		If it's not present, please read the license.

		Do not remove this text
*/
session_start();
if (!isset($_SESSION['zpuid'])) {
    die("<h1>Unauthorized request!</h1> Request not accessible outside!");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<META NAME="DESCRIPTION" CONTENT="SPF Record Generator">
	<META NAME="KEYWORDS" CONTENT="SPF,Sender Policy Framework,DNS,email">
	<title>SPF Record Generator</title>
<link rel='stylesheet' href='css/styles.css'>
<script language=JavaScript>
<!--
var message="We do not allow function!";
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {if 
(document.layers||(document.getElementById&&!document.all)) {
if (e.which==2||e.which==3) {(message);return false;}}}
if (document.layers) 
{document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;}
else{document.onmouseup=clickNS;document.oncontextmenu=clickIE;}
document.oncontextmenu=new Function("return false")
// --> 
</script>
</head>
<body>
<center>
<div id='container'>
<!--<h3>SPF Record Generator</h3>-->
<p>Please enter e-mail sources which are allowed to send e-mail on your domain's behalf. <br>To use your DNS zone as the domain name, just enter an '@' symbol.
<form method="post" action="">
	<table class='spftable'>
	<tr>
		<td><strong>Domain</strong></td><td><input class='textbox' type='text' name='domainname' value="" placeholder="@"></td><td><strong> The domain to which this policy belongs.</strong></td>
	</tr>
	</table>
	<br>
	<p id='info'>You can give multiple values by separating them with a comma (,).<br>
	If the senders' IP address can be resolved by any of this domains' A records or the mail server is specified in an MX record, <br>please tick the 'any' checkbox for that record type.</p>
	<table class='spftable'>
		<tr>
			<th>Policy Item</th>
			<th class='centered'>Value(s)</th>
			<th class='centered'>Any</th>
			<th>Description</th>
		</tr>
		<tr>
			<td>A records</td>
			<td>
				<input class='textbox' type='text' name='arecords' placeholder="your static ip address">
			</td>
			<td class='centered'>
				<input type='checkbox' name='arecords_any' value='1'>
			</td>
			<td>The IP addresses which are pointed to by this/these A record(s).</td>
		</tr>
		<tr>
			<td>MX records</td>
			<td>
				<input class='textbox' type='text' name='mxrecords' placeholder="mail.yourdomain.com">
			</td>
			<td class='centered'>
				<input type='checkbox' name='mxrecords_any' value='1'>
			</td>
			<td>The mail servers which are present in your domain's DNS zone</td>
		</tr>
		<tr>
			<td>IPv4 Addresses</td>
			<td>
				<input class='textbox' type='text' name='ipv4records' placeholder="your ipv4 address">
			</td>
			<td></td>
			<td>Allow from these IPv4 addresses</td>
		</tr>
		<tr>
			<td>IPv6 Addresses</td>
			<td>
				<input class='textbox' type='text' name='ipv6records' placeholder="your ipv6 address">
			</td>
			<td></td>
			<td>Allow from these IPv6 addresses</td>
		</tr>
		<tr>
			<td>Other domains</td>
			<td>
				<input class='textbox' type='text' name='includespf' placeholder="">
			</td>
			<td></td>
			<td>Specify domains which SPF records you would like to add</td>
		</tr>
		<tr>
			<td colspan='4'>&nbsp;</td>
		</tr>
		<tr>
			<td>Other sources</td>
			<td>
				<select class='selectbox' name='othersources'>
					<option value='allow'>Allow</option>
					<option value='warn'>Mark Suspicious</option>
					<option value='deny'>Deny</option>
				</select>
			</td>
			<td></td>
			<td>What to do when an e-mail from your domain comes from a different source.</td>
		</tr>
		<tr>
			<td colspan='4'>
				<center>
					<input type='submit' name='generate' value='Generate' id='generatebutton'>
				</center>
			</td>
		</tr>
	</table>
</form>
<!--<br><pre>Highlight and use |[CTRL + C (Windows)]|[Command-C (MAC)]| to copy generated SPF record below:</pre>-->
<?php
// Turn off error reporting
error_reporting(0);

// Check if the generate button has been pressed and the domain name has been entered.
if(isset($_POST['generate']) AND $_POST['domainname'] != NULL) {

// Initialize funtions
require_once("functions/function.extractvalues.php");

// Put the field values into variables. Remove whitespace around the domain name
$domain_name = trim($_POST['domainname']);
$a_records = $_POST['arecords'];
$mx_records = $_POST['mxrecords'];
$ipv4_records = $_POST['ipv4records'];
$ipv6_records = $_POST['ipv6records'];
$includespf = $_POST['includespf'];
$othersources = $_POST['othersources'];

// Create an array for the values of each field
$a_records_array = extractValues($a_records);
$mx_records_array = extractValues($mx_records);
$ipv4_records_array = extractValues($ipv4_records);
$ipv6_records_array = extractValues($ipv6_records);
$includespf_array = extractValues($includespf);

// The 1st part of the SPF record
$record_generated = $domain_name."\tIN TXT or SPF\t \"v=spf1 ";

// If this stays '0', this means no fields are entered
$some_match = 0;

// Variable to store any error messages
$error_messages = "";

		// The part for A Records. 
		
		// Check if the 'any' checkbox has been ticked
		if($_POST['arecords_any'] == 1) {
				// The checkbox has been ticked, so set this to 1
						$some_match = 1;
						$record_generated .= "a ";
						
				// if this is not the case, check if the field has been filled
			} elseif($a_records_array[0] != NULL AND count($a_records_array) > 0) {
				// Some field has been entered, so set this to 1
				$some_match = 1;
				foreach($a_records_array as $a_records_array_item) {
						$record_generated .= "a:".$a_records_array_item." ";
				}
				

		}
		
		// The part for MX Records. 
		
		if($_POST['mxrecords_any'] == 1) {
				
						$some_match = 1;
						$record_generated .= "mx ";
			} elseif($mx_records_array[0] != NULL AND count($mx_records_array) > 0) {
				
				$some_match = 1;
				foreach($mx_records_array as $mx_records_array_item) {
						$record_generated .= "mx:".$mx_records_array_item." ";
				}
				

		}
		
		// The part for IPv4 addresses. 
		if($ipv4_records_array[0] != NULL AND count($ipv4_records_array) > 0) {
				$some_match = 1;
				foreach($ipv4_records_array as $ipv4_records_array_item) {
						$record_generated .= "ip4:".$ipv4_records_array_item." ";
				}
		}
		
		// The part for IPv6 addresses. 
		if($ipv6_records_array[0] != NULL AND count($ipv6_records_array) > 0) {
				$some_match = 1;
				foreach($ipv6_records_array as $ipv6_records_array_item) {
						$record_generated .= "ip6:".$ipv6_records_array_item." ";
				}
		}
		
		// The INCLUDE part
		if($includespf_array[0] != NULL AND count($includespf_array) > 0) {
				$some_match = 1;
				foreach($includespf_array as $includespf_array_item) {
						$record_generated .= "include:".$includespf_array_item." ";
				}
		}
		
		// Check the field for other sources
		switch($othersources) {
				case "allow":
					$record_generated .= "+all";
				break;
				case "warn":
					$record_generated .= "~all";
					// If 'all' is suspicous and no other entries have been entered, give a warning
					if($some_match == 0) {
							$error_messages.= "You have not entered any other record and chose do mark all other sources suspicious. This will mark all e-mail from this domain as suspicious / spam.<br>";
					}
				break;
				case "deny":
					$record_generated .= "-all";
					// If 'all' is denied and no other entries have been entered, give a warning
					if($some_match == 0) {
							$error_messages.= "You have not entered any other record and chose to deny all other sources. This will basically deny all e-mail from this domain.<br>";
					}
				break;
		}
		// End of the SPF record
		$record_generated .= "\"";
		
		// The part that shows the SPF record, and eventually error messages
		echo "<br/></br><b>Your SRV Record:</b><br>";
		echo "<br><font style='color: green;'><font style='color: red;'><strong>Right Click Disabled:</strong></font> Highlight and use [CTRL + C (Windows)]|[Command-C (MAC)] to copy generated SPF record below:</font>";
		echo "<pre id='srvrecord_generated'>".$record_generated."</pre>";
		echo "<p><font style='color: red; font-weight: bold;'>".$error_messages."</font></p>";

}
// If the generate button is clicked and the domain name is not entered, show an error message
if(isset($_POST['generate']) AND $_POST['domainname'] == NULL) {
echo "<p><font style='color: red; font-weight: bold;'>Please enter a domain name or @ sign</font></p>";
}
?>
</div>
</center>

<?php //include_once("footer.php"); ?>
</body>
</html>
