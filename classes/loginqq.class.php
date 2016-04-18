<?php
/**
 * File:index.class.php
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
 * 登陆封装类：登陆、提取sid
 *
 */
class LoginQq {
	private $login_url = 'http://pt.3g.qq.com/s?aid=nLogin3gqq';
	private $sidtype = 1;
	private $nopre = 0;
	private $q_from = '';
	private $loginTitle = '手机腾讯网';
	private $bid = 0;
	private $go_url = '';
	private $qq = '';
	private $pwd = '';
	private $loginType = '';
	private $loginsubmit = '登陆';
	private $post_url = 'http://pt.3g.qq.com/handleLogin';
	private $preg_success = '/ontimer="http:\/\/info\.3g\.qq\.com\/g\/s\?sid=(.*)&/U';
	private $preg_pwd_err = '/您输入的帐号或密码不正确/U';
	private $preg_exception = '/系统检测到您的操作异常/U';
	private $sid = null;
	private $msg = '';
	private $cookie_file ='';
	function __construct($type = 1) {
		$this->loginType = $type;
	}
	public function login($qqText, $tSid = false) {
		foreach (explode("\r\n", $qqText) as $value) {
			if(empty($value))
				continue;
			list($qq, $pwd) = explode('----', $value);
			$this->qq = $qq;
			$this->pwd = $pwd;
			$fields = 'login_url='.$this->login_url.'&sidtype='.$this->sidtype.'&nopre='.$this->nopre.'&q_from='.$this->q_from.'&loginTitle='.$this->loginTitle.'&bid='.$this->bid.'&go_url='.$this->go_url.'&qq='.$this->qq.'&pwd='.$this->pwd.'&loginType='.$this->loginType.'&loginsubmit='.$this->loginsubmit;
			$content = $this->curlFun($this->post_url, 1, true, $fields);
			preg_match($this->preg_success, $content, $sid);
			if(isset($sid[1])&&!empty($sid[1])) {
				$profile = LoginQq::qqProfile($sid[1]);
				$dir = 'bigface';
			}else {
				$profile['head_num'] = 1;
				$profile['name'] = '昵称';
				$profile['lv'] = 1;
				$profile['update'] = 0;
				$dir = 'bigoffline';
			}
			$this->msg .= '<div class="border-diy"><img style="width:32px;" id="'.$this->qq.'head" src="http://221.233.41.38/images/face/'.$dir.'/'.$profile['head_num'].'.gif" class="img-polaroid"><div style="display:inline-block;vertical-align:top;"><span id="'.$this->qq.'name">'.$profile['name'].'</span>(<small class="muted">'.$this->qq.'</small>)<br><img id="'.$this->qq.'lv" style="vertical-align:middle;" src="http://3gimg.qq.com/3gqq/lv/'.$profile['lv'].'.png"> 还差<span id="'.$this->qq.'update" class="label label-info">'.$profile['update'].'</span>天升级</div><div id="'.$this->qq.'">';
			switch (LoginQq::loginSate($content)) {
				case 'success':
					switch ($this->loginType) {
						case 1:
							$type = '在线';
							break;
						case 2:
							$type = '隐身';
							break;
						case 3:
							$type = '未登录QQ';
							break;
					}
					$this->msg .= '<span class="text-success">登陆成功!</span>状态:'.$type.' <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$this->qq.'&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:'.$this->qq.':52" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>';
					if($tSid)
						$this->sid .=$this->qq.'----'.$sid[1].'<br>';
					break;
				case 'pwd_err':
					$this->msg .= '<span class="text-danger">密码错误!</span>';
					break;
				case 'exception':
					preg_match('/<img src="(.*)" alt="验证码"\/>/U', $content, $img);
					preg_match('/<anchor>马上登录\n<go href="(.*)" method="post">\n\n<postfield name="qq" value="(.*)"\/>\n<postfield name="u_token" value="(.*)"\/>\n\n<postfield name="hexpwd" value="(.*)"\/>\n<postfield name="sid" value="(.*)"\/>\n<postfield name="hexp" value="(.*)"\/>\n<postfield name="auto" value="(.*)"\/>\n<postfield name="loginTitle" value="(.*)"\/>\n<postfield name="q_from" value="(.*)"\/>\n<postfield name="modifySKey" value="(.*)"\/>\n<postfield name="q_status" value="(.*)"\/>\n<postfield name="r" value="(.*)"\/>\n<postfield name="loginType" value="(.*)"\/>\n\n<postfield  name="bid_code" value="(.*)"\/>\n\n<postfield name="imgType" value="(.*)"\/>\n<postfield name="extend" value="(.*)"\/>\n<postfield name="r_sid" value="(.*)"\/>\n<postfield name="bid" value="(.*)"\/>\n<postfield name="login_url" value="(.*)"\/>\n\n<postfield name="rip" value="(.*)"\/>\n\n<postfield name="verify" value="(.*)"\/>\n<\/go><\/anchor>/U', $content, $form);
					$this->msg .= '
					<div class="input-prepend input-append" style="padding:5px 0 0 2px;">
						<img  src="'.$img[1].'" class="add-on" style="padding:0px;height:28px;">
						<form action=" method="post" name="'.$form[2].'" style="display:inline-block;margin:0px;">
							<input type="hidden" name="action" value="'.$form[1].'"/>
							<input type="hidden" name="qq" value="'.$form[2].'"/>
							<input type="hidden" name="u_token" value="'.$form[3].'"/>
							<input type="hidden" name="hexpwd" value="'.$form[4].'"/>
							<input type="hidden" name="sid" value="'.$form[5].'"/>
							<input type="hidden" name="hexp" value="'.$form[6].'"/>
							<input type="hidden" name="nopre" value="'.$form[7].'"/>
							<input type="hidden" name="auto" value="'.$form[8].'"/>
							<input type="hidden" name="loginTitle" value="'.$form[9].'"/>
							<input type="hidden" name="q_from" value="'.$form[10].'"/>
							<input type="hidden" name="modifySKey" value="'.$form[11].'"/>
							<input type="hidden" name="q_status" value="'.$form[12].'"/>
							<input type="hidden" name="r" value="'.$form[13].'"/>
							<input type="hidden" name="loginType" value="'.$this->loginType.'"/>
							<input type="hidden" name="login_url" value="http://pt.3g.qq.com/s?aid=nLogin3gqq"/>
							<input type="hidden" name="extend" value="'.$form[16].'"/>
							<input type="hidden" name="r_sid" value="'.$form[17].'"/>
							<input type="hidden" name="bid_code" value="'.$form[18].'"/>
							<input type="hidden" name="bid" value="'.$form[19].'"/>
							<input type="hidden" name="rip" value="106.82.231.92"/>
							<input name="verify" class="form-control input-mini"  type="text" maxlength="18" value="" />
						</form>
						<a class="btn add-on" name="submit" id="'.$form[2].'" role="button">提交</a>
					</div>';
					break;
					default:
						$this->msg .= 'error';
			}
			$this->msg .='</div></div>';
		}
		header("Content-Type:text/json");
		echo json_encode(array('msg'=>$this->msg, 'sid'=>$this->sid));
	}
	
	/**
	 * 账号登陆状态
	 * @param unknown_type $content
	 * @return string
	 */
	public static function loginSate($content) {
		$preg_success = '/ontimer="http:\/\/info\.3g\.qq\.com\/g\/s\?sid=(.*)&/U';
		$preg_pwd_err = '/您输入的帐号或密码不正确/U';
		$preg_exception = '/系统检测到您的操作异常/U';
		$preg_login_err = '/您的帐号暂时无法登录/';
		if(preg_match($preg_success, $content, $sid))
			return 'success';
		elseif (preg_match($preg_pwd_err, $content))
			return 'pwd_err';
		elseif (preg_match($preg_exception, $content))
			return 'exception';
		elseif (preg_match($preg_login_err, $content))
			return 'login_err';
	}
	
	/**
	 * SID登陆状态
	 * @param unknown_type $content
	 * @return string
	 */
	public function sidLoginSate($content) {
		if(preg_match('/手动刷新/U', $content))
			return 'success';
		elseif (preg_match('/The URL has moved/U', $content)) {
			LoginQq::borwser('http://q16.3g.qq.com/g/s?aid=nqqchatMain&sid='.$this->sid.'&myqq='.$this->qq);
			return 'success';
		}elseif (preg_match('/为了保护您的账户安全，有时，即使您已经登录，我们仍会请您输入验证码进行验证。/U', $content))
			return 'exception';
		elseif (preg_match('/sid已经过期/U', $content))
			return 'sid_expire';
		elseif(preg_match('/登录失败，可能系统繁忙，请稍后重试/U', $content))
			return 'login_lose';
	} 

	function sidLogin($qqText) {
		foreach (explode("\r\n", $qqText) as $value) {
			if(empty($value))
				continue;
			list($qq, $sid) = explode('----', $value);
			$this->qq = $qq;
			$this->sid = $sid;
			$ch = curl_init('http://pt.3g.qq.com/s?aid=nLogin3gqqbysid&3gqqsid='.$this->sid);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36");
			$content = curl_exec($ch);
			curl_close($ch);
			$key = $this->sidLoginSate($content);
			$profile = LoginQq::qqProfile($this->sid);
			$dir = 'bigface';
			if ($profile['head_num']==null) {
				$profile['head_num'] = 1;
				$profile['name'] = '昵称';
				$profile['lv'] = 1;
				$profile['update'] = 0;
				$dir = 'bigoffline';
			}
			$this->msg .= '<div class="border-diy">
					<img id="'.$this->qq.'head" src="http://221.233.41.38/images/face/'.$dir.'/'.$profile['head_num'].'.gif" class="img-polaroid">
							<div style="display:inline-block;vertical-align:top;"><span id="'.$this->qq.'name">'.$profile['name'].'</span>(<small class="muted">'.$this->qq.'</small>)<br><img id="'.$this->qq.'lv" style="vertical-align:middle;" src="http://3gimg.qq.com/3gqq/lv/'.$profile['lv'].'.png"> 还差<span id="'.$this->qq.'update" class="label label-info">'.$profile['update'].'</span>天升级</div>
									<div id="'.$this->qq.'">';
			switch ($key) {
				case 'success':
					preg_match('/aid=nqqStatus">(.*)<\/a>/U', $content,$type);
					$this->msg .= '<span class="text-success">登陆成功!</span>状态:'.$type[1].' <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$this->qq.'&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:'.$this->qq.':52" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>';
					break;
				case 'exception':
					preg_match('/<img src="(.*)" alt="验证码"\/>/U', $content, $img);
					preg_match('/<FORM action="(.*)" method="post" id="(.*)"><INPUT type="hidden" name="qq" value="(.*)"><INPUT type="hidden" name="imgType" value="\$imgType"><INPUT type="hidden" name="sid" value="(.*)"><INPUT type="hidden" name="method" value=""><INPUT type="hidden" name="extend" value="(.*)"><INPUT type="hidden" name="r_sid" value="(.*)"><INPUT type="hidden" name="3gqqsid" value="(.*)"><INPUT type="hidden" name="verify" value="\$verify"><INPUT name="i_p_w" type="hidden" value="imgType\|verify\|">/U', $content, $form);
					$this->msg .= '
					<div class="input-prepend input-append" style="padding:5px 0 0 2px;">
						<img  src="'.$img[1].'" class="add-on" style="padding:0px;height:28px;">
						<form method="post" name="'.$form[3].'" style="display:inline-block;margin:0px;">
							<input type="hidden" name="action" value="'.$form[1].'">
							<input type="hidden" name="qq" value="'.$form[3].'">
							<input type="hidden" name="imgType" value="gif">
							<input type="hidden" name="sid" value="'.$form[4].'">
							<input type="hidden" name="method" value="">
							<input type="hidden" name="extend" value="'.$form[5].'">
							<input type="hidden" name="r_sid" value="'.$form[6].'">
							<input type="hidden" name="3gqqsid" value="'.$form[7].'">
							<input name="i_p_w" type="hidden" value="imgType|verify|">
							<input class="form-control input-mini" name="verify" type="text">
						</form>
						<a class="btn add-on" name="submitSid" id="'.$form[3].'" role="button">提交</a>
					</div>';
					break;
				case 'sid_expire':
					$this->msg .= 'SID过期.';
				case 'login_lose':
					$this->msg .='登录失败, 建议重提SID.';
			}
			$this->msg.='</div></div>';
		}
		echo $this->msg;
	}
	
	/**
	 * CURL 模拟登陆函数
	 * @param unknown_type $url
	 * @param unknown_type $returnTransfer
	 * @param unknown_type $fields
	 * @return mixed
	 */
	static function curlFun($url, $returnTransfer=0, $isPost=true, $fields, $cookie=null) {
		// 			$cookie =  tempnam('./temp', 'cookie');
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
		if ($isPost) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		}
		if ($cookie!=null)
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	
	static function qqProfile($sid) {
		$url = 'http://q16.3g.qq.com/g/s?sid='.$sid.'&aid=nqqSelf';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36");
		$content = curl_exec($ch);
		curl_close($ch);
		$preg_heade_img = '/baseinfo-face"><img alt=\'\.\' src="http:\/\/221\.233\.41\.38\/images\/face\/bigface\/(.*)\.gif"/U';
		preg_match($preg_heade_img, $content, $heade_num);
		$profile['head_num'] = $heade_num[1];
		$preg_name = '/<strong>昵称：<\/strong>\r\n(.*)&nbsp;/U';
		preg_match($preg_name, $content, $name);
		$profile['name'] = $name[1];
		$preg_lv = '/<strong>等级：<\/strong>(.*)&nbsp;/U';
		preg_match($preg_lv, $content, $lv);
		$profile['lv'] = $lv[1];
		$preg_update = '/<strong>还有<\/strong>(.*)天升级/U';
		preg_match($preg_update, $content, $update);
		$profile['update'] = $update[1];
		return $profile;
	}
	
	
	/**
	 * 模拟浏览器访问
	 * @param unknown_type $url
	 * @return mixed
	 */
	public static function borwser($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36");
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	
}