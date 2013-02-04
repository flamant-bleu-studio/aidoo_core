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
	<h1>{t}My articles{/t}</h1>
	<div>{t}Manage my articles{/t}</div>
</div>

<div class="zone">
	<div class="zone_titre">
		<h2>{t}Your futur article{/t} <span class="helper"></span></h2>
		<div>{t}Enter the informations{/t}</div>
	</div>
	
	<form id="{$form->getId()}" enctype="multipart/form-data" action="{$form->getAction()}" method="post">
	
		<div class="zone">
			<div class="zone_titre">
				<div>{t}Document options{/t}</div>
			</div>
			
			{$form->id}
			{$form->status}
			
			<div class="pull-left">{$form->category}</div>
			
			{if $backAcl->hasPermission("mod_categories", "create")}
				<a href="{routeFull action="create-category-in-article"}" style="margin-left:5px;" class="pull-left btn btn-success fancybox">{t}Add a new category{/t}</a>
			{/if}
			
			{$form->access}
			<div class="row-fluid">
				<div class="span6">
				{$form->date_start}
				</div>

				<div class="span6">
					{$form->isPermanent}
				</div>
			
			</div>

			
			{$form->date_end}
		</div>
		
		<div class="zone">
			<div class="zone_titre">
				<div>{t}Document content{/t}</div>
			</div>
		
			{$form->title}
			{$form->image}
			{$form->chapeau}
			{$form->readmore}
			<div class="clearfix"></div>
			{$form->readmore_elements}
		</div>
		
		<div class="zone">
			<div class="zone_titre">
				<div>{t}Facebook comments{/t}</div>
			</div>
			{$form->fb_comments_active}
		</div>

		<div class="zone">
			<div class="zone_titre">
				<div>{t}Manage rights{/t}</div>
			</div>
			<div class="droits_content">
				{$form->permissions}
			</div>
		</div>
		
		{formButtons cancelLink="{routeShort action='index'}"}

	</form>
</div>

<script type="text/javascript">

function updateSelectCategories(datas) {
	$('#category').append('<option value='+datas.id_categorie+' label='+datas.title+'>'+datas.title+'</option>');
	$('#category').trigger("liszt:updated");
}

$(document).ready(function(){
	showContent($("#readmore").attr('checked'));
	showDate(!$("#isPermanent").attr('checked'));
	
	$("#readmore").change(function(){
		showContent(this.checked);
	});
	$("#isPermanent").change(function(){
		showDate(!this.checked);
	});

	function showContent(state)	{
		if(state)
			$('#readmore_elements-element').show();
		else
			$('#readmore_elements-element').hide();
	}
	function showDate(state) {
		if(state)
			$('#form_date_end').show();
		else
			$('#form_date_end').hide();
	}

	$(document).ready(function() {
		
		$(".fancybox").fancybox({
			'scrolling'		: 'auto',
			'width'			: '75%',
			'height'		: '100%',
			'titleShow'		: false,
			'autoScale'		: true,
			'type'		: 'iframe'
		});
	});
});
</script>
