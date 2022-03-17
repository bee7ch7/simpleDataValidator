
<?php

//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header('X-Powered-By:');


function test_input($data) {
$data = strip_tags(trim($data));
$data = stripslashes($data);
$data = htmlspecialchars($data);
return $data;
}

$headers = apache_request_headers();
$hostname = $_SERVER['REMOTE_ADDR'];
$method = $_SERVER['REQUEST_METHOD'];

//echo $hostname;


// if not POST - redirect to main website
if ($method !== "POST") {
	
	http_response_code(303);
	header('Location: https://www.meldm.com/');
	exit();
	
}

// if content-length > 5000 - 400- bad request
if ($headers['Content-Length'] > 5000) {
	
	http_response_code(400);
	exit();
	
}

// if api key is not correct on content-type is not json - 401 - Unauthorized 
if ($headers['Api-X-Key'] !== 'passwd123' or $headers['Content-Type'] !== 'application/json') {
	
	
	http_response_code(401);
	exit();
		
}


// if input is not valid json - 400 - bad request
$check_input_for_valid_json = json_decode( file_get_contents('php://input') , true );

if( $check_input_for_valid_json == NULL ) {
	
	http_response_code(400);
	exit();
	
}
	



//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation
//// if checks is ok - proceed to var validation





//// get input data 
$input_json = json_decode(file_get_contents('php://input'),1);

function validateFormat($text) {
    return preg_match ('/^[a-zA-Z\p{Cyrillic}\s]+$/u', $text);
}


$customer_lines = $input_json['customer'];
$product_lines = $input_json['invoice'];
$storeid = $input_json['subdivision'];
$order_type = $input_json['order_type'];


/// static data 

//$order_type = 4;
$customer_id = "409872";


/// allowed stores 

$allowed_stores = array(1,2,3,4,5,6,7);
$allowed_order_types = array(1,4);


/// if storeid is not numeric and not in list - error code 10
if (!is_numeric($storeid) or !in_array($storeid, $allowed_stores) ) {
	
			echo json_encode(array(
			"code" => "10",
			"message" => "Invalid storeid."
			));
			exit();
}


if (!is_numeric($order_type) or !in_array($order_type, $allowed_order_types) ) {
	
			echo json_encode(array(
			"code" => "14",
			"message" => "Invalid order type."
			));
			exit();
}

/// validate customer lines 
if ($customer_lines) {
	
	/// if name contains numeric chars or special symbols or more than 20 chars - error code 11
	if (!validateFormat($customer_lines['name']) or mb_strlen($customer_lines['name']) > 20) {
			
			http_response_code(400);
			echo json_encode(array(
			"code" => "11",
			"message" => "Name is too long or contain special chars."
			));
			exit();
	}
		/// if name contains numeric chars or special symbols or more than 20 chars - error code 12
		if (!validateFormat($customer_lines['surname']) or mb_strlen($customer_lines['surname']) > 20) {

			http_response_code(400);
			echo json_encode(array(
			"code" => "12",
			"message" => "Surname is too long or contain special chars."
			));
			exit();
	}
		/// if phone is not numeric and more than 10 chars
		if (!is_numeric($customer_lines['phone']) or strlen($customer_lines['phone']) > 10) {

			http_response_code(400);
			echo json_encode(array(
			"code" => "13",
			"message" => "Phone is too long or contain not numeric chars."
			));
			exit();
	}
	
}


/// validate product line 

if (is_array($product_lines)) {
	
	$product_counter = 1;
	foreach ($product_lines as $product) {
		
	//	echo $product['code'];
	//	echo $product['quantity'];
	//	echo $product['type'];
	//	echo $product['unitPrice'];
		
		// if product code (lm) is not numeric or more than 8 chars 
		if (!is_numeric($product['code']) or strlen($product['code']) > 8) {

			http_response_code(400);
			echo json_encode(array(
			"code" => "21",
			"message" => "LM is too long or is not numeric. Line $product_counter"
			));
			exit();
	}
		// if qty is not numeric or float
		if (!is_numeric($product['quantity']) and !is_float($product['quantity'])) {

			http_response_code(400);
			echo json_encode(array(
			"code" => "22",
			"message" => "Qty is not numeric or float. Line $product_counter"
			));
			exit();
	}
			// if price is not numeric or float
			if (!is_numeric($product['unitPrice']) and !is_float($product['unitPrice'])) {

			http_response_code(400);
			echo json_encode(array(
			"code" => "24",
			"message" => "Price is not numeric or float. Line $product_counter"
			));
			exit();
	}
	
		// if product type is not in list 
		if ($product['type'] !== 'BV' and $product['type'] !== 'BVI' and $product['type'] !== 'SE' and $product['type'] !== 'CS' and $product['type'] !== 'RAP') {

			http_response_code(400);
			echo json_encode(array(
			"code" => "23",
			"message" => "Type of line is not available. Line $product_counter"
			));
			exit();
	}
	
	
	$product_counter++;
	}
	
}




///
///
///
///
///
///
///
///
///
///
/// if we still online 
/// prepare xml if data is valid and send to store



/// replace storeid 

$storeid = substr($storeid,2); 

if ($order_type == 1) {
	
	
	/////
	/////
	/////
	/////
	/////					ORDER
	/////
	/////
	/////
	/////
	/////


$xml_header = "
<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:v3=\"http://v3.order.wsprocess.pyxis.madeo.com\" xmlns:dto=\"http://dto.common.wsprocess.pyxis.madeo.com\" xmlns:dto1=\"http://dto.v3.order.wsprocess.pyxis.madeo.com\">
   <soapenv:Header/>
   <soapenv:Body>
      <v3:addOrder>
         <v3:in0>
            <v3:entityWS>
               <dto:typEtt>1</dto:typEtt>
               <dto:numBu>23</dto:numBu>
               <dto:numEtt>$storeid</dto:numEtt>
            </v3:entityWS>
";

//prepare product block

$xml_product_block = "
            <v3:orderWS>
               <dto1:orderId></dto1:orderId>
               <dto1:orderType>$order_type</dto1:orderType>
               <dto1:lines>
";

$total_order_price = 0;

foreach ($product_lines as $product) {
	
	$lm = $product['code'];
	$qty = $product['quantity'];
	$type = $product['type'];
	$price = $product['unitPrice'];
	
	$total_price = $qty * $price;
	
	$total_order_price += $total_price;
	
	$xml_product_block .= "
	                  <dto:lines>
                     <dto:lineId></dto:lineId>
                     <dto:itemPrice>$price</dto:itemPrice>
                     <dto:type>$type</dto:type>
                     <dto:itemQty>$qty</dto:itemQty>
                     <dto:dateliv></dto:dateliv>
                     <dto:numLine></dto:numLine>
                     <dto:refArticle>$lm</dto:refArticle>
                     <dto:linePrice>
                        <dto:priceVAT>$total_price</dto:priceVAT>
                        <dto:priceVATFree></dto:priceVATFree>
                        <dto:VATValue></dto:VATValue>
                        <dto:VAT>20</dto:VAT>
                        <dto:depositValue>$total_price</dto:depositValue>
                     </dto:linePrice>
                  </dto:lines>
	";
	
	
}
$xml_product_block_end = "
               </dto1:lines>
               <dto1:deliveryAddress/>
";

$xml_customer_block = "
               <dto1:customer>
                  <dto:clientId>$customer_id</dto:clientId>
               </dto1:customer>
               <dto1:idSeller></dto1:idSeller>
";

$xml_end_of = "
               <dto1:transactionPrice>
                  <dto:priceVAT>$total_order_price</dto:priceVAT>
                  <dto:priceVATFree></dto:priceVATFree>
                  <dto:VATValue></dto:VATValue>
                  <dto:VAT>20</dto:VAT>
                  <dto:depositValue>$total_order_price</dto:depositValue>
               </dto1:transactionPrice>
               <dto1:updateDate></dto1:updateDate>
            </v3:orderWS>
            <v3:version></v3:version>
            <v3:language></v3:language>
         </v3:in0>
      </v3:addOrder>
   </soapenv:Body>
</soapenv:Envelope>
";

$xml_ready_to_send = "
$xml_header
$xml_product_block
$xml_product_block_end
$xml_customer_block
$xml_end_of
";

} elseif ($order_type == 4) {
	
	/////
	/////
	/////
	/////
	/////					BVI 
	/////
	/////
	/////
	/////
	/////

	
	$xml_header = "
<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ns1=\"http://dto.common.wsprocess.pyxis.madeo.com\" xmlns:ns2=\"http://v2.salesorder.wsprocess.pyxis.madeo.com\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:ns3=\"http://dto.v2.salesorder.wsprocess.pyxis.madeo.com\">
<SOAP-ENV:Body>
<ns2:addSalesOrder>
            <ns2:in0>
                <ns2:entityWS>
                    <ns1:typEtt>1</ns1:typEtt>
                    <ns1:numBu>23</ns1:numBu>
                    <ns1:numEtt>$storeid</ns1:numEtt>
                </ns2:entityWS>
";

//prepare product block

$xml_product_block = "
                <ns2:salesOrderWS>
                    <ns1:transactionId xsi:nil=\"true\" />
                    <ns1:lines>
";

$total_order_price = 0;

foreach ($product_lines as $product) {
	
	$lm = $product['code'];
	$qty = $product['quantity'];
	$type = $product['type'];
	$price = $product['unitPrice'];
	
	$total_price = $qty * $price;
	
	$total_order_price += $total_price;
	
	$xml_product_block .= "
	                  <ns1:lines>
                     <ns1:lineId xsi:nil=\"true\" />
                     <ns1:itemPrice>$price</ns1:itemPrice>
                     <ns1:type>$type</ns1:type>
					 <ns1:lineType xsi:nil=\"true\" />
                     <ns1:itemQty>$qty</ns1:itemQty>
                     <ns1:dateliv xsi:nil=\"true\" />
                     <ns1:refArticle>$lm</ns1:refArticle>
					 <ns1:newLine48 xsi:nil=\"true\" />
                     <ns1:linePrice>
                        <ns1:priceVAT>$total_price</ns1:priceVAT>
                        <ns1:VAT>20</ns1:VAT>
                     </ns1:linePrice>
					 <ns1:lineStatus xsi:nil=\"true\" />
                  </ns1:lines>
	";
	
	
}
$xml_product_block_end = "
               </ns1:lines>

";

$xml_customer_block = "

				    <ns1:customer>
					   <ns1:gender xsi:nil=\"true\"/>
					   <ns1:faxNumber xsi:nil=\"true\"/>
					   <ns1:topSMS xsi:nil=\"true\"/>
					   <ns1:adresseComp xsi:nil=\"true\"/>
					   <ns1:cellPhoneNumber xsi:nil=\"true\"/>
					   <ns1:personnalPhoneNumber xsi:nil=\"true\"/>
					   <ns1:street xsi:nil=\"true\"/>
					   <ns1:numCard xsi:nil=\"true\"/>
					   <ns1:firstName xsi:nil=\"true\"/>
					   <ns1:lastName xsi:nil=\"true\"/>
					   <ns1:zipCode xsi:nil=\"true\"/>
					   <ns1:country xsi:nil=\"true\"/>
					   <ns1:professionnalPhoneNumber xsi:nil=\"true\"/>
					   <ns1:email xsi:nil=\"true\"/>
					   <ns1:clientId>$customer_id</ns1:clientId>
					   <ns1:stairway xsi:nil=\"true\"/>
					   <ns1:city xsi:nil=\"true\"/>
				   </ns1:customer>

               <ns1:idSeller xsi:nil=\"true\" />
";

$xml_end_of = "
               <ns1:transactionPrice>
                  <ns1:priceVAT>$total_order_price</ns1:priceVAT>
                  <ns1:VAT>20</ns1:VAT>
               </ns1:transactionPrice>
			    <ns3:transactionStatus xsi:nil=\"true\" />
            </ns2:salesOrderWS>
			<ns2:version xsi:nil=\"true\" />
           </ns2:in0>
        </ns2:addSalesOrder>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
";

$xml_ready_to_send = "
$xml_header
$xml_product_block
$xml_product_block_end
$xml_customer_block
$xml_end_of
";
	
	
}

/// define api servers for each store

//echo $xml_ready_to_send;


//exit();

 if ($storeid == '1') {
	   // define which method to call
				if ($order_type == 4) {
				$soap_url = "http://xx.xx2.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v2/salesOrderProcess";
				} elseif ($order_type == 1 or $order_type == 2) {
				$soap_url = "http://xx.xx2.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v3/orderProcess";
				}

     } elseif ($storeid == '2') {
		 // define which method to call
				if ($order_type == 4) {
				$soap_url = "http://xx.xx4.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v2/salesOrderProcess";
				} elseif ($order_type == 1 or $order_type == 2) {
				$soap_url = "http://xx.xx4.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v3/orderProcess";
				}

     } elseif ($storeid == '3') {
		 // define which method to call
				if ($order_type == 4) {
				$soap_url = "http://xx.xx6.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v2/salesOrderProcess";
				} elseif ($order_type == 1 or $order_type == 2) {
				$soap_url = "http://xx.xx6.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v3/orderProcess";
				}

     } elseif ($storeid == '4') {
		 // define which method to call
				if ($order_type == 4) {
				$soap_url = "http://xx.xx8.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v2/salesOrderProcess";
				} elseif ($order_type == 1 or $order_type == 2) {
				$soap_url = "http://xx.xx8.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v3/orderProcess";
				}

     } elseif ($storeid == '5') {
		 // define which method to call
				if ($order_type == 4) {
				$soap_url = "http://xx.xx10.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v2/salesOrderProcess";
				} elseif ($order_type == 1 or $order_type == 2) {
				$soap_url = "http://xx.xx10.130:10080/pyxis-vente-appsvc-v3.5/serviceCXF/v3/orderProcess";
				}
  }
  
  
  $curl = curl_init();


curl_setopt_array($curl, array(
  CURLOPT_URL => "$soap_url",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_POST => 1,
  CURLOPT_POSTFIELDS => "$xml_ready_to_send",

  CURLOPT_HTTPHEADER => array(
  	"Accept: text/xml",
    "Content-Type: text/xml; charset=utf-8",
    "cache-control: no-cache",
    "Content-length: ".strlen($xml_ready_to_send)
  ),
));

$response = curl_exec($curl);
$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

$err = curl_error($curl);


if ($err) {
  echo "cURL Error #:" . $err;
} else {
//  echo "<pre>";

$catch = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
$xml_create = new SimpleXMLElement($catch);
$body = $xml_create->xpath('//soapBody')[0];
$xml_response_to_array = json_decode(json_encode((array)$body), TRUE);

//print_r($xml_response_to_array);

if ($order_type == 1) {

$order_id = $xml_response_to_array['ns2addOrderResponseElement']['ns2out']['ns2order']['ns3orderId'];
$order_price = $xml_response_to_array['ns2addOrderResponseElement']['ns2out']['ns2order']['ns3transactionPrice']['priceVAT'];

$response_code = $xml_response_to_array['ns2addOrderResponseElement']['ns2out']['ns2responseCode'];
$response_err_msg = $xml_response_to_array['ns2addOrderResponseElement']['ns2out']['ns2errorMsg'];

	if ($response_code == 100 or !empty($order_id)) {
		
		echo json_encode(array(
			"order_id" => $order_id,
			"order_price" => $order_price,
			"response_code" => $response_code
			));
			exit();
		
	} else {
		echo json_encode(array(
			"response_code" => $response_code,
			"erro_msg" => $response_err_msg
			));
			exit();
	}
} elseif ($order_type == 4) {
	
	$order_id = $xml_response_to_array['ns2addSalesOrderResponseElement']['ns2out']['ns2bvi']['transactionId'];
	$order_price = $xml_response_to_array['ns2addSalesOrderResponseElement']['ns2out']['ns2bvi']['transactionPrice']['priceVAT'];

	$response_code = $xml_response_to_array['ns2addSalesOrderResponseElement']['ns2out']['responseCode'];
	$response_err_msg = $xml_response_to_array['ns2addSalesOrderResponseElement']['ns2out']['errorMsg'];

	if ($response_code == 100 or !empty($order_id)) {
		
		echo json_encode(array(
			"order_id" => $order_id,
			"order_price" => $order_price,
			"response_code" => $response_code
			));
			exit();
		
	} else {
		echo json_encode(array(
			"response_code" => $response_code,
			"erro_msg" => $response_err_msg
			));
			exit();
	}
	
	
}

}


?>
