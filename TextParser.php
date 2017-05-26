<?php

namespace Frogg;

class TextParser{

	/**
	 * This function is used to set the max length of a string without cutting the words
	 * @param string $str The string you want to shorten
	 * @param int 	 $len the string maximum length
	 */
	static function maxLengthByWords($str, $len = 50){
		$str = self::removeHTMLSpecialChars($str);
		$cut = "\x1\x2\x3";
		$str = strip_tags($str);
		list($str) = explode($cut, wordwrap($str, $len, $cut));
		return $str;
	}


	static function removeAccents($string) {
	    if ( !preg_match('/[\x80-\xff]/', $string) )
	        return $string;

	    $chars = array(
	    // Decompositions for Latin-1 Supplement
	    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
	    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
	    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
	    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
	    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
	    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
	    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
	    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
	    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
	    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
	    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
	    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
	    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
	    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
	    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
	    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
	    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
	    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
	    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
	    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
	    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
	    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
	    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
	    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
	    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
	    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
	    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
	    chr(195).chr(191) => 'y',
	    // Decompositions for Latin Extended-A
	    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
	    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
	    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
	    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
	    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
	    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
	    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
	    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
	    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
	    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
	    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
	    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
	    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
	    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
	    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
	    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
	    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
	    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
	    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
	    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
	    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
	    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
	    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
	    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
	    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
	    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
	    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
	    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
	    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
	    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
	    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
	    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
	    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
	    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
	    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
	    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
	    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
	    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
	    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
	    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
	    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
	    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
	    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
	    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
	    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
	    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
	    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
	    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
	    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
	    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
	    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
	    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
	    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
	    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
	    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
	    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
	    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
	    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
	    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
	    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
	    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
	    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
	    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
	    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
	    );

	    $string = strtr($string, $chars);

	    return $string;
	}

	static function email($email){
		$normalizeChars = array(
			'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
			'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
			'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
			'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',

			'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
			'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
			'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
			'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y',

			'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y'
		);
		return strtr($email, $normalizeChars);
	}

	static function normalize($email){
		$normalizeChars = array(
			'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
			'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
			'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
			'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',

			'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
			'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
			'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
			'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y',

			'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y'
		);
		return strtr($email, $normalizeChars);
	}

	static function toUpper($string){
		return strtr(strtoupper($string), array(
				"à" => "À",
				"â" => "Â",
				"á" => "Á",
				'ã' => 'Ã',
				'å' => 'Å',
				'ä' => 'Ä',
				"è" => "È",
				"é" => "É",
				"ê" => "Ê",
				'ë' => 'Ë',
				"ì" => "Ì",
				"î" => "Î",
				"í" => "Í",
				'ï' => 'Ï',
				"ò" => "Ò",
				"ô" => "Ô",
				"ó" => "Ó",
				'õ' => 'Õ',
				'ö' => 'Ö',
				"ú" => "Ú",
				"ù" => "Ù",
				"û" => "Û",
				'ü' => 'Ü',
				'ý' => 'Ý',
				"ç" => "Ç",
			)
		);
	}

	static function strip_urls($str){
		return preg_replace('|https?://(www\.)?[a-z\.0-9\\\\\\/?=&\-_\+]+|i', '', $str);
	}

	static function prepareTags($str, $max = 20){
		$str = self::removeHTMLSpecialChars($str);
		$str = strip_tags($str);

		$quotes = array(
			"\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
			"\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
			"\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
			"\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
			"\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
			"\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
			"\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
			"\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
			"\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
			"\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
			"\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
			"\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
		);
		$str = strtr($str, $quotes);

		$a = array('/ +/'					=>' ',
			   '/[^ÂÀÁÄÃâãàáäÊÈÉËêèéëÎÍÌÏîíìïÔÕÒÓÖôõòóöÛÙÚÜûúùüÇça-zA-Z0-9\\-, ]/' 		=>'',
			   '/-+/'						=>'-',
			   '/“/'						=>'"',
			   '/”/'						=>'"',
			   '/, /'						=>',',
			   '/ ,/'						=>',',
			   '/,+/'						=>',',
			   '/^,/'						=>'',
			   '/,$/'						=>''
		);

		$tags = explode(',', preg_replace(array_keys($a), array_values($a), $str));

		return array_unique($tags);
	}

	static public function truncate($text, $length = 150, $ending = '...', $exact = false, $considerHtml = false){
		if ($considerHtml) {
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}

			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {
				if (!empty($line_matchings[1])) {
					if (preg_match('/^<(s*.+?/s*|s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(s.+?)?)>$/is', $line_matchings[1])) {
					} else if (preg_match('/^<s*/([^s]+?)s*>$/s', $line_matchings[1], $tag_matchings)) {
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					} else if (preg_match('/^<s*([^s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					$truncate .= $line_matchings[1];
				}
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
					$left = $length - $total_length;
					$entities_length = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				if($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}

		if (!$exact) {
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;

		if($considerHtml) {
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
	}

	static function removeHTMLSpecialChars($str){
		return preg_replace("/&#?[a-z0-9]+;/i","",$str);
	}

	static function prepareHeadline($headline){

		$a = array('/ +/'						=>' ',
				   '/\?+/'						=>'?',
				   '/\.+/'						=>'.',
				   '/-+/'						=>'-',
				   '/!+/'						=>'!'
				   );

		return strip_tags(htmlspecialchars(trim(preg_replace(array_keys($a), array_values($a), $headline), " \r\n")));
	}

}