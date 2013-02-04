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
	<h1>{t}Calendar{/t}</h1>
	<div>{t}Edit event{/t}</div>
</div>

<div class="zone">
	
	<form id="{$form->getId()}" method="{$form->getMethod()}" action="{$form->getAction()}">
		
		{$form->id_calendar}
		
		{$form->name}
		{$form->description}
		
		{$form->date_start}
		{$form->date_end}
		
		{$form->status}
		
		<div class="form_submit">
			<button class="btn btn-large btn-success">{t}Submit{/t}</button>
		</div>
		
	</form>
	
</div>
