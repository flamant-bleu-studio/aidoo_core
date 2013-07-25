<ul class="nav nav-tabs" id="nav-users">
	<li {if $active == 'users'}class="active"{/if}><a href="{routeShort action="users"}">{t}Users{/t}</a></li>
	<li {if $active == 'groups'}class="active"{/if}><a href="{routeShort action="groups"}">{t}Groups{/t}</a></li>
	<li {if $active == 'access'}class="active"{/if}><a href="{routeShort action="access"}">{t}Access{/t}</a></li>
	<li {if $active == 'options'}class="active"{/if}><a href="{routeShort action="options"}">{t}Options{/t}</a></li>
	<li {if $active == 'permissions'}class="active"{/if}><a href="{routeShort action="permissions"}">{t}Permissions{/t}</a></li>
</ul>