<?php
	class Model_Functions {
		
		protected $column;
		
		public static function generateRandom($size) 
	    {
	        $arr = '';
	        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	        for ($i = 0; $i < $size; $i++) {
	            $r = rand(0, 62);
	            $arr = $arr . $chars[$r];
	        }
	        return $arr;
	    }
		
		public function sendMail($type,$values)
		{
			$mcon = Zend_Registry::get('mailconfig');
			$config = array(
							'ssl' => $mcon['ssl'], 
							'port' => $mcon['port'], 
							'auth' => $mcon['auth'],
							'username' => $mcon['username'], 
							'password' => $mcon['password']
							);
							
			$tr = new Zend_Mail_Transport_Smtp($mcon['smtp'],$config);
			Zend_Mail::setDefaultTransport($tr);
			
			if($type == "resetpassword")
			{
				$mailbody = "<div style='width: 100%; '><div style='border-bottom: solid 1px #aaa; 
				             margin-bottom: 10px;'>";
	            $mailbody = $mailbody . "<a href='http://www.hiveusers.com' style='text-decoration: none;'>
	                                     <span style='font-size: 34px; color: #2e4e68;'><b>hive</b></span>";
	                                     
	            $mailbody = $mailbody . "<span style='font-size: 26px; color: #83ac52; text-decoration:none;'>
	                                     <b>users.com</b></span></a><br/><br/>Password Reset</div>";
										 
	            $mailbody = $mailbody . "<div style='margin-bottom:10px;'><span style='color: #000;'>
	                                     <i>Hello</i>,<br/><br/>Your Password has been Reset to <b>" . 
	                                     $values['rpassword'] ."</b></span><br><span>After logging in change your 
	                                     password <a href = 'http://www.hiveusers.com/userprofile/changepassword'>
	                                     here</a></span></div>";
	                                     
	            $mailbody = $mailbody . "<div style='border-top: solid 1px #aaa; color:#aaa; padding: 5px;'>
	                                    <center>This is a generated mail, please do not Reply.</center></div></div>";
			}
			$mail = new Zend_Mail();
	        $mail->setBodyHtml($mailbody);
	        $mail->setFrom($mcon['fromadd'], $mcon['fromname']);
			$mail->addTo($values['email'], $values['firstName']);
			$mail->setSubject($values['subject']);
        	$mail->send();
		}
		
		public function pathFormat($path)
		{
			$i = 0;
			$formattedPath = "";
			for($i=0;$i<strlen($path);$i++)
			{
				if(ord(substr($path,$i,1)) == 92)
				{
					$formattedPath = $formattedPath . chr(47);
				}
				else {
					$formattedPath = $formattedPath . substr($path,$i,1);
				}
			}
			return $formattedPath;
		}
		
		public function getFileExt($filepath)
		{
			$i=0;
			for($i=0;$i<strlen($filepath);$i++)
			{
				if(substr($filepath,$i,1) == '/')
				{
					$pos = $i;
				}
			}
			$filename = substr($filepath,$pos+1,strlen($filepath)-$pos);
			$i=0;
			for($i=0;$i<strlen($filename);$i++)
			{
				if(substr($filename,$i,1) == '.')
				{
					$pos = $i;
				}
			}
			$ext = substr($filename,$pos+1,strlen($filename)-$pos); 
			return $ext;
			
		}
		
		public static function highlightResults($query,$text)
		{
			
			$keywords = explode(" ",$query);
			if(count($keywords) == 0)
			{
				$keywords[0] = $query;
			}
			$words = explode(" ",$text);
			for($i=0;$i<count($keywords);$i++)
			{
				for($j=0;$j<count($words);$j++)
				{
					if(strtolower($keywords[$i]) == strtolower($words[$j])
					|| Model_Functions::strIsPartOf(strtolower($keywords[$i]),strtolower($words[$j]))
					)
					{
						$words[$j] = "<span class = 'search-highlighter' style = 'font-weight: bold;color: #1A4C80;background-color : #c1e199;'>" . $words[$j] . "</span>";
					}
				}
			}
			$output = implode(" ",$words);
			return $output;
			
		}
		
		public static function highlightEOH($from,$to,$eoh)
		{
			if((int)$eoh >= $from && (int)$eoh <= $to)
			{
				$eoh = "<span class = 'search-highlighter' style = 'font-weight: bold;color: #1A4C80;background-color : #c1e199;'>" . $eoh . "</span>";
			}
			return $eoh;
		}
		
		public static function strIsPartOf($str1,$str2)
		{
			
			if(strlen($str1) > strlen($str2))
			{
				$max = strlen($str1);
				$maxstr = $str1;
				$min = strlen($str2);
				$minstr = $str2;
			}
			else {
				$max = strlen($str2);
				$maxstr = $str2;
				$min = strlen($str1);
				$minstr = $str1;
			}
			if ($min <= 4)
			{
				return false;
			}
			for($i=0;$i<$max;$i++)
			{
				if(strtolower($minstr) == substr($maxstr,$i,$min))
				{
					return true;
				}
			}
			return false;
		}
	}