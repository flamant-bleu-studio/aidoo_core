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

<a href="#" id="login">
	<div class="contenair-carre size-50">
		<div class="carre hasIcon hasText" style="background-color: #ED6C10;">
			<img src="http://demo.selectup.net/skins/lempereur/icon/connect-white.png" />
			<div class="text" style="color: #fff;">Connexion</div>
		</div>
	</div>
</a>

<a href="#" id="register">
	<div class="contenair-carre size-50">
		<div class="carre hasIcon hasText" style="background-color: #F39200;">
			<img src="http://demo.selectup.net/skins/lempereur/icon/inscription-white.png" />
			<div class="text" style="color: #fff;">Inscription</div>
		</div>
	</div>
</a>

<div class="clear"></div>


<div id="form_login" style="display:none;width: 100%;">
	{$loginForm}
</div>

<div id="form_register" style="display:none;width: 100%;">
	{$formInscription}
</div>

<a href="/selectup/list-concessions">
	<div class="contenair-carre size-100">
		<div class="carre hasIcon hasText" style="background-color: #E84720;">
			<img src="http://demo.selectup.net/skins/lempereur/icon/locate-white.png">
			<div class="text" style="color:#ffffff;">Nos concessions</div>
			<div class="clear"></div>
	</div>
</a>

<script type="text/javascript">
$(document).ready(function(){
	$('#login').on('click', function(){
		$('#form_register').hide();
		$('#form_login').show();
	});
	
	$('#register').on('click', function(){
		$('#form_login').hide();
		$('#form_register').show();
	});
});
</script>