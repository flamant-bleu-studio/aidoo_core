{include file="{$smarty.const.APPLICATION_PATH}/modules/users/views/back/menu.tpl" active="access"}

<div id="content">

<a href="{routeShort action="create-viewaccess"}" class="btn btn-primary">
	<i class="icon-plus icon-white"></i> {t}Add access{/t}
</a>

<hr />

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>{t}Name{/t}</th>
			<th style="width: 96px;">Actions</th>
		</tr>
	</thead>
	<tbody>
	{if $viewAccess != null}
	{foreach from=$viewAccess key=key item=v}
		<tr>
		 	 <td>{$v->name}</td>
		     <td>
				<a class="btn btn-primary btn-mini" href='{routeShort action="edit-viewaccess" id=$v->id}'><i class="icon-pencil icon-white"></i></a>
				<a class="btn btn-danger btn-mini" href='{routeShort action="delete-viewaccess" id=$v->id}' onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this view-access ?{/t}</h1>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>
			</td>
		</tr>
	{/foreach}
	{else}
		<tr class='second'>
			<td colspan="6" style="text-align: center;">Aucun membre... Click Add !</td>
		</tr>
	{/if}
	</tbody>
</table>
</div>