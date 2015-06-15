<?php
####### DEFAULTS: #######
$default = array('start' => 1, 'finish' => 1000);
$limit = 100000;
set_time_limit(120);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Alexnow.com :: Prime number finder tool</title>
  </head>

  <body>
    <form name="form1" method="post">

      <fieldset>
        <legend>Get all prime numbers between</legend>
        <input type="number" name="start" value="<?= $default['start'] ?>" min="1" max="<?= $limit - 1 ?>" /> and
        <input type="number" name="finish" value="<?= $default['finish'] ?>" min="2" max="<?= $limit ?>" /> (Maximum <?= number_format($limit) ?>) <br />
        <input type="submit" name="submit" value="Submit" onclick="this.value='Processing...';" />
      </fieldset>

      <fieldset>
        <legend>Options:</legend>
        Layout:
        <input type="radio" name="layout" value="v" checked /> Vertical | <input type="radio" name="layout" value="h" /> Horizontal | <input type="radio" name="layout" value="t" /> Textarea<br />
        <input type="checkbox" name="return_nonprimes" /> Also return a list of composite (non prime) numbers <br />
        <input type="checkbox" name="numformat" /> Format numbers (i.e. 12,345) <br />
      </fieldset>

    </form>

<?php

if(filter_input(INPUT_POST, 'submit')){ //form submitted

####### VALIDATION #######

  $layout = filter_input(INPUT_POST, 'layout');
  $numformat = !is_null(filter_input(INPUT_POST, 'numformat')) ? 1 : 0;

  $start = filter_input(INPUT_POST, 'start', FILTER_VALIDATE_INT, array("min_range"=>1, "max_range"=>($limit-1)));
  $finish = filter_input(INPUT_POST, 'finish', FILTER_VALIDATE_INT, array("min_range"=>($start+1), "max_range"=>$limit));

  $return_nonprimes = !is_null(filter_input(INPUT_POST, 'return_nonprimes')) ? 1: 0;

  // Handle invalid or empty inputs
	$start = ($start == false || is_null($start)) ? $default['start'] : $start;
  $finish = ($finish == false || is_null($finish)) ? $default['finish'] : $finish;

// Prevent outside IPs from requesting a huge calculation
  if(($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && !ereg("^192.168+",$_SERVER['REMOTE_ADDR'])) && $finish > $limit){
	   $finish = $limit;
     echo "   <p><strong>Request too large, limiting calculations to ". number_format($finish) . "</strong></p>\n";
  }

####### BENCHMARK #######
$time_start = microtime(true);
$operations = 0;

####### CALCULATIONS #######
for($x = $start; $x <= $finish; $x++){
  $last_div = $x / 2; // round()?
   for($y = 2; $y <= $last_div; $y++){
     $operations++;
     if($x % $y === 0) { // $x is divisible by y without a residual and therefore not prime
       $not_prime_array[] = $x;
       $not_prime_divby[] = $y;
      break 1;
    }

   }
 }

  ####### BENCHMARK #######
  $time_end = microtime(true);
  $time_elapsed = $time_end - $time_start;

  $primearray = array_diff(range($start,$finish),$not_prime_array);
?>

    <fieldset>
      <legend>Info:</legend>
        <p>Searching for prime numbers between <?= $start ?> and <?= $finish ?>...</p>
        <p>Processing time: <?= number_format($time_elapsed , 13 , '.' , ',') ?> seconds (<?= number_format($operations) ?> operations).</p>
    </fieldset>

    <fieldset>
      <legend>Results:</legend>
<?php

  if(!empty($primearray)){

  echo number_format(count($primearray)) . " prime numbers found: <br />\n";


    switch($layout){
      case 'h':
        foreach($primearray as $prime){
          echo ($numformat) ? number_format($prime) : $prime;
          echo " \n";
        }
      break;

      case 'v':
        foreach($primearray as $prime){
          echo ($numformat) ? number_format($prime) : $prime;
          echo " <br />\n";
        }
      break;

      default:
          echo "<form>\n<textarea rows='20' cols='10' wrap='hard'>\n";
        foreach($primearray as $prime){
          echo ($numformat) ? number_format($prime) : $prime;
          echo "\n";
        }
          echo "\n</textarea></form>\n";
      break;

    }


    if($return_nonprimes){
?>
    <hr />
    Composite numbers and first divisor without a residual: <br />

<?php
      for($x=0; $x < count($not_prime_array); $x++){
        echo $not_prime_array[$x] . ' div by ' . $not_prime_divby[$x] . "<br />\n";
      }
    }


  } else { // empty $primearray
    echo "No prime numbers found";
  }
?>

    </fieldset>

<?php
} // submit
?>

    <hr />
    <fieldset>
      <legend>About this tool:</legend>
      <p><strong>Finds all prime numbers within a range of 2 values.</strong></p>

      Change log:
      <dl>
        <dt>6/14/2015</dt>
        <dd><p>I've learned a lot in the last 10 years, specially since I started coding for a living.<br />
          Got this script out and updated it with better input filtering, code layout improvements, html5 enhancements.<br />
          Most notable is a much improved calculation method that limits the divisions to 1/2 of the number being checked (as anything bellow that would return a fractional number) and most importantly it now uses <strong>modulos</strong>, also eliminating the need for the additional type checking.<br />
          <p>Benchmark with new calculation method (2.8GHz Intel Core i7)<br />
          <em>1 - 1,000 test:</em><br />
           Before: Processing time: 0.0402719974518 seconds (78,022 operations). <br />
           Now: Processing time: 0.0045177936554 seconds (40,043 operations) (10 times faster!) <br />
          <em>1 - 10,000 test:</em><br />
           Before: Processing time: 2.5108559131622 seconds (5,775,223 operations). <br />
           Now: Processing time: 0.4331419467926 seconds (2,907,640 operations). <br />
          <em>1 - 100,000 test:</em><br />
           Before: Not happening. <br />
           Now: Processing time: 38.5100219249725 seconds (227,995,678 operations).</p>

           <p><strong>To do:</strong> rewrite this in Javascript and remove the limit.</p>
        </dd>

        <!-- <dt>7/29/2013</dt>
        <dd>Found this old script and debugged it, a lot.</dd> -->

        <dt>6/27/2005</dt>
        <dd><p>Added functionality and other improvements:<br />
          <em>Presentation:</em><br />
          Added option for vertical or horizontal layout.<br />
          Added option to format the numbers returned by the script.<br />
          Cleaned up markup (very basic to begin with) now 4.01 Transitional Valid (W3C DTD).<br />
          <br />
          <em>Performance:</em><br />
          Added the option to return a list of non-prime numbers and the first divisor that returns an integer.<br />
          Better benchmarking.<br />
          The new P4 server ran the 1-1000 task (78,022 ops) in 0.1383790969849 seconds when tested.
          (even whith the new non-prime/div array)<br />
          <br />
          <em>Security:</em><br />
          Added some input validation (input non empty, is an integer, min < max).<br />
          Limited the target number when script is called from an outside network.</p>
        </dd>

        <dt>4/30/2002</dt>
        <dd><p>Changed the inner loop to start from 2 and count up to x - 1. (it used to start at x-1 and counted down to 2)
          This greatly improved performance, in the benchmark for finding primes 1 - 1000:<br />
          Old script: Process time: 11.336248993874 seconds (335052 operations)<br />
          New script: Process time: 2.8276569843292 seconds (78022 operations)<br />
          (for example: 100 had to be divided by 99, 98... until it got to 50 where it returned 2, now it returns non-prime with the very first division by 2)<br />
          Don't ask me why I originally did it the other way.<br />
          Increased script execution timeout to 2 minutes.</p>
        </dd>
      </dl>
    </fieldset>
  </body>
</html>
