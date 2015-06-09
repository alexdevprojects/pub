<?php
	/**
	 * Compares two key=>value sets to determine which have been added, changed or deleted on the second set.
	 * 
	 * Returns object with the properties 'add', 'update', 'delete' and optionally 'same'
	 * containing arrays of categorized data, also an int 'differences' which is the sum 
	 * of add, update and delete. Useful to write SQL for data handling. 
	 * If $result->differences == 0 there's no difference between the datasets
	 * @access	public
	 * @param	array	original key=>value data
	 * @param	array	newest key=>value data
	 * @param	bool	also return a list of unchanged values
	 * @param	bool	use identical comparison (===)
	 * @return object
	 */
	function differences($a1, $a2, $unchanged = true, $identical = true){
	 
		$result = new stdClass();

		$result->add = array_diff_key($a2, $a1);
		$result->delete = array_diff_key($a1, $a2);
		$result->update = array();

		$both = array_intersect_key($a1, $a2);
		if($unchanged) $result->same = array();

		foreach($both as $id=>$val){
			if($identical && $a1[$id] !== $a2[$id])
				$result->update[$id] = $a2[$id];
			elseif($a1[$id] != $a2[$id])
				$result->update[$id] = $a2[$id];
			elseif($unchanged)
				$result->same[$id] = $a2[$id];
		}

		$result->differences = count($result->add) + count($result->update) + count($result->delete);

		return $result;
	}
	
	/**
	 * Turns a simple array into a key=>value multi-dimentional one 
	 * 
	 * Converts a simple array into a key=>value one with the keys matching the values
	 * It prepares an array of values into something useful for the {@link differences()} function
	 * @access	public
	 * @param	array	
	 * @return	array
	 */
	function my_array_upscale($a){
		return array_flip( array_combine($a, $a) );
		// array_flip is only a safeguard to remove any possible duplicate values
	}
?>

<?php	
/* * TEST * */
  $asHTML = false;

  $array1 = array('one'=>'5'
  				, 'two'=>'5'
  				, 'three'=>'5'
  				, 'four'=>'5'
  				, 'five'=>'5');

  $array2 = array('one'=>'10'
  				, 'two'=>'5'
  				, 'three'=>'1'
  				, 'four'=>5
  				, 'six'=>'10'
  				, 'seven'=>'10');

  $test = differences($array1, $array2, true);
/* Expected output:
 * one 		- update +
 * two 		- same
 * three 	- update -
 * four 	- update (type) or same (if not using identical comparison)
 * five 	- delete
 * six 		- add
 * seven 	- add
 */	

if (!$asHTML)
	var_dump($test);
else {
?>
<!DOCTYPE html>
<html>
	<body>
		<pre>
<?= var_dump($test); ?>
		</pre>
	</body>
</html>
<?php
}
?>