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
 */

	class Index  {
		function index(){
			$this->display();
		}

		/**
		 * Ajax批量登陆QQ(账号+密码)
		 */
		function ajaxLoginQq() {
			$login_qq = new LoginQq($_POST['state']);
			$login_qq->login($_POST['num'], $_POST['sid']);
		}
		
		/**
		 * Ajax批量登陆QQ(账号+SID)
		 */
		function ajaxLoginQqSid() {
			$login_qq = new LoginQq();
			$login_qq->sidLogin($_POST['sid']);
		}
		
		/**
		 * Ajax验证码登陆QQ
		 */
		function ajaxLoginQq_key() {
			$url = $_POST['action'];
			$fields = '';
			$preg_success = '/ontimer="http:\/\/info\.3g\.qq\.com\/g\/s\?sid=(.*)&/U';
			unset($_POST['action']);
			foreach ($_POST as $key=>$value) {
				$fields .=$key.'='.$value.'&';
			}
			$content = LoginQq::curlFun($url, 1, true, $fields);
			switch (LoginQq::loginSate($content)) {
				case 'success':
					switch ($_POST['loginType']) {
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
					preg_match($preg_success, $content, $sid);
					if(isset($sid[1])&&!empty($sid[1]))
						$profile = LoginQq::qqProfile($sid[1]);
					$msg = '<span class="text-success">登陆成功!</span>状态:'.$type.' <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin='.$_POST['qq'].'&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:'.$_POST['qq'].':52" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>';
					if($_POST['isSid']==true)
						$sid = $_POST['qq'].'----'.$sid[1].'<br>';
					break;
				case 'pwd_err':
					$msg .= '<span class="text-danger">密码错误!</span>';
					break;
				case 'exception':
					$msg .= '<span class="text-danger">验证码错误!</span>';
					break;
				case 'login_err':
					$msg .= '该账号暂时无法登录, 点击<a href="http://aq.qq.com/cn2/login_limit/limit_detail_v2?account='.$_POST['qq'].'" target="_blank"> 这里 <i class="icon-external-link"> </i></a>恢复正常使用.';
					break;
				default :
					$msg .= '登陆异常: 帐号或密码不正确';
			}
			header("Content-Type:text/json");
			echo json_encode(array('msg'=>$msg, 'profile'=>$profile, 'sid'=>$sid));
		}
		
		function ajaxSidLoginQq_key() {
			$url = $_POST['action'];
			$fields = '';
			unset($_POST['action']);
			foreach ($_POST as $key=>$value) {
				$fields .=$key.'='.$value.'&';
			}
			$content = LoginQq::curlFun($url, 1, true, $fields);
			$loginQq = new LoginQq();
			$loginQq->sidLoginSate($content);
			$profile = LoginQq::qqProfile($_POST['3gqqsid']);
			header("Content-Type:text/json");
			echo json_encode(array('profile'=>$profile));
		}
	}