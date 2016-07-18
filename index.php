<?php


// The fully qualified URL to your WHMCS installation root directory
$whmcsUrl = "freetimespress.com/whmcs/";

// Admin username and password
$username = "bryan";
$password = "Bungie1997";


// Set post values
$postfields = array(
    'username' => $username,
    'password' => md5($password),
    'action' => 'getclientsproducts',
    'clientid' => "1",
    'responsetype' => 'json',
);


//$postfields["action"] = "getclientsproducts";
//$postfields["clientid"] = "1";

// Call the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $whmcsUrl . 'includes/api.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
$response = curl_exec($ch);

if (curl_error($ch)) {
    die('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
}
curl_close($ch);

// Attempt to decode response as json
$jsonData = json_decode($response, true);
//print_r($jsonData);

// Dump array structure for inspection
//var_dump($jsonData);

$output = print_r($jsonData, true);

$file = "status.txt";
$lines = file($file, FILE_IGNORE_NEW_LINES);
$i = 0;

foreach($lines as $key => &$line) {
    //echo $key;

    if ($key % 2 == 0) {
        $serverstatus = $line;
        continue;
    }

    if ($key % 2 == 1) {
        $servername = $line;
    }

    if (strpos($output, "[servername] => $servername") != false || $servername == "Network") {

        // CODE FOR ALERT WILL GO HERE

        foreach ($jsonData['products']['product'] as $product) {
        	$productname = $product['translated_name'];
        }


        ?>
        <html>
        <head>
        <style>
        .alert {
            padding: 20px;
            background-color: #e74c3c;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
            margin-bottom: 15px;
        }

        .alert.degraded {background-color: #f1c40f;}
        .alert.partial {background-color: #e67e22;}

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .closebtn:hover {
            color: black;
        }
        </style>
        </head>
        <body>

        <? if ($serverstatus == "major outage") { ?>
        <div class="alert">
          <span class="closebtn">&times;</span>
          <strong>Warning!</strong> <? echo "There is a $serverstatus alert on the $servername server where your $productname product is hosted." ?>
        </div>
        <?}

        if ($serverstatus == "degraded performance") { ?>
        <div class="alert degraded">
          <span class="closebtn">&times;</span>
          <strong>Notice!</strong> <? echo "There is a $serverstatus alert on the $servername server where your $productname product is hosted." ?>
        </div>
        <? }

        if ($serverstatus == "partial outage") { ?>
        <div class="alert partial">
          <span class="closebtn">&times;</span>
          <strong>Warning!</strong> <? echo "There is a $serverstatus alert on the $servername server where your $productname product is hosted." ?>
        </div>
        <? } ?>

        <script>
        var close = document.getElementsByClassName("closebtn");
        var i;

        for (i = 0; i < close.length; i++) {
            close[i].onclick = function(){
                var div = this.parentElement;
                div.style.opacity = "0";
                setTimeout(function(){ div.style.display = "none"; }, 600);
            }
        }
        </script>

        </body>
        </html>
        <?


        //break;
    }


}

?>
