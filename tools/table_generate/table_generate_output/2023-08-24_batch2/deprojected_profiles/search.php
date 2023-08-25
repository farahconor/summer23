<?PHP 
// TODO: Review data type
// TODO: Review units
// TODO: fix descriptions

$file_name = "deprojected_profiles_kpc_H070_Om0.3.csv"
$RA_index = 12
$DEC_index = 13

class conesearch
{
  //follow this: https://www.ivoa.net/documents/VOTable/20191021/REC-VOTable-1.4-20191021.html#example1

  function errorDetection(){
    // this function checks the inputs to make sure they are all acceptable, if not it returns the detected errors
    $flags = "";
    
    if (isset($_GET["RA"])){ // checks if RA exists
      $RA = $_GET["RA"];
      $dataType = $this->dataTyper($RA); // checks if RA is a number
      if ($dataType != 'int' && $dataType != 'float'){
          $flags .= "RA=" . $RA . " is not a number; ";
      } elseif ($RA > 360 || $RA < 0){ // checks if RA is in the correct range
          $flags .= "RA=" . $RA . " is out of range; ";
      }
    } else {
      $flags .= "No RA given; ";
    }
    if (isset($_GET["DEC"])){ // checks if DEC exists
      $DEC = $_GET["DEC"];
      $dataType = $this->dataTyper($DEC); // checks if DEC is a number
      if ($dataType != 'int' && $dataType != 'float'){
          $flags .= "DEC=" . $DEC . " is not a number ";
      } elseif ($DEC > 90 || $DEC < -90){ // checks if DEC is in the correct range
          $flags .= "DEC=" . $DEC . " is out of range; ";
      }
    } else {
        $flags .= "No DEC given; ";
    }
    if (isset($_GET["SR"])){ // checks if SR exists
      $SR = $_GET["SR"];
      $dataType = $this->dataTyper($SR); // checks if SR in a number
      if ($dataType != 'int' && $dataType != 'float'){
          $flags .= "SR=" . $SR . " is not a number; ";
      }
    } else {
        $flags .= "No SR given; ";
    }
    return $flags;
  }

  function angular_separation_PHP($ra1, $dec1, $ra2, $dec2){
    // takes 2 RAs and DECs in degrees and returns their separation in degrees
    // this function is very similar to "astropy.coordinates.angular_separation", astropy lists the inputs as "lon1, lat1, lon2, lat2", I have listed them as ra and dec instead
  
    // must convert RAs and DECs to radians
    $lon1 = deg2rad($ra1);
    $lat1 = deg2rad($dec1);
    $lon2 = deg2rad($ra2);
    $lat2 = deg2rad($dec2);
  
    //this code directly follows the "angular_separation" function from astropy: https://docs.astropy.org/en/stable/_modules/astropy/coordinates/angle_utilities.html#angular_separation
    $sdlon = sin($lon2-$lon1);
    $cdlon = cos($lon2-$lon1);
    $slat1 = sin($lat1);
    $slat2 = sin($lat2);
    $clat1 = cos($lat1);
    $clat2 = cos($lat2);
  
    $num1 = $clat2 * $sdlon;
    $num2 = $clat1 * $slat2 - $slat1 * $clat2 * $cdlon;
    $denominator = $slat1 * $slat2 + $clat1 * $clat2 * $cdlon;
  
    return rad2deg(atan2(hypot($num1, $num2),$denominator)); //slight deviation from astropy, I convert back to degrees
  }

  function dataTyper($testee){
    // this function attempts to determine the data type of the given string
    // this function won't work if a float in the first row happens to be a whole number, i.e. 5.0
    if ($testee=='NaN'){
      $type = 'float';
    } elseif ($testee == (string)(int)$testee){ // converts string to integer, then back to string and compares it to original string
      $type = 'int';
    } elseif ($testee == (string)(float)$testee){ // converts string to float, then back to string and compares it to original string
      $type = 'float';
    } else {
      $type = 'char" arraysize="*'; // "char" datatypes need an array size given since strings are not considered datatypes, this is a sort of hacky solution to include that array size without having to use addition conditional statements
    }
    return $type;
  }

  function csvToArray($csvFile){
    // this function takes a csv file and converts it to a php array
    $file_to_read = fopen($csvFile, 'r');
    while (!feof($file_to_read) ) {
        $lines[] = fgetcsv($file_to_read);
    }
    fclose($file_to_read);
    return $lines;
  }

  function header($errorMsg){
    // this function initializes the VOTable, including the column names for the data (all manually entered)
    header("Content-Type: text/xml");
    echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',"\n",
    '<VOTABLE version="1.4" xmlns="http://www.ivoa.net/xml/VOTable/v1.3">', "\n",
    $errorMsg,
    '<RESOURCE>',"\n",
    '<TABLE name="' , $file_name , '">',"\n";
  }

  function fields($catalog, $unitExceptions){
    // this function writes all the fields for the VOTable, which contain the column names, units, and datatypes
    for ($i=0; $i < count($catalog[0]); $i++) {
      if (array_key_exists($catalog[1][$i], $unitExceptions)){ // this checks if there are any exceptions to the units listed in the csv, example: "degree" should be "deg" in VOTable units
        $unit = $unitExceptions[$catalog[1][$i]];
      } else {
        $unit = $catalog[1][$i];
      }
      echo '<FIELD ID="col', $i+1, '" name="', $catalog[0][$i], '" datatype="', $this->dataTyper($catalog[2][$i]), '" unit="', $unit, '"/>', "\n";
    }
    echo '<DATA>',"\n",
    '<TABLEDATA>',"\n";
  }

  function writeData($catalog){
    // this function iterates over each row in the php array and checks to see if the RA and DEC of the object is within the search radius
    // if it is, it echos the data from that row in the VOtable format
    for ($i = 3; $i < count($catalog); $i++) { //$i should be set to the index of the first row of data
      if ($this->angular_separation_PHP($_GET["RA"],$_GET["DEC"],$catalog[$i][$RA_index],$catalog[$i][$DEC_index])<$_GET["SR"]) {
        echo "<TR>\n"; #start table row
        for ($j = 0; $j < count($catalog[$i]); $j++) {
          echo "<TD>", $catalog[$i][$j], "</TD>"; #add data to the row
        }
        echo "\n</TR>\n"; #end table row
      }
    }
    echo "</TABLEDATA>\n",
    "</DATA>\n";
  }

  function end()
  {
    // this function closes the VOTable
    echo "</TABLE>\n",
    "</RESOURCE>\n",
    "</VOTABLE>\n";
    exit;
  }

}

$unitExceptions = [ // list any units that need to be changed from what is listed in the csv
  "degree" => "deg",
  "erg" => "cm2 g/s2",
  "erg/s" => "cm2 g/s3",
  "erg/s/cm2" => "g/s3",
  "--" => ""
];

$xml = new \conesearch();

$errorFlags = $xml->errorDetection(); // checks for errors in the inputs

if ($errorFlags==""){ // if there are no errors, proceedes normally, otherwise displays the error
  $csvFile = $file_name;
  $catalog = $xml->csvToArray($csvFile); //get for step 2 and 3
  
  $xml->header($errorMsg='<INFO name="QUERY_STATUS" value="OK"/>'."\n"); // step 1
  
  $xml->fields($catalog, $unitExceptions); // step 2
  
  $xml->writeData($catalog); // step 3
} else{
  $errorMsg = '<INFO ID="Error" name="Error" value="' . $errorFlags . '"/>' . "\n";

  $xml->header($errorMsg=$errorMsg); // step 1

  // skips steps 2 and 3
}

$xml->end(); // step 4

exit;
?>;
?>