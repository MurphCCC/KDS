<?php
	define('__ROOT__', dirname(dirname(__FILE__)));

	require_once(__ROOT__.'/dbconfig.php');
	require_once(__ROOT__.'/apiconfig.php');

	$db = mysqli_connect('host','user','password','database')
	        or die('Error connecting to server.');

	$timeCurrent = date("Y-m-d H:i:s");
	$timePast = date('Y-m-d H:i:s',time() - 10 * 60);
        $post_orders = "dataname=$api_dataname&key=$api_key&token=$api_token&table=order_contents&column=device_time&value_min=$timePast&value_max=$timeCurrent&valid_xml=1&limit=0,5";

/*Old Curl code used to query the api
        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $api_url);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	curl_setopt($ch, CURLOPT_POST, 1);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_orders);
        	$response = curl_exec($ch);
		curl_close ($ch);
print_r($response);

//cleanInput($response);
*/

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.poslavu.com/cp/reqserv/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"dataname\"\r\n\r\n*dataname*\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"key\"\r\n\r\n*key*\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"token\"\r\n\r\n*token*\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"table\"\r\n\r\norder_contents\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"column\"\r\n\r\ndevice_time\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"value_min\"\r\n\r\n$timePast\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"value_max\"\r\n\r\n$timeCurrent\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"valid_xml\"\r\n\r\n1\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"limit\"\r\n\r\n0,200\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; 
  							name=\"filters\"\r\n\r\n[{\"field\" : \"category_id\", \"operator\" : \"!=\",  \"value1\"  :  \"148\"},{\"field\" : \"category_id\", \"operator\" : \"!=\",  \"value1\"  :  \"155\"},{\"field\" : \"category_id\", \"operator\" : \"!=\",  \"value1\"  :  \"156\"},{\"field\" : \"category_id\", \"operator\" : \"!=\",  \"value1\"  :  \"157\"},{\"field\" : \"category_id\", \"operator\" : \"!=\",  \"value1\"  :  \"158\"}]\r\n-----011000010111000001101001--",
  CURLOPT_HTTPHEADER => array(
    "content-type: multipart/form-data; boundary=---011000010111000001101001"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

echo $response;




	$xmlstr = simplexml_load_string($response);

	foreach ($xmlstr->row as $row) {

		$id = $row->id;
		$item_id = $row->item_id;
        $order = $row->order_id;
	    $item = $row->item;
		$item = addslashes($item);
	    $notes = $row->notes;
        $quantity = $row->quantity;
	    $options = $row->options;
		$special = $row->special;
		$print = $row->print;
		$ticket_time = $row->device_time;
		$category_id = $row->category_id;


		        	$insert =  "INSERT IGNORE INTO tickets (id, item_id, order_id, item, notes, quantity, check_number, special, ticket_time, print, category_id)
	                        VALUES('$id', '$item_id', '$order', '$item', '$notes', '$quantity', '$options', '$special', '$ticket_time', '$print', '$category_id')";


	        if ($print == 1) {


					$db->query($insert);
					$filter_modifier = "UPDATE tickets SET modifier = '' WHERE item = 'Check #'";
					$db->query($filter_modifier);
	}
}
				

					
	$db->close();
/*

$orders = simplexml_load_string($response);


	foreach($orders->row as $row) {
		$id=$row->item_id;
		$item=$row->item;
		printf(
			$id . "-" . $item . "-" . $row->order_id . "<br>" ,
			"<br>"
		);
	}


/*
//	$orders = (object) array_filter((array) $orders);

	$json = json_encode($orders, JSON_PRETTY_PRINT);
//ø	echo preg_replace('/"(tax.)": {},/', '', $json);


//	$patterns = array();
//	$patterns[0] = '/"(.*?)tax(.*?)": (.*?),/';
//	$patterns[1] = '/"(.*?)discount(.*?)": (.*?),/';
//	$patterns[2] = '/"(.*?)hidden(.*?)": (.*?),/';

//	$replacements = array();
//	$replacements[0] = '';
//
//	$json = preg_replace($patterns, $replacements, $json);
//	$json = preg_replace('/^\s+/m', '', $json);
//	echo $json;

	$data = json_decode($json, true);

	$id = $data['id'];

	echo $id;

	$query = "INSERT INTO tickets(id) VALUES($id)";

	$db->query($query);
*/

//echo $response;
        $db = mysqli_connect('localhost','root','N0bigdeal','lavu')
                or die('Error connecting to server.');

                                $filter_notes = 'UPDATE tickets SET item = CONCAT(quantity, "-", item, "<br>", "<modifier>", notes, "</modifier>") WHERE notes != ""';
				$saute = "UPDATE tickets INNER JOIN menu_items 
							ON tickets.item_id = menu_items.id 
								AND menu_items.category = 'saute' 
									SET tickets.item = concat('<saute>', item, '</saute>') 
										WHERE item NOT LIKE '%<saute>%'";

					$sandwich = "UPDATE tickets INNER JOIN menu_items 
							ON tickets.item_id = menu_items.id 
								AND menu_items.category = 'sandwich' 
									SET tickets.item = concat('<sandwich>', item, '</sandwich>') 
										WHERE item NOT LIKE '%<sandwich>%'";


				$clear_notes = 'UPDATE tickets SET notes = ""';
				$clear_quantity = 'UPDATE tickets SET quantity = ""';		
						$db->query($filter_notes);
						$db->query($clear_notes);
						$db->query($clear_quantity);
						$db->query($saute);
						$db->query($sandwich);
			                                        

?>
