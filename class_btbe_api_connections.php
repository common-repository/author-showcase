<?php
class Btbe_Amazon
{
	protected $self = array();
	public function __construct($assoc, $access, $secret)
	{
		$this->self['assoc_tag'] = $assoc;
		$this->self['access'] = $access;
		$this->self['secret_key'] = $secret;
	}
	public function getReviews($asin)
	{
		$response_array = array();
    $parameters = array(
      'Operation' => 'ItemLookup',                          
      'ResponseGroup' => 'Reviews',
      'IdType' => 'ASIN',
      'ItemId' => $asin
    );
    $response_array = $this->makeAWSUrl($parameters);
		return (string)$response_array->Items->Item->CustomerReviews->IFrameURL;
	}
	private function makeAWSUrl($parameters)
	{
		$host = 'ecs.amazonaws.com';
		$path = '/onca/xml';
		$query = array(
			'Service' => 'AWSECommerceService',
			'AWSAccessKeyId' => $this->self['access'],
			'AssociateTag' => $this->self['assoc_tag'],
			'Timestamp' => gmdate('Y-m-d\TH:i:s\Z')
		);
		// Merge in any options that were passed in
		if (is_array($parameters))
		{
			$query = array_merge($query, $parameters);
		}
		// Do a case-insensitive, natural order sort on the array keys.
		ksort($query);
		// create the signable string
		$temp = array();
		foreach($query as $k => $v)
		{
			$temp[] = str_replace('%7E', '~', rawurlencode($k)) . '=' . str_replace('%7E', '~', rawurlencode($v));
		}
		$signable = implode('&', $temp);
		$stringToSign = "GET\n$host\n$path\n$signable";
		// Hash the AWS secret key and generate a signature for the request.
		$hex_str = hash_hmac('sha256', $stringToSign, $this->self['secret_key']);
		$raw = '';
		for ($i = 0; $i < strlen($hex_str); $i+= 2)
		{
			$raw.= chr(hexdec(substr($hex_str, $i, 2)));
		}
		$query['Signature'] = base64_encode($raw);
		ksort($query);
		$temp = array();
		foreach($query as $k => $v)
		{
			$temp[] = rawurlencode($k) . '=' . rawurlencode($v);
		}
		$final = implode('&', $temp);
		$url = 'https://' . $host . $path . '?' . $final;
		$ch = curl_init();
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch);
		curl_close($ch);
		$response = simplexml_load_string($output);
		return $response;
	}
}

function btbe_goodreads($isbn, $goodreads_access) {
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "https://www.goodreads.com/book/isbn?isbn=".$isbn."&key=".$goodreads_access); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$output = curl_exec($ch);
	curl_close($ch);  
	$initial_slice = explode('<reviews_widget>', $output);
	if(isset($initial_slice[1])) {
		$second_slice = explode('</reviews_widget>', $initial_slice[1]);
		$ripstylesout = strstr($second_slice[0], '</style>');
		$final = str_replace('</style>', '', $ripstylesout);
		$final = str_replace(']]>', '', $final);
		return $final;
	}
  return '';
}

?>