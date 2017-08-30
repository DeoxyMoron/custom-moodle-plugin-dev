<?php

$url = 'https://api.scratch.mit.edu/users/djsanosa/projects/169189283';


//  Initiate curl
$ch = curl_init();
// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);



//echo 'wwwwwwwwww\r\n';
//echo $result;
$json = json_decode($result, true);
echo $json['title'];
echo $json['image'];
$imageurl = $json['image'];
echo "<img src='{$imageurl}'>"
//echo 'aaaaaaaaaaa\r\n';
//echo "<pre>"; print_r($result); die;
//$test = file_get_contents("https://api.scratch.mit.edu/users/djsanosa/projects/169189283");
//echo $test;
?>
