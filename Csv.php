<?php

namespace Frogg;

class Csv{

	static function export($list, $filename='data', $separator=',', $enclosure='"'){
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename.'.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		foreach ($list as $line) {
			fputcsv($output, $line, $separator, $enclosure);
		}
	}

	static function addVal(&$line, $value){
		$line[] = '="'.utf8_decode($value).'"';
	}

}