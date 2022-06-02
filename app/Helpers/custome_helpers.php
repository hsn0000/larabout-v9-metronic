<?php

/*
|--------------------------------------------------------------------------
| Custom Helpers made by self
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| List of Country
|--------------------------------------------------------------------------
*/
if ( ! function_exists('country_list'))
{
	function country_list($selected = '')
	{
		$_country_list = config('country.list');

		if(isset($selected) && is_array($selected))
		{
			foreach($selected as $key => $val)
			{
				if($_country_list[$val])
				{
					$_countries[$val] = $_country_list[$val];
				}
			}

			return $_countries;
		}

		return $_country_list;
	}
}

/*
|--------------------------------------------------------------------------
| Convert timezone
|--------------------------------------------------------------------------
*/
if ( ! function_exists('convert_tz'))
{
	function convert_tz($original = '', $format = 'Y-m-d H:i:s', $tz = 'UTC')
	{
        $original = $original?:date('Y-m-d H:i:s');
        $format = $format == 'timezone' ? 'Y-m-d\TH:i:sP' : $format;
		$tz = session('timezone') && $tz == 'UTC' ? session('timezone') : $tz;

		$date = new DateTime(date('Y-m-d\TH:i:sP', strtotime($original)));
		$date->setTimezone(new DateTimeZone($tz));

		return $date->format($format);
	}
}

/*
|--------------------------------------------------------------------------
| Floor with number format
|--------------------------------------------------------------------------
*/
if ( ! function_exists('number_floor'))
{
	function number_floor($number = '')
	{
		return floor($number * 100) / 100;
	}
}

/*
|--------------------------------------------------------------------------
| Convert timezone
|--------------------------------------------------------------------------
*/
if ( ! function_exists('convert_value'))
{
    function convert_value($value)
    {
        $k = pow(10,3);
        $mil = pow(10,6);
        $bil = pow(10,9);

        $format = '';
        $count_player =  $value;
        if ($value >= $bil){
            $count_player =  ($value / $bil);
            $format = "B";
        }
        else if ($value >= $mil){
            $count_player =  ($value / $mil);
            $format = "M";
        }
        else if ($value >= $k){
            $count_player =  ($value / $k);
            $format = "K";
        }

        return number_format( $count_player, strlen($count_player)<=3?0:2, '.', ''). $format;
    }
}

/*
|--------------------------------------------------------------------------
| Array merge for Attendance Data
|--------------------------------------------------------------------------
*/
if( ! function_exists('array_merge_attd'))
{
    function array_merge_attd(array &$array1, array &$array2)
    {
        $merged = $array1;
        $attendance = $array2;
        foreach ($attendance as $key => $value)
        {
            $a = array_search($value['date'], array_column($merged, 'date'));
            $prev_day = date('Y-m-d', strtotime($value['date'].' -1 days'));
            $ap = array_search($prev_day, array_column($attendance, 'date'));
            $p = array_search($prev_day, array_column($merged, 'date'));
            $next_day = date('Y-m-d', strtotime($value['date'].' +1 days'));
            $an = array_search($next_day, array_column($attendance, 'date'));
            $n = array_search($next_day, array_column($merged, 'date'));

            if($a >= 0)
            {
                if(!is_numeric($ap) && $p)
                {
                    $attd[] = $merged[$p];
                }
                $attd[] = $value;
                if(!is_numeric($an) && $n)
                {
                    $attd[] = $merged[$n];
                }
            }
            else
            {
                $attd[] = $merged[$n];
            }
        }

        $attd = array_map("unserialize", array_unique(array_map("serialize", $attd)));
        /**
         * Sorting ID
         */
        usort($attd,function($a,$b){
            return strtotime($a['date']) - strtotime($b['date']);
        });
        foreach($attd as $k => $v)
        {
            $_arr[] = [
                'id' => ++$k
            ];
        }
        // dd($aaa,$attd,$merged);

        $attd = array_replace_recursive($attd, $_arr);
        return $attd;
    }
}

/*
|--------------------------------------------------------------------------
| Date format
|--------------------------------------------------------------------------
*/
if( ! function_exists('format_date'))
{
    function format_date($date = '', $format = 'default')
    {
        $date = $date?:date('Y-m-d H:i:s');
        $date = convert_tz($date);

        switch($format)
        {
            case 'date':
                $format = date('M d, Y', strtotime($date));
            break;
            case 'time':
                $format = date('H:i', strtotime($date));
            break;
            case 'fulldate':
                $format = date('D, M d, Y', strtotime($date));
            break;
            case 'fulldatetime':
                $format = date('D, M d, Y - H:i:s', strtotime($date));
            break;
            case 'fulldt_timezone':
                $format = date('D, M d, Y - H:i:s P', strtotime($date));
            break;
            case 'timezone':
                $format = date('T', strtotime($date));
            break;
            case 'datetime':
                $format = date('M d, Y - H:i', strtotime($date));
            break;
            case 'general_date':
                $format = date('Y-m-d', strtotime($date));
            break;
            case 'general':
                $format = date('Y-m-d H:i:s', strtotime($date));
            break;
            case 'timestamp':
                $format = strtotime($date);
            break;
            default:
                $format = date('D, M d, Y - H:i', strtotime($date));
        }

        return $format;
    }
}

/*
|--------------------------------------------------------------------------
| Date different interval
|--------------------------------------------------------------------------
*/
if( ! function_exists('date_interval'))
{
    function date_interval($start, $end)
    {
        $date1 = new DateTime($start);
        $date2 = new DateTime($end);
        return $date1->diff($date2);
    }
}

/*
|--------------------------------------------------------------------------
| Custom Number Format
|--------------------------------------------------------------------------
*/
if( ! function_exists('custom_numfor'))
{
    function custom_numfor($n, $precision = 0, $ds = ',', $ts = '.')
    {
        if ($n < 1000) {
            // Anything less than a million
            $n_format = number_format($n, $precision, $ds, $ts);
        } else if ($n < 1000000) {
                // Anything less than a million
                $n_format = number_format($n / 1000, $precision, $ds, $ts) . 'k';
        } else if ($n < 1000000000) {
            // Anything less than a billion
            $n_format = number_format($n / 1000000, $precision, $ds, $ts) . 'M';
        } else {
            // At least a billion
            $n_format = number_format($n / 1000000000, $precision, $ds, $ts) . 'B';
        }

        return $n_format;
    }
}

/*
|--------------------------------------------------------------------------
| Get country by IP
|--------------------------------------------------------------------------
*/
if ( ! function_exists('get_country_by_ip'))
{
	function get_country_by_ip($ip_address = '')
	{
		$ipdata = json_decode(file_get_contents('http://ip-api.com/json/'.$ip_address));

		if(is_object($ipdata))
		{
			return $ipdata;
		}
		else
		{
			return (object) ['timezone' => NULL];
		}
	}
}

/*
|--------------------------------------------------------------------------
| Print for required fields
|--------------------------------------------------------------------------
*/
if( ! function_exists('required_field'))
{
	function required_field($message)
	{
		return '<div class="invalid-feedback">'.$message.'</div>';
	}
}

/*
|--------------------------------------------------------------------------
| Print out variable
|--------------------------------------------------------------------------
*/
if ( ! function_exists('debug'))
{
   function debug($str = '')
   {
		array_map(function($x) {
			echo '<pre>';
			print_r($x);
			echo '</pre>';
		}, func_get_args());
   }
}

/*
|--------------------------------------------------------------------------
| Simple Number Format
|--------------------------------------------------------------------------
*/
if ( ! function_exists('is_number'))
{
   function is_number(Float $number, $decimals = 0, $points = '.', $thousands_sep = ',')
   {
       return number_format($number, $decimals, $points, $thousands_sep);
   }
}

/*
|--------------------------------------------------------------------------
| Convert expires of access token to date
|--------------------------------------------------------------------------
*/
if ( ! function_exists('expires_token'))
{
   function expires_token(Int $expires)
   {
       return date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', $expires) ? TRUE : FALSE;
   }
}

/*
|--------------------------------------------------------------------------
| Push to Multidimensional Array
|--------------------------------------------------------------------------
*/
if(!function_exists('array_push_multidimension'))
{
	function array_push_multidimension($array_data = array(), $array_push = array(), $position = 'last')
	{
		$array = array();
		if(is_array($array_data))
		{
			if($position == 'last')
			{
				$position_key = @end(array_keys($array_data));
			}
			else
			{
				$position_key = $position;
			}

			foreach($array_data as $key => $val)
			{
				if($position != 'first'){$array[$key] = $val;}

				if($key == $position_key || $position == 'first')
				{
					foreach($array_push as $push_key => $push_val)
					{
						if(is_numeric($push_key) && $key == 0)
						{
							++$push_key;
						}

						$array[$push_key] = $push_val;
						if(is_array($push_key))
						{
							return array_push_multidimension($array, $push_key, $position);
						}
					}
				}

				if($position == 'first'){$array[$key] = $val;}
			}
		}
		else
		{
			foreach($array_push as $push_key => $push_val)
			{
				$array[$push_key] = $push_val;
				if(is_array($push_key))
				{
					return array_push_multidimension($array, $push_key);
				}
			}
		}

		return ($array);
	}
}


/*
|--------------------------------------------------------------------------
| Random String from CodeIgniter
|--------------------------------------------------------------------------
*/
if ( ! function_exists('random_string'))
{
	/**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	int	number of characters
	 * @return	string
	 */
	function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'basic':
				return mt_rand();
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'alpha':
				switch ($type)
				{
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case 'unique': // todo: remove in 3.1+
			case 'md5':
				return md5(uniqid(mt_rand()));
			case 'encrypt': // todo: remove in 3.1+
			case 'sha1':
				return sha1(uniqid(mt_rand(), TRUE));
		}
	}
}



/*
|--------------------------------------------------------------------------
| Get Browser Details
|--------------------------------------------------------------------------
*/
if ( ! function_exists('getBrowser'))
{
	function getBrowser()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }

	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/OPR/i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }
	    else
	    {
	        $bname = 'Other';
	        $ub = "Other";
	    }

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }

	    // see how many we have
		$i = count($matches['browser']);

		if(is_array($matches['version']) && count($matches['version']) > 0)
		{
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}
			else {
				$version= $matches['version'][0];
			}
		}

	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}

	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern,
	        'alias' 	=> strtolower(str_replace(' ', '-', $ub).'-'.$version.'-'.$platform)
	    );
	}
}

if( ! function_exists('id_transaction'))
{
    /**
     * @param int $number
     * @return string
     */
    function id_transaction(int $number)
    {
        return sprintf('%011d', $number);
    }
}

if( ! function_exists('uid'))
{
    /**
     * @param int $number
     * @return string
     */
    function uid(int $number)
    {
        return sprintf('%06d', $number);
    }
}

if ( ! function_exists('active'))
{
    function active($enum = 'y')
    {
        if($enum == 'y')
        {
            $temp = '<span class="publish text-success"><i class="flaticon2-check-mark text-success" style="font-size: 0.8rem;"></i> Yes</span>';
        }
        else if($enum == 'n')
        {
            $temp = '<span class="publish text-danger"><i class="flaticon2-cancel text-danger" style="font-size: 0.8rem;"></i> No</span>';
        }
        else
        {
            $temp = '<i class="flaticon-more-1" style="font-size: 0.8rem;"></i> Unknown';
        }

        return $temp;
    }
}

if ( ! function_exists('thumb_img'))
{
    function thumb_img($img)
    {
        $exp = explode('/',$img);
		$start = $exp[0].'//'.$exp[1].''.$exp[2];
        $end = end($exp);

		$rfls_str = str_replace($start.'/','',$img);
		$file_thmb = str_replace($end,'',$rfls_str).'thumbs/'.$end;
        $str_img = str_replace($end,'',$img);

		if(file_exists($file_thmb)){
			$retun = $str_img.'thumbs/'.$end;
		} else {
			$retun = $img;
		}

        return $retun;
    }
}