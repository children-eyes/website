<?php

class JQuery{
	protected static $m_jQueryReadyString = '';

	public static function ready($js = null){
		if($js === null){
			if(!self::$m_jQueryReadyString)
				return '';

			return "\n".self::inline('jQuery(document).ready(function ($){ '." \n".self::$m_jQueryReadyString." });\n");
		}

		self::$m_jQueryReadyString .= $js."\n";
	}

	public static function script($file){
		return "<script type=\"text/javascript\" src=\"$file\" ></script>";
	}

	public static function inline($str){
		return '<script language="javascript" type="text/javascript"><!--'."\n".$str." \n--></script>";
	}

	public static function write($str){
		return self::inline("document.write('".addcslashes($str, "'")."')");
	}

	public static function location($url){
		return self::inline("location.href=\"$url\"");
	}
}
