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
	font-family: verdana;
}
-->
</style></head>

<body>
<p>Nouvel article sur {$smarty.server.SERVER_NAME}</p><br />
 
<p>Un nouvel article vient d'être proposé.</p>
<p>Titre : {$article->title}</p>
<p>Pour l'éditer : <a href="http://{$smarty.server.SERVER_NAME}{$urlAdminArticle}">http://{$smarty.server.SERVER_NAME}{$urlAdminArticle}</a></p><br />

<p style="font-style:italic;">Email envoyé le {formatDate format="EEEE F"} à {formatDate format="HH:mm"}</p>
</body>
</html>
