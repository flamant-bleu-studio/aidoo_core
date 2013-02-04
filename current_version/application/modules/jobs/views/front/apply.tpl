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

<div class="document">
	{if $job}
		<h1>Postuler : {$job->job_title}</h1>
	{else}
		<h1>Candidature spontanée</h1>
	{/if}
	<div class="document_content">
		<div class="jobapply formulaire">
			{if $sentOk == true}
				<p>Votre email nous a bien été envoyé, nous vous en remercions. <br/>Nous étudierons votre candidature dans les meilleurs délais.<p>
			{else if $sentError == true}
				<p>Une erreur s'est produite lors de l'envoi de votre candidature.<br/>Veuillez réessayer ou contacter l'administrateur.<p>
			{else}
			
				<form action="{$form->getAction()}" method="post" id="{$form->getId()}" enctype="{$form->getEnctype()}">
					
					{$form->id}		
					{$form->civilite}
					{$form->lastName}
					{$form->firstName}
					{$form->adress}
					{$form->cp}
					{$form->city}
					{$form->phone}
					{$form->email}
					{$form->message}
					{$form->cv}
					{$form->submit}
					{$form->object}
				</form>
			{/if}
		</div>
	</div>
	<div class="document_content_bottom"></div>
</div>
