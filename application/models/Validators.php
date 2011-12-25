<?php
	class Model_Validators
	{
		public static function alnum()
		{
			$alnum = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
			$alnum->setMessages(
				array(
					Zend_Validate_Alnum::NOT_ALNUM => "Input must be alphanumeric",
					Zend_Validate_Alnum::INVALID => "Input must be alphanumeric"
				)
			);
			
			return $alnum;
		}
		
		public static function alpha()
		{
			$alpha = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
			$alpha->setMessages(
				array(
					Zend_Validate_Alpha::NOT_ALPHA => "Input must contain only alphabets",
					Zend_Validate_Alpha::STRING_EMPTY => "Input cannot be empty",
					Zend_Validate_Alpha::INVALID => "Input must contain only alphabets"
				)
			);
			
			return $alpha;
		}
		
		public static function int()
		{
			$intValid = new Zend_Validate_Int();
			$intValid->setMessages(
				array(
					Zend_Validate_Int::NOT_INT => "Input must be an integer",
					Zend_Validate_Int::INVALID => "Input must be an integer"
				)
			);
			
			return $intValid;
		}
		
		public static function dateval()
		{
			$dateValid = new Zend_Validate_Date();
			$dateValid->setMessages(
				array(
					Zend_Validate_Date::INVALID_DATE => "Input must be a valid date",
					Zend_Validate_Date::INVALID => "Input must be a valid date",
					Zend_Validate_Date::FALSEFORMAT => "Date format should be YY-MM-DD"
				)
			);	
			
			return $dateValid;
		}
		
		public static function password()
		{
			$passwordString = new Zend_Validate_StringLength(6,60);
			$passwordString->setMessages(
								array(
									Zend_Validate_StringLength::TOO_SHORT => "Minimum of 6 characters required",
									Zend_Validate_StringLength::TOO_LONG => "Not more than 60 characters allowed"
								)
							);
							
			return $passwordString;	
		}
		
		public static function regex($str)
		{
			$regex = new Model_CustomRegex($str);
			
			$regex->setMessages(
					array(
						Model_CustomRegex::INVALID => "Illegal characters found",
						Model_CustomRegex::NOT_MATCH => "Illegal characters found",
						Model_CustomRegex::ERROROUS => "Illegal characters found"
					)
				);
			
				
			return $regex;
				
		}
		
		public static function email()
		{
			$email = new Zend_Validate_EmailAddress();
			$email->setMessages(
				array(
						 Zend_Validate_EmailAddress::INVALID            => "Invalid email address",
					     Zend_Validate_EmailAddress::INVALID_FORMAT     => "Invalid email address",
					     Zend_Validate_EmailAddress::INVALID_HOSTNAME   => "Invalid email address",
					     Zend_Validate_EmailAddress::INVALID_MX_RECORD  => "Invalid email address",
					     Zend_Validate_EmailAddress::INVALID_SEGMENT    => "Invalid email address",
					     Zend_Validate_EmailAddress::DOT_ATOM           => "Invalid email address",
					     Zend_Validate_EmailAddress::QUOTED_STRING      => "Invalid email address",
					     Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "Invalid email address",
					     Zend_Validate_EmailAddress::LENGTH_EXCEEDED  => "Invalid email address"
					  )
			);
			
			return $email;
		}
		
		public static function stringlength($ll,$ul)
		{
			$str = new Zend_Validate_StringLength($ll,$ul);
			$str->setMessages(
								array(
									Zend_Validate_StringLength::TOO_SHORT => "Minimum of $ll characters required",
									Zend_Validate_StringLength::TOO_LONG => "Not more than $ul characters allowed"
								)
							);
			return $str;
		}
		
	}