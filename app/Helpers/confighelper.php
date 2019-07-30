<?php

if(!function_exists('replace_dynamic_content'))
{
	function replace_dynamic_content($content, $replacements)
	{
		$display_content = preg_replace_callback('/\{\{([a-zA-Z0-9-_. ]+)\}\}/', function ($match)
			use ($replacements) {
				$ret = '';

				if($match[1] !== '') {
					$m = explode('.', $match[1]);
					$chkstr = '$findstr = isset($replacements';
					$getstr = '$retstr = $replacements';
					foreach($m as $m1) {
						$chkstr .= "['".$m1."']";
						$getstr .= "['".$m1."']";
					}
					$chkstr .= ') ? 1 : 0;';
					$getstr .= ';';
					eval($chkstr);

					if($findstr) {
						eval($getstr);
						$ret = $retstr;
					}
				}

				return $ret;
			//return (array_key_exists($match[1], $replacements)) ? $replacements[$match[1]] : $match[1];
		}, $content);

		return $display_content;
	}
}
