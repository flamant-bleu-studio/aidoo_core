<ul class="nav nav-tabs" id="nav-users">
	<li {if $active == 'users'}class="active"{/if}><a href="{routeShort action="users"}">{t}Users{/t}</a></li>
	<li {if $active == 'groups'}class="active"{/if}><a href="{routeShort action="groups"}">{t}Groups{/t}</a></li>
	<li {if $active == 'access'}class="active"{/if}><a href="{routeShort action="access"}">{t}Access{/t}</a></li>
	<li><a href="#options">{t}Options{/t}</a></li>
	<li><a href="#permissions">{t}Permissions{/t}</a></li>
</ul>