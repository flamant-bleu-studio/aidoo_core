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

require_once 'Zend/Validate/Abstract.php';

class CMS_Validate_ColorHexa extends Zend_Validate_Abstract
{
    const INVALID  = 'colorHexa';

    protected $_messageTemplates = array(
       self::INVALID    => "'%value%' ne semble pas être un code couleur hexadecimal valide",
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        if( preg_match("/^([0-9a-fA-F]{3})||([0-9a-fA-F]{6})$/", $value) ){
            return true;
        }
        
        $this->_error(self::INVALID);
        return false;
    }
}
