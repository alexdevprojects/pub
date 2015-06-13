<?php
	$try = range(0,255);
	$bits = array(128,64,32,16,8,4,2,1);
	$mask = filter_input(INPUT_POST, 'mask', FILTER_VALIDATE_INT, array('default'=>0, 'min_range'=>0, 'max_range'=>255));// or 0;

	function binaryze($num){
		$result = array();

		$bits = array(128,64,32,16,8,4,2,1);

		foreach($bits as $bit){
			if($num >= $bit){
				$result[$bit] = true;
				$num = $num - $bit;
			}else{
				$result[$bit] = false;
			}
		}

		return $result;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Bit mask exercise</title>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
		<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<script>
			$(document).ready( function(){
				$('#menu').css('position', 'fixed');
				$( ".spinner" ).spinner({min: 1, max: 255});
			});
		</script>

		<style type="text/css">
			body {
				margin-top: 0px;
				border-top: 0px;
			}

			#menu{
				background-color: #DDDDDD;
			}

			.highlight {
				background-color: yellow;
			}

			.match {
				color: #CC6600;
				font-weight: bold;
			}

			.nomat {
				color: #7f7f7f;
			}

			.checkmark {
				color: #CC6600;
			}

			.checknone {
				color: #7f7f7f;
			}

			table {
				border: 1px black solid;
				padding-top: 0px;
			}

			td {
				padding: 0px;
				border: 1px black solid;
				width: 57px !important;
				text-align: center;
			}

			table thead tbody{
				border-spacing: 0;
				border-collapse: collapse;
				padding: 0px;
			}

			.ui-spinner{
				width: 45px;
				height: 1em;
			}

			.spinner {
				font-size: 0.7em !important;
				text-align: right;
				display: inline !important;
			}

			.footnote {
				color: #7f7f7f;
			}
		</style>

	</head>
	<body>
		<table id="main">
			<thead>
				<tr id="menu">
					<td># &</td>
					<td>
						<span style="white-space: nowrap;">
							<form method="POST">
								<input name="mask" type="text" class="spinner" onsubmit="this.form.submit()" style="float:right" <?= $mask ? 'value="'. $mask . '" />' : '/>' ?>

							</form>
						</span>
					</td>

<?php
	foreach($bits as $td){
?>
					<td>
						<?= $td ?>

					</td>

<?php
	}
?>
				</tr>
			</thead>
			<tbody>
				<tr id="menuph">
					<td colspan="10">
						GitHub: http://https://github.com/alexnowcom/tidbits
					</td>
				</tr>

<?php

		if ($mask === false || !in_array($mask, $try))
				die('no no no... only numbers between 0 and 255 accepted');

		foreach($try as $num){
			$my01 = binaryze($num);
?>
				<tr>
					<td<?= ($num == $mask) ? ' class="highlight"' : '' // Highlight the cell that matches the input ?>>
						<?= $num ?>

					</td>

<?php
			// Here's the magic:
			if ( $num & $mask ){
				// The bit mask evaluates to true
?>
					<td>
						<span class="checkmark" title="<?= $num . ' & ' . $mask ?>">&#x2713</span>
					</td>

<?php
			}else{
				// It's not a match
?>
					<td>
						<span class="checknone" title="<?= $num . ' & ' . $mask ?>">&#x2717</span>
						</td>

<?php
			}

			// Now to explain why break it down bit by bit:
			foreach($bits as $bitcode){
?>
					<td<?= ($mask & $bitcode) ? ' class="highlight"' : '';?>>
						<span class="<?= ($my01[$bitcode] == true && $mask & $bitcode) ? 'match' : 'nomat' ?>"><?= ($my01[$bitcode] == true) ? '1' : '0' ?></span>
					</td>

<?php
			}
?>
				</tr>

<?php
		}
?>
			</tbody>
		</table>
		<p class="footnote">
			The table above represents all the possible combinations of an 8 bit mask. <br />
			The format of the rows from left to right is [number], [bit], [result (number & bit)], breakdown of registers. <br />
			The default mask is 0 but if a value is selected in the form at the top the table will highlight the requested mask plus the registers that mask checks against.
		</p>
<!--
About bit masking: https://answers.yahoo.com/question/index?qid=20111115042952AAb5lyN
The Thinker:
Bitmasks are a very resource friendly means of storing settings. A bitmask makes use of the fact that binary numbers are made up of 1's and 0's, each digit in a binary number being equivalent to one bit. This makes binary numbers ideal for use as "switches" to enable or disable certain facilities.

Binary numbers are always read from right to left, and when used as bitmasks the same is true. The rightmost digit is always bit 0, so taking 1010 as an example bit 0 is off, bit 1 is on, bit 2 is off and bit 3 is on.

A good example of bitmask usage is when setting the log levels for a particular service. For example, if you enable all the logging options for the SMTP service you will end up with the SMTPLog variable set to a value of 4382. Converting 4382 to its binary equivalent gives 1000100011110, that is bits 1, 2, 3, 4, 8 and 12 are all set.

If you look at the Logs > Transaction Levels page you will see that there are 6 possible settings for SMTP logging that can be set via the GUI. Each one of these corresponds to one of the bits that are enabled (switched on) above.

Bitmasks are always converted to their decimal equivalent prior to entering them.
-->
	</body>
</html>
