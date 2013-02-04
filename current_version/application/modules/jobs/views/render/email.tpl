{*
* CMS Aïdoo
* 
* Copyright (C) 2013  Flamant Bleu Studio
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
*}

<html><head>
<style type="text/css">
<!--
body {
	background-color: #FFF;
	color: #000;
}
-->
</style></head>

<body>
 <p>
	 <strong>Poste :</strong> {$object}<br/>
	 <strong>Civilité :</strong> {$civilite}<br/>
	 <strong>Prénom :</strong> {$firstName}<br/>
	 <strong>Nom :</strong> {$lastName}<br/>
	 {if $adress}<strong>Adresse :</strong> {$adress}<br/>{/if}
	 {if $cp}<strong>Code Postal :</strong> {$cp}<br/>{/if}
	 {if $city}<strong>Ville :</strong> {$city}<br/>{/if}
	 {if $phone}<strong>Tel :</strong> {$phone}<br/>{/if}
	 <strong>Email :</strong> {$email}<br/>
 </p>
 {if $message}
 <p>
	<strong>Message :</strong><br/> {$message}
 </p>
 {/if}
</body>
</html>
