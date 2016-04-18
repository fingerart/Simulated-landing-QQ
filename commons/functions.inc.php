<?php
/**
 * File:functions.inc.php
 * Encoding:UTF-8
 * Language:PHP
 * FrameWork:Brophp1.0
 * ProjectName:QQ_Login Version1.0
 * ================================================
 * CopyRight 2014 FingerArt
 * Web: http://FingerArt.me
 * ================================================
 * Author: FingerArt
 * Date: 2014-1-15
 */
	//全局可以使用的通用函数声明在这个文件中.
	

	/**
	 * 获取Gravatar头像
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	function get_gravatar( $email, $img = true, $s = 80, $d = 'identicon', $r = 'g', $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
	
	
	
	/**
	 * randkeys 随机生成字符
	 * @param int $length		生成的位数
	 * @return string				生成的字符串
	 */
	function randkeys($length)
	{
		$str = 'aBcDe0FgH1iJk2LmN3oPq4Rs5TuV6wXy7ZAb8CdE9fGh0IjKlOmNoPqRsTuVwXyZ';//字符池
		$key = '';
		for($i=0;$i<$length;$i++) {
			$key .= $str{mt_rand(0,61)};    //生成php随机数
		}
		return $key;
	}
	
