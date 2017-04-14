<?php 

/*
SCRIPT: 
CREATED BY: KASHIF ASIF
Email: kashifasif.68@outlook.com
*/

set_time_limit(0);
error_reporting(0);

// database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amazondailydealdb";

// download data from amazon	
function DownloadDailyDeals(){
	copy("https://rssfeeds.s3.amazonaws.com/goldbox", "temp.xml");
	echo "File Content is copied";
}

// download file
DownloadDailyDeals();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// clean db string
function cleanStr($val){        // TO CLEAN AN FOR SECURITY
    $str = trim($val);
    if(!get_magic_quotes_gpc()){            // if magic_qoutes_gps is off
        $str = addslashes($str);
    }
    return $str;
}

//file verfication
$fileIntoString = file_get_contents("temp.xml");

if (strpos($fileIntoString, '</rss>') > -1) 
{
	echo "found";
}
else
{
	echo "not fonund";
	DownloadDailyDeals();
}
//end of varification

$x = simplexml_load_file("temp.xml");
$sql = "";
$i = true;
// extracting data
foreach($x->channel->item as $lang) {
	
		set_time_limit(30);
		
		$_dealPrice;
		$_listprice;
		$_claim;
		$_title;
		$_expDate;
		$_imgMatch;
		$_link;
		$_pubDate;
		
		//Product Publish Date
		$_pubDate = $lang->pubDate;
		
		// image of product
		preg_match('/src="([^"]+)"/', $lang->description, $imgMatch, PREG_OFFSET_CAPTURE, 0);
		$imghandler = $imgMatch[1];
		if(!empty($imghandler[0])){
			$_imgMatch = $imghandler[0];
		}

		// title 
		$_title = $lang->title;
		
		// link
		$_link = $lang->link;

		
		// Expire Date
		preg_match('/(Expires.*?)(.+)(<\/td><\/tr><\/td>)/', $lang->description, $expDatematches, PREG_OFFSET_CAPTURE, 0);
		$expdate = $expDatematches[2];
		if(!empty($expdate[0])){
			$_expDate = $expdate[0];
		}
		
		
		// Deal Price
		preg_match('/(Deal Price:.*?)(.+)(<\/b>)/', $lang->description, $matches, PREG_OFFSET_CAPTURE, 0);
		$deailPrice = $matches[2];
		if(!empty($deailPrice[0])){
			$_dealPrice = $deailPrice[0];
		}
		
		
		// List Price
		preg_match('/(List Price:.*?<strike>)(.+)(<\/strike>)/', $lang->description, $listPriceMatch, PREG_OFFSET_CAPTURE, 0);
		$listprice = $listPriceMatch[2];
		if(!empty($listprice[0])){
			$_listprice = $listprice[0];
		}
		
		// % claim
		preg_match('/(\d{1,}|\d{1,}\.\d{1,})\s*%/', $lang->description, $percentageclainMatch);
		$claim = $percentageclainMatch;
		if(!empty($claim[0])){
			$_claim = $claim[0];
		}
		
			$_dealPrice = mysqli_real_escape_string($conn, $_dealPrice);
			$_listprice =mysqli_real_escape_string($conn, $_listprice);
			$_claim =mysqli_real_escape_string($conn, $_claim);
			$_title =mysqli_real_escape_string($conn, $_title);
			$_expDate = mysqli_real_escape_string($conn, $_expDate);
			$_imgMatch = mysqli_real_escape_string($conn, $_imgMatch);
			$_link = mysqli_real_escape_string($conn, $_link);
			$_pubDate = mysqli_real_escape_string($conn, $_pubDate);
		
		// search data to avoid duplication
		$searchQuery = "select * from dailydeals WHERE p_title = '$_title' AND 	product_link = '$_link' AND product_publish_date = '$_pubDate' AND p_expire_date = '$_expDate' AND 	list_price = '$_listprice' AND deal_price = '$_dealPrice' AND deal_claimed = '$_claim'";
		$count = 0;
		if ($result = mysqli_query($conn, $searchQuery))
		{
				// Fetch one and one row
				while ($row = mysqli_fetch_array($result))
				{
					$count = $count + 1;
				}
				// Free result set
			mysqli_free_result($result);
		}else{
			echo("<br />Error description: " . mysqli_error($conn));
		}

		if($count == 0){
			$sql = "INSERT INTO dailydeals (`id`, `p_title`, `product_link`, `product_publish_date`, `p_expire_date`, `product_image_url`, `list_price`, `deal_price`, `deal_claimed`) VALUES(default, '$_title', '$_link', '$_pubDate', '$_expDate', '$_imgMatch', '$_listprice', '$_dealPrice', '$_claim')";
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully <br />";
			}
		}
		
		$_dealPrice = "";
		$_listprice = "";
		$_claim = "";
		$_title = "";
		$_expDate = "";
		$_imgMatch = "";
		$_link = "";
		$_pubDate = "";
		$sql = "";
	}
 ?>