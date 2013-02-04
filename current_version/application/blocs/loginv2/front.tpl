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

<div class="bloc_login">
{if !$hasIdentity}

	<form enctype="multipart/form-data" action="{$form->getAction()}" method="post">
		{$form->type}
		{$form->email}
		{$form->password}
		<a href="{routeFull route='users' controller='users' action='forgot-password'}">{t}forgot password ?{/t}</a>
		{$form->submit}
	</form>
{else}
	<div class="connected_field">
		{t}YOU ARE CONNECTED{/t}
		<p><a href="{routeFull route='users' controller='users' action='logout'}">{t}DISCONNECTING{/t}</a></p>
	</div>
{/if}
</div>

<script type="text/javascript">
{literal}
$(document).ready(function() {
	var email;
	var password;
	
	$(".bloc_login").find('input').live('focus', function(){
		if ($(this).val() == 'EMAIL'){
			login = $(this).val();
			$(this).val('');
		}
		if ($(this).val() == 'PASSWORD'){
			password = $(this).val() ;
			$(this).val('');
			switchPasswordText($(this));
		}
	});
	$(".bloc_login").find('input').live('focusout', function(){
		if ($(this).val() == ''){
			$(this).attr('id') == 'email' ? $(this).val(login) : $(this).val(password) ;	
			if ($(this).attr('id') == 'password'){
				switchPasswordText($(this));
			}
		}
	});
	
	function switchPasswordText(obj){
		var originalBtn = obj;
		var newBtn = originalBtn.clone();

		if (newBtn.attr('type') == 'text')
			newBtn.attr("type", "password");
		else
			newBtn.attr("type", "text");
		
		newBtn.insertBefore(originalBtn);
		originalBtn.remove();
		newBtn.attr("id", originalBtn.attr('id'));
		
		if (newBtn.attr('type') == 'password')
			newBtn.focus();		
	}
});
{/literal}
</script>
