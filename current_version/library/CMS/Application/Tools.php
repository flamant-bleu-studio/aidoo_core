<?php

/**
 * CMS Aïdoo
 *
 * Copyright (C) 2013  Flamant Bleu Studio
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

class CMS_Application_Tools {

	public static function formatSize ($dsize) {
        if (strlen($dsize) <= 9 && strlen($dsize) >= 7) {                
            $dsize = number_format($dsize / 1048576, 1);
            return $dsize." MB";
        } 
        elseif (strlen($dsize) >= 10) {
            $dsize = number_format($dsize / 1073741824, 1);
            return $dsize." GB";
        } 
        else {
            $dsize = number_format($dsize / 1024, 1);
            return $dsize." KB";
        }
	}
    public static function resetLevels(array $config) {
        
        $root = current($config);
        $rootKey = key($config);
        $key = key($root);
        
        if (!is_int($key)) {
            $config = array();
            $config[$rootKey][0] = $root;
        }
        
        return $config;
    }	

	public static function _convertDateTimePickerToUs($dateTime)
	{
		$day    = substr($dateTime,0,2);
		$month  = substr($dateTime,3,2);
		$year   = substr($dateTime,6,4);
		$hour   = substr($dateTime,13,2);
		$minute = substr($dateTime,16,2);
		$second = substr($dateTime,19,2);
		
		return date("Y-m-d H:i:s",mktime($hour,$minute,$second,$month,$day,$year));		
	}
	
    public static function _convertDateTimeUsToPicker($dateTime)
	{
		return date("d/m/Y - H:i",strtotime ($dateTime));
	}
	
	public static function _convertDateTimeFrToUs($dateTime)
	{
		$day    = substr($dateTime,0,2);
		$month  = substr($dateTime,3,2);
		$year   = substr($dateTime,6,4);
		$hour   = substr($dateTime,13,2);
		$minute = substr($dateTime,16,2);
		$second = substr($dateTime,19,2);
		
		return date("Y-m-d H:i:s",mktime($hour,$minute,$second,$month,$day,$year));		
	}
	
	public static function _convertDateTimeUsToFr($dateTime)
	{
		return date("d/m/Y - H:i",strtotime ($dateTime));
	}
	
	/**
	 * Retourne le nom de domaine courant
	 * Gère : - Protocole (http / https)
	 * 		  - Port (443 / 80)
	 * Exemples : - http://mydomain.fr
	 * 			  - https://mydomain.com
	 */
	public static function getCurrentDomain()
	{
		return 	(isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] === 443 || $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT'])));
	}
	
	/**
	 * read last X lines of a given file
	 * @param string $file path + filename
	 * @param int $lines number of lines
	 */
	public static function getLastLinesOfFile($file, $lines) {

	// $header: flag to indicate that the file contains a header which should not be included if the number of lines in the file is <= lines
	    global $error_string;
	   
	    // Number of lines read per time
	    $bufferlength = 1024;
	    $aliq = "";
	    $line_arr = array();
	    $tmp = array();
	    $tmp2 = array();
	   
	    if (!($handle = fopen($file , "r"))) {
	        echo("Could not fopen $file");
	    }
	
	    if (!$handle) {
	        echo("Bad file handle");
	        return 0;
	    }
	
	    // Get size of file
	    fseek($handle, 0, SEEK_END);
	    $filesize = ftell($handle);
	
	    $position= - min($bufferlength,$filesize);
	
	    while ($lines > 0) {
	        if (fseek($handle, $position, SEEK_END)) {
	            echo("Could not fseek");
	            return 0;
	        }
	       
	        unset($buffer);
	        $buffer = "";
	        // Read some data starting fromt he end of the file
	        if (!($buffer = fread($handle, $bufferlength))) {
	            echo("Could not fread");
	            return 0;
	        }
	       
	        // Split by line
	        $cnt = (count($tmp) - 1);
	        for ($i = 0; $i < count($tmp); $i++ ) {
	            unset($tmp[0]);
	        }
	        unset($tmp);
	        $tmp = explode("\n", $buffer);
	       
	        // Handle case of partial previous line read
	        if ($aliq != "") {
	            $tmp[count($tmp) - 1] .= $aliq;
	        }
	
	        unset($aliq);
	        // Take out the first line which may be partial
	        $aliq = array_shift($tmp);
	        $read = count($tmp);
	       
	        // Read too much (exceeded indicated lines to read)
	        if ($read >= $lines) {
	            // Slice off the lines we need and merge with current results
	            unset($tmp2);
	            $tmp2 = array_slice($tmp, $read - $lines);
	            $line_arr = array_merge($tmp2, $line_arr);

	            // Break the loop
	            $lines = 0;
	        }
	        // Reached start of file
	        elseif (-$position >= $filesize) {
	            // Get back $aliq which contains the very first line of the file
	            unset($tmp2);
	            $tmp2[0] = $aliq;
	           
	            $line_arr = array_merge($tmp2, $tmp, $line_arr);
	          
	            // Break the loop
	            $lines = 0;
	        }
	        // Continue reading
	        else {
	            // Add the freshly grabbed lines on top of the others
	            $line_arr = array_merge($tmp, $line_arr);
	            $lines -= $read;
	
	            // No longer a full buffer's worth of data to read
	            if ($position - $bufferlength < -$filesize) {
	                $bufferlength = $filesize + $position;
	                $position = -$filesize;                    
	            }
	            // Still >= $bufferlength worth of data to read
	            else {
	                $position -= $bufferlength;
	            }
	        }
	    }
	   
	    fclose($handle);
	
	    return $line_arr;
	}
	

	public static function generatePassword($type = 'alphanum', $lenght = 8)
	{
		$numeric 			= "0123456789";
		$alhpa 				= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$specialCharacter 	= "#&,;:!=+";
		
		if ($type == 'numeric')
			$chaine = $numeric;
		else if ($type == 'alpha')
			$chaine = $alhpa;
		else if ($type == 'alphanum')
			$chaine = $alhpa.$numeric;
		else
			throw new Exception('Invalid type password');
		
		$lenghtStringChar = strlen($chaine);
		
		$return = "";
		for($u = 1; $u <= $lenght; $u++)
			$return .= $chaine[mt_rand(0,($lenghtStringChar-1))];
		
		return $return;
	}


	public static function checkPOST($params = array())
	{
		$return = array();
		
		if (is_array($params)) {
			foreach ($params as $param) {
				if (array_key_exists($param, $_POST))
					$return[$param] = htmlspecialchars($_POST[$param]);
				else
					return false;
			}
		} else {
			if (array_key_exists($params, $_POST))
				$return[$params] = htmlspecialchars($_POST[$params]);
			else
				return false;
		}
		
		return $return;
	}
	
	public static function generateApiKey()
	{
		//on utlilise mt_rand() pour avoir une valeur plus aléatoire qu’avec la fonction rand(),
		//et on passe true en paramètre à uniqid() pour lui dire qu’on veut une longue chaine de caractère
		$uniqid = uniqid(mt_rand(),true) ;
		return md5($uniqid); // on retourn le hash
	}
}

