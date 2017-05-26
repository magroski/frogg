<?php

namespace Frogg\Sms;

class Tww{

	private $credentials;

	/**
	 * Constructor
	 * @param array $credentials TWW credentials in the following format ['TWW_USER'=>x,'TWW_PASS'=>y]
	 * @return -
	 */
	public function __construct(array $credentials){
		$this->credentials = ['NumUsu'=>$credentials['TWW_USER'], 'Senha'=>$credentials['TWW_PASS']];
	}

	/**
	 * Send a sms using Tww API
	 * @param array  $data['id', 'text', 'to'] Key-value array
	 *  * 'id'   - recipient unique identifier
	 *	* 'text' - message that will be sent
	 * 	* 'to'	 - number without country code,
	 * @return -
	 */
	public function send(array $data){
		$phone		= preg_replace("/[(,),\-,\s]/", "", $data['to']);
		$phone		= preg_replace('/^' . preg_quote('+55', '/') . '/', '', $phone);

		$text		= self::sanitizeText($data['text']);
		$url		= 'http://webservices.twwwireless.com.br/reluzcap/wsreluzcap.asmx/EnviaSMS';
		$queryData	= array_merge($this->credentials,['Celular'=>$phone,'Mensagem'=>$text]);

		if(isset($data['id'])){
			$queryData['SeuNum'] = $data['id'];
		} else {
			$queryData['SeuNum'] = 0;
		}

		$options = [
			'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($queryData),
			],
		];
		$context = stream_context_create($options);
		file_get_contents($url, false, $context);
	}

	/**
	 * Swap incompatible characters with compatible ones.
	 * Ex: Swaps [ã | à | á] with [a]
	 * @param string $text Text to be sanitized
	 * @return string Sanitized text.
	 */
	public static function sanitizeText(string $text){
		$unwanted_array = ['Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A',
				'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O',
				'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a',
				'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i',
				'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
				'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'];
		return strtr( $text, $unwanted_array );
	}
}