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

<div id="user_register">
	<h2>{t}Register{/t}</h2>
	<div class="content">
	
		<div class="facebook">
			<a href="{$loginUrlFacebook}"><img src="{$baseUrl}{$skinUrl}/img/facebook_log.jpg"/></a>
		</div>
		
		<p class="login_separator">{t}Or{/t}</p>

		<form id="{$formInscription->getId()}" action="{$formInscription->getAction()}" method="post">
			<input type="hidden" name="type" value="register">
			{$formInscription->username}
			{$formInscription->email}
			{*{$formInscription->civility}*}
			{$formInscription->firstname}
			{$formInscription->lastname}
			{$formInscription->password}
			{$formInscription->verifPassword}
			{$formInscription->submit}
		</form>
		
	</div>
</div>

<div id="user_login">
	<h2>{t}Login{/t}</h2>
	<div class="content">
	
		<div class="facebook">
			<a href="{$loginUrlFacebook}"><img src="{$baseUrl}{$skinUrl}/img/facebook_log.jpg"/></a>
		</div>
		
		<p class="login_separator">{t}Or{/t}</p>
		
		<form id="{$loginForm->getId()}" action="{$loginForm->getAction()}" method="post">
			<input type="hidden" name="type" value="login">
			{$loginForm->email}
			{$loginForm->password}
			<div class="left">{$loginForm->submit}</div>
			<a href="{routeShort action="forgot-password"}" class="link_button">{t}Forgot password{/t}</a>
			<div class="clear"></div>
		</form>
	</div>
</div>

