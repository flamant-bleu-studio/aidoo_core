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


 <form class="form-signin" action="{$form->getAction()}" method="POST">
	<h2>
		<span class="icon-stack">
		  <i class="icon-circle icon-stack-base"></i>
		  <i class="icon-signin icon-light"></i>
		</span>
		
		{t}Administration{/t}
	</h2>
  
	{$form->id}
	{$form->pass}

	<button class="btn btn-large btn-primary" type="submit">{t}Login{/t}</button>
</form>