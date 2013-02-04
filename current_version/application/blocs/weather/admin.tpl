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

{if $notConfig|@count > 0}
	<div style="border: 1px dashed red;padding: 10px;text-align: center;margin-bottom:15px;">
		Un ou plusieurs types de formulaire ne sont pas configurés. <a href="{routeFull route='contact_back'}">Configurer le module</a>.
	</div>
{/if}

{$form->code}
{$form->latlong}

<div class="clear"></div>
