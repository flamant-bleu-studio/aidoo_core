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

{if $ongletWithBlocDeleted|@count > 0}
	<div style="border: 1px dashed red;padding: 10px;text-align: center;margin-bottom:15px;">
		Attention, les onglets suivants : 
		
		{foreach name=foo from=$ongletWithBlocDeleted key=key item=onglet}
			"{$onglet}"
			{if !$smarty.foreach.foo.last},{/if}
		{/foreach}
		
		sont associés à un bloc qui n'existe plus.
	</div>
{/if}

<div id="add_onglet">
	<span class="text">{t}Add tab{/t}</span>
</div>

<div id="list_onglet">
	
	{if $onglets|@count > 0}
		{foreach from=$onglets key=keyOnglet item=onglet}
			<div class='onglet' realid="{$keyOnglet}">
			
				<div class="title form_line left">
					<div class="form_text">
						<div class="form_label">
							<label for="onglet[{$keyOnglet}][title]">{t}Title{/t}</label>
						</div>
						<div class="form_desc">{t}Tab title{/t}</div>
					</div>
					<div class="form_elem">
						<input type='text' name='onglet[{$keyOnglet}][title]' value='{$onglet["title"]}' />
					</div>
					<div class="clear"></div>
				</div>
				
				<input class="deleteOnglet left" type="submit" value="{t}Delete tab{/t}" />
				<input class="addBloc left" type="submit" value="{t}Add block{/t}" />
				<div class="clear"></div>
				
				<div class="css form_line">
					<div class="form_text">
						<div class="form_label">
							<label for="onglet[{$keyOnglet}][css]">{t}CSS class{/t}</label>
						</div>
						<div class="form_desc">{t}Tab CSS class{/t}</div>
					</div>
					<div class="form_elem">
						<input type='text' name='onglet[{$keyOnglet}][css]' value='{$onglet["css"]}' />
					</div>
					<div class="clear"></div>
				</div>
				
				<br/><br/>
				{t}Associated blocks{/t}: <br/><br/>
				
				{foreach from=$onglet["blocs"] key=keyBloc item=bloc}
					<div class="select">
						<select name="onglet[{$keyOnglet}][blocs][{$keyBloc}]">
							{html_options options=$allBlocsTpl selected=$bloc}
						</select>
						<input class="deleteBloc" type="submit" value="{t}Delete block{/t}" />
					</div>
				{/foreach}
				
			</div>
		{/foreach}
	{/if}
	
</div>

<div id="sample_select" style="display:none;">
	<div class="select">
		<select>
			{html_options options=$allBlocsTpl}
		</select>
		<input class="deleteBloc" type="submit" value="{t}Delete block{/t}" />
	</div>
</div>
	
<div id="sample_onglet" style="display:none;">
	<div class='onglet'>
		<div class="title form_line left">
			<div class="form_text">
				<div class="form_label">
					<label for="onglet[{$keyOnglet}][title]">{t}Title{/t}</label>
				</div>
				<div class="form_desc">{t}Tab title{/t}</div>
			</div>
			<div class="form_elem">
				<input type='text' value='{$onglet["title"]}' />
			</div>
			<div class="clear"></div>
		</div>
		
		<input class="deleteOnglet left" type="submit" value="{t}Delete tab{/t}" />
		<input class="addBloc left" type="submit" value="{t}Add block{/t}" />
		<div class="clear"></div>
		
		<div class="css form_line">
			<div class="form_text">
				<div class="form_label">
					<label for="onglet[{$keyOnglet}][css]">{t}CSS class{/t}</label>
				</div>
				<div class="form_desc">{t}Tab CSS class{/t}</div>
			</div>
			<div class="form_elem">
				<input type='text' value='{$onglet["css"]}' />
			</div>
			<div class="clear"></div>
		</div>
		
		<br/><br/>
		{t}Associated blocks{/t}: <br/><br/>
	</div>
</div>

{literal}
<script type="text/javascript">
$(document).ready(function(){
	
	var skinUrl 		= "{/literal}{$skinUrl}{literal}";
	var counter_onglet 	= $("#list_onglet div.onglet").size();
	var counter_bloc 	= $(".onglet div.select").size() + 1;

	$("#add_onglet").live("click", function(){
		var new_onglet = $("#sample_onglet .onglet").clone();
		
		new_onglet.attr("realid", counter_onglet);
		new_onglet.find(".title input").val("").attr("name", "onglet["+counter_onglet+"][title]");
		new_onglet.find(".css input").val("").attr("name", "onglet["+counter_onglet+"][css]");
		new_onglet.find(".select").remove();
		$("#list_onglet").append(new_onglet);

		counter_onglet++;
	});
	
	$("#list_onglet .deleteOnglet").live("click", function(e){
		e.preventDefault();		
		$(this).parent().remove();
	});
	
	$("#list_onglet .addBloc").live("click", function(e){
		e.preventDefault();
		var id = $(this).parent(".onglet").attr("realid");
		var blocks_list = $("#sample_select .select").clone();
		
		blocks_list.find("select").attr("name", "onglet[" + id + "][blocs][" + counter_bloc+"]");
		
		$(this).parent(".onglet").append(blocks_list);
		
		counter_bloc++;
	});
	
	$("#list_onglet .deleteBloc").live("click", function(e){
		e.preventDefault();
		$(this).parent().remove();
	});
	
});
</script>
{/literal}

{appendScript type="css"}
input[type=submit] {
	margin-left: 20px;
}
#add_onglet {
	border:1px dashed #969696;
	cursor:pointer;
	margin:20px;
	margin-left:50px;
	padding:10px;
	width: 250px;
	text-align:center;
	margin:20px auto;
}
#add_onglet:hover {
	border-color:#FF9300;
}
#add_onglet span.text {
	font-size:14px;
	font-weight:bold;
}
.onglet {
	border-top: 1px dashed grey;
	margin:5px;
	padding:15px;
}
.onglet .form_line{
	width: auto;
}
{/appendScript}
