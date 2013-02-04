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

<div class="content_titre">
	<h1>{t}Users Manager{/t}</h1>
	<div>{t}Edit options{/t}</div>
</div>
	
<div class="zone">
	<form action="{$form->getAction()}" method="post" id="{$form->getId()}"> 
	
		{$form->mailAdminNewAccount}
		
		<div id="show_emailNotify" {if !$form->mailAdminNewAccount->getValue()}style="display:none;"{/if}>
			{$form->emailNotify}
		</div>
		
		{$form->formatDisplayName}
		
		{$form->groupFrontList}
		
		{$formAcl}
		
		<ul class="unstyled">
			<li><span class="bleu">Manage :</span> éditer ces options</li>
			<li><span class="bleu">View :</span> voir le module et son contenu</li>
		</ul>
		
		
		<button class="btn btn-large btn-success">{t}Submit{/t}</button>
		
	</form> 
</div>

<script type="text/javascript">

$("#mailAdminNewAccount").on('click', function(){
	$("#show_emailNotify").toggle($(this).is(":checked"));
});

</script>
