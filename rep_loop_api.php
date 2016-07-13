<?php

class funct{
	var $data;
	var $html;
	var $show_per_page=2;
 	function build_table($array,$tablename=NULL,$showbars = false,$style = "style=\"width: 100%;"){
    // start table
	$this->html="";
	if($tablename!=NULL)
    $this->html = "<table id=\"$tablename\"  >\n";
	else
	$this->html = "<table  $style\">\n";
    // header ro
    $this->html .= "<tr>\n";
   
    foreach( $array as $key=>$value){
        
       
           
			if(is_array($value)){
				foreach( $value as $key2=>$value2){
					$this->html .= "<tr><th>". ucwords(str_replace("_"," ",$key2)) ."</th><td>" .  $value2 . "</td></tr>\n";
				}
			}else{
				 $this->html .= "<tr><th>\n" . ucwords(str_replace("_"," ",$key)) . "</th>\n";
				$this->html .="<td>" . $value . "</td></tr>\n";
			}
			if($showbars)
        $this->html .="<tr><td colspan='2' style='background-color:black'>&nbsp;</td></tr>";
    }
	$this->html .= "</tr>";
    // finish table and return it

    $this->html .= "</table>";
    return $this->html;
}
	function paganation($display_array, $page) {
        $show_per_page = $this->show_per_page;

        $page = $page < 1 ? 1 : $page;

        // start position in the $display_array
        // +1 is to account for total values.
        $start = ($page-1) * ($show_per_page);
        $offset = $show_per_page;

        $outArray = array_slice($display_array, $start, $offset);

        return $outArray;
    }
	function curl_get($url, array $options = array()){    
			$defaults = array( 
				CURLOPT_URL => $url, 
				CURLOPT_HEADER => 0, 
				CURLOPT_RETURNTRANSFER => TRUE, 
				CURLOPT_TIMEOUT => 4 
			); 
			
			$ch = curl_init(); 
			curl_setopt_array($ch, ($options + $defaults)); 
			if( ! $result = curl_exec($ch)) 
			{ 
				trigger_error(curl_error($ch)); 
			} 
			curl_close($ch); 
			return $result; 
		} 
	function convert_array(&$array){
		foreach($array as $key=>$value){
			foreach($value as $key2=>$value2){
			if($key2=="review_from") {
				///echo $value2;
				switch($value2){
					case "0": $array[$key][$key2]="internal"; break;
					case "1": $array[$key][$key2]="yelp";break;
					case "2": $array[$key][$key2]="google";break;
				}
			}
			}
		}
		
	}
	function stdtoarray($std){
		return json_decode(json_encode($std),true);
	}
	function pagnation_bar($page,$whole_arr){
		$show_per_page = $this->show_per_page;
		$line = "<div style=\"width:100%; display:inline; padding-left:50px;\">pages<div style=\"width:100%; display:inline; padding-left:75px; letter-spacing: 25px;  \">";
		$count = count($whole_arr);
		$pages = $count/$show_per_page;
		//echo "count is " .$count . " and the $pages is " . $pages;
		for($i=1;$i < $pages+1;$i++){
			if($i==$page) 	$line .= $i;
			else $line .= "<a href=\"?page=$i\">$i</a>";
			
		}
		$line.="</div></div>";
		return $line;
		
	}
}
$new1 = new funct();

$data = $new1->curl_get("http://test.localfeedbackloop.com/api?apiKey=61067f81f8cf7e4a1f673cd230216112&noOfReviews=10&internal=1&yelp=1&google=1&offset=50&threshold=1");

try{
$data = json_decode($data);
}
catch(Exception $e){
	print_r($e);
}

$business = $new1->stdtoarray($data->business_info);
$reviews = $new1->stdtoarray($data->reviews);
$rev_arr = $reviews;
//print_r($data);
$new1->convert_array($reviews);
$top = $new1->build_table($business);
if(!isset($_GET["page"])) $_GET["page"] = 1;
$reviews = $new1->paganation($reviews,$_GET["page"]);
$reviews = $new1->build_table($reviews,"reviews",true);
$bar = $new1->pagnation_bar($_GET["page"],$rev_arr);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Display Company Info</title>
<style>
	.tableno {
		border:0px hidden #C0C0C0;	
		border-collapse:separate;
		padding:0;
	}
	table {
		border:2px dotted #C0C0C0;
		border-collapse:collapse;
		padding:5px;
	}
	table th {
		border:1px solid #C0C0C0;
		padding:5px;
		background:#F0F0F0;
	}
	table td {
		border:1px solid #C0C0C0;
		padding:5px;
	}
a {
	border: thin solid black;
	text-align:center;
	text-decoration:none;
	text-indent:hanging;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
</head>

<body>
<?PHP echo "<table class=\"tableno\"><tr>
<td colspan=\"2\">". $top . "</td>
</tr>
<tr><td width='200px'>&nbsp;</td><td>REVIEWS<!--<input name=\"show\" type=\"button\" value=\"show reviews\" onclick=\"$('#reviews').toggle()\" />-->". $bar . "</td></tr>
<tr>
<td width='200px'>&nbsp;</td><td>" . $reviews . "</td>
</tr></table>";?>
</body>
</html>