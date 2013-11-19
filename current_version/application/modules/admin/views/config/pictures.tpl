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

<div id="content">
	
	<div class="content_titre">
		<h1>{t}Edit site configuration{/t}</h1>
		<div>Images</div>
	</div>
	
	<form method="post" id="form-size" >
		
		{counter start="0" assign="test" print=false}
		
		{if $thumbs_sizes}
		{foreach from=$thumbs_sizes item=size}
		    <div class="well form-inline">
			    <input type="text" name="name[{$test}]" class="name input-small" placeholder="{t}Title{/t}" value="{$size.name}" {if $size.name == 'default'}readonly="readonly"{/if}/>
			    
			    &nbsp;
			 	<label for="width[{$test}]">
				 	<div class="input-prepend">
				 		Width <input name="width[{$test}]" type="text" class="width input-mini" value="{$size.width}" /><span class="add-on">px</span>
				 	</div>
			 	</label>
				<label for="height[{$test}]">
					<div class="input-prepend">
						Height <input name="height[{$test}]" type="text" class="height input-mini" value="{$size.height}" /><span class="add-on">px</span>
					</div>
				</label>
				
				&nbsp;
				<label for="adaptiveResize[{$test}]">
					Adaptive resize  
					<input type="checkbox" name="adaptiveResize[{$test}]" class="adaptiveResize" {if $size.adaptiveResize}checked{/if} /> <span class="help-inline">{t}(cropped from center){/t}</span>
				</label>
				
				&nbsp;
				{if $size.name != 'default'}
			   		<button class="btn btn-danger delete-size"><i class="icon-trash icon-white"></i></button>
			   	{/if}
		    </div>
		    
		    {counter print=false}
	    {/foreach}
	    {/if}
		
		{$form->imageDefault}
		
		<div class="form_submit">
			<button class="btn btn-success add" id="add-size"><i class="icon-plus icon-white"></i> {t}Add new size{/t}</button>		
			<button class="btn btn-primary">{t}Save{/t}</button>
			<a href='{routeShort action="regenerate-pictures"}' class="btn btn-danger right" onClick="confirmDelete(this.href, '<h1>{t}Are you sure you want to regenerate images ?{/t}</h1><br/><span style=\'color: red;\'>{t}This action may take several minutes.{/t}</span>', '{t}Yes{/t}', '{t}No{/t}');return false;""><i class="icon-repeat icon-white"></i>  {t}Regenerate pictures{/t}</a>
		</div>
	</form>

</div>

<script type="text/javascript">
$(document).ready(function(){

	var counter = '{$test + 1}';
	
	$("#add-size").on("click", function(e){
		e.preventDefault();

		var line = $(this).parents("form").find(".form-inline:first").clone();
		
		var name = line.find(".name");
		var width = line.find(".width");
		var height = line.find(".height");
		var adaptiveResize = line.find(".adaptiveResize");
		
		name.attr("name", name.attr("name").replace("0", counter)).removeAttr("readonly").val("");
		width.attr("name", width.attr("name").replace("0", counter)).val("");
		height.attr("name", height.attr("name").replace("0", counter)).val("");
		adaptiveResize.attr("name", adaptiveResize.attr("name").replace("0", counter)).removeAttr('checked');
		
		$(this).before(line);
		counter++;
	});
	
	$(".delete-size").on("click", function(e){
		e.preventDefault();
		$(this).parents(".form-inline").remove();
	});
});
</script>
