{include file="{$smarty.const.APPLICATION_PATH}/modules/users/views/back/menu.tpl" active="groups"}

<div id="content">

<a href="{routeShort action="create-group"}" class="btn btn-primary">
	<i class="icon-plus icon-white"></i> {t}Add group{/t}
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
		{if $groups != null}
		{foreach from=$groups key=key item=v}
				<tr>
				 	 <td>{$v->level} {$v->name}</td>
				     <td>
						{if $backAcl->hasPowerOn($v->id) && $groupId != $v->id}
						<a class="btn btn-primary btn-mini" href='{routeShort action="edit-group" id=$v->id}'><i class="icon-pencil icon-white"></i></a>
						{if $v->id != 2}
						<a class="btn btn-danger btn-mini" href='{routeShort action="delete-group" id=$v->id}' onClick="confirmDelete(this.href, '<h1>{t}Are you sure to delete this group ?{/t}</h1><p>{t}Children groups will not be delete{/t}</p><p>{t}All users in this group will be move in Public group{/t}</p>', '{t}Delete{/t}', '{t}Cancel{/t}');return false;"><i class="icon-trash icon-white"></i></a>{/if}
						{/if}
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