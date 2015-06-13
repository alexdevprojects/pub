<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Alexnow.com :: Prime numbers tool</title>
</head>

<body>

<?php

####### DEFAULTS: #######

$start = 1;
$endnum = 10000;
$max_exec_time = 120;
$numformat = FALSE;
$layout = 'v';
$maxOutsideNumber = 1000000; // set to higher than usual due to outside IP validation broken
$maxoutside_f = number_format($maxOutsideNumber);



$form1 = <<<EOF
<form name="form1" action="$_SERVER[PHP_SELF]" method="post">
Get all primes between <br />
<input type="text" name="start" />
and <br>
<input type="text" name="end" /> (Maximum $maxoutside_f) <br />
Layout: 
<input type="radio" name="layout" value="v" checked /> Vertical | <input type="radio" name="layout" value="h" /> Horizontal | <input type="radio" name="layout" value="t" /> Textarea<br />
<input type="checkbox" name="return_nonprimes" /> Return non primes also <br />
<input type="checkbox" name="numformat" /> Format numbers (x,xxx) <br />
<input type="submit" name="submit" value="Submit" />
EOF;

if(isset($_GET['override'])){
$form1 .= '<input type="hidden" name="outside_override" value="TRUE" />' . "\n";
}

$form1 .= '</form>';

$info1 = <<<EOF
<pre>
Function:
Finds all prime numbers within a range of 2 given values

Method logic:
An outter FOR loop iterates through all whole numbers, one by one, from the minimun input to the maximun. 

An inner loop performs a division with the current key as dividend by all numbers from 2 to key minus 1 as divisors.
Each result is tested to see if it's an integer, if it is, the number being procesed
is flagged as a non-prime an stored in an array variable. 
The inner loop is also broken since the number has already been proved to be non-prime
(ie number 6 is divided by 2, never makes it to be divided by 3)

When the outter loop finishes a temporary array is generated with the range of the 
two original values and then it's intersected with the array containing the non-prime numbers...
The result is an array of pure primes that gets output.
 
Changes:

7/29/2013

Hello PHP! it's been a long time. Anyway, debugged this thing and made it work, some bad var names and stuff.

6/27/05

Revisited this old script and made a few improvements:

Presentation:
Added option for vertical or horizontal layout.
Added option to format the numbers returned by the script.
Cleaned up markup (very basic to begin with) now 4.01 Transitional Valid (W3C DTD).

Performance:
Added the option to return a list of non-prime numbers and the first divisor that returns an integer.
Better benchmarking algorithm.

The new P4 server ran the 1-1000 task (78,022 ops) in 0.1383790969849 seconds when tested. 
(even whith the new non-prime/div array)

Security:
Added some input verification (input non empty, is an integer, min < max).
Limited max number argument to $maxoutside_f when script is called from an IP outside my network.
   Did this to launch live, since a very large calculation would take too many valuable resources from my server.
   Should you have the need for a calculation outside that range simply email me, tell me what you need the info for
   and I'll run this script for you.

4/30/02
Had the great idea for the inner loop to start from 2 and continue the divisions
through x - 1.
(it used to be x-1 and substracting by 1 through number 2)

The result was great, in the benchmark for finding primes 1 - 1000:
Old script: Process time: 11.336248993874 seconds (335052 operations)
New script: Process time: 2.8276569843292 seconds (78022 operations)

(ie 700 had to be divided by 699, 698, 697... untill 350 where it returned 2,
now it is divided by 2 first returnin non-prime right away)

Do not ask why I originally did it the other way.

Change 2:
I made the script override PHP's default maximun execution time of 30 seconds.
The new value is 2 minutes, just to prevent the script to "time out" during an
unnecesary calculation of something like primes 1 to 1,000,000. 
</pre>
EOF;

#                                     Copied this to further down as to always show the form regardles of SUBMIT 
# if(!isset($_POST["submit"])){

echo($form1);
#echo("\n<hr />\n");
# echo($info1);                       added this to the end of document regarless of SUBMIT


if(!isset($_POST["submit"])){
  echo("\n");                         //just throw something out there
}else{ //form submitted

####### VALIDATION #######

if(isset($_POST['layout'])){
$layout = $_POST['layout'];
}

if(isset($_POST['numformat'])){
$numformat = TRUE;
}


if(isset($_POST['start']) && is_numeric($_POST['start']) && $_POST['start'] > 0){
$start = $_POST['start'];
settype($start, "int");
}else{
  $start = 1;
}

if(isset($_POST['end']) && is_numeric($_POST['end']) && $_POST['end'] > $_POST['start']){
$end = $_POST['end'];
settype($end, "int");

##                                                        TRY TO FIX THIS FOR SECURITY
#	if($REMOTE_ADDR != "127.0.0.1" && !ereg("^192.168+",$REMOTE_ADDR) && !isset($_POST['outside_override']) && $end > $max_outside){
#	$end = $max_outside;

##                                                        USING THIS INSTEAD FOR NOW:
	if($end > $maxOutsideNumber){
	$end = $maxOutsideNumber;
	}

}else{
  $end = 100;
}


set_time_limit($max_exec_time);

####### BENCHMARK #######

$start_micro = explode(" " , microtime());
$start_time = $start_micro[1] + $start_micro[0];
$operations = 0;



####### CALCULATIONS #######

for($x=$start; $x<=$end; $x++){
   for($y=2;$y < $x; $y++){
   $current_div = $x / $y;
   $operations++;
   #print("$x divided by $y = $current_div<br />\n");

   if(is_int(($current_div))){ // $x is not prime
   $not_prime_array[] = $x;
   $not_prime_divby[] = $y;
   break 1;
   }
   
   }
}



####### BENCHMARK #######
$end_micro = explode(" " , microtime());
$end_time = $end_micro[1] + $end_micro[0];


$original_values = range($start,$end);

if(isset($not_prime_array)){
$primearray = array_diff($original_values,$not_prime_array);
}


$process_time = $end_time - $start_time;

if(isset($primearray)){

print("These primes found: ($start ... $end) | Process time: " . number_format($process_time , 13 , '.' , ',') . ' seconds (' . number_format($operations) . " operations).<br/ >\n");


switch($layout){
  case 'h':
    foreach($primearray as $prime){
      print($numformat)?number_format($prime):$prime;
      print " \n";
    }
  break;

  case 'v':
    foreach($primearray as $prime){
      print($numformat)?number_format($prime):$prime;
      print " <br />\n";
    }
  break;
  
  default:
      print "<form>\n<textarea rows=\"20\" cols=\"10\" wrap=\"hard\">\n";
    foreach($primearray as $prime){
      print($numformat)?number_format($prime):$prime;
      print "\n";
    }
      print "\n</textarea></form>\n";
  break;  

}


if(isset($_POST["return_nonprimes"])){
print "\n<hr />\nNon primes and first divisor that returns an integer: <br />\n";

for($x=0; $x < count($not_prime_array); $x++){
 print $not_prime_array[$x] . ' div by ' . $not_prime_divby[$x] . "<br />\n";
}

#foreach($not_prime_array as $key => $val){ // this needed a change inside the loop creating the arrays
#print $key . ' div by ' . $value . "<br />\n";
#}

}


}else{ // no $primearray
print("No primes found ($start ... $end)");
}


} // submit

print "\n<hr />\n".$info1;
?>
</body>
</html>