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

<div class="contact">
	{if $titleForm}
    <h1>{$titleForm}</h1>
    {else}
    <h1>Formulaire de contact</h1>
    {/if}
    
    <div id="contact_content">
    	{$contact_content}
    </div>
    
    <div id="contact_form">   
		{if $sendOk == true}
			<p>Mail envoyé avec succès<p>
		{elseif $sendError == true}
			<p>Erreur lors de l'envoi de l'email<p>
		{else}
			<div class="ui-body ui-body-e ui-corner-all">{$form}</div>
		{/if}   
    </div>
</div>
