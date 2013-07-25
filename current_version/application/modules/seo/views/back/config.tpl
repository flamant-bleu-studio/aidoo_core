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

{include file="{$smarty.const.APPLICATION_PATH}/modules/seo/views/back/menu.tpl" active="config"}

<div id="content">
	
	<h3>Référencement par défaut</h3>
	
	{$formGeneralConfig}
	
	<hr />
	
	<h3>Pages d'accueil</h3>
	
	{$homeForm}
	
	<hr />
	
	<h3>Page d'erreurs</h3>
	
	<form class="form-horizontal" method="post">
		<input type="hidden" value="" name="404" />
		
		<div class="control-group">		
			<label class="control-label" for="template">Template</label>

			<div class="controls">
				<select name="template">
					{html_options options=$templates selected=$tpl_404}
				</select>
				<p class="help-block">Template de la page d'erreurs</p>
			</div>
		</div>
		
		<div id="form_home_submit" class="form_submit">
			<button class="btn btn-success" name="home_submit">
				<span>Valider</span>
			</button>	
		</div>
	</form>

</div>