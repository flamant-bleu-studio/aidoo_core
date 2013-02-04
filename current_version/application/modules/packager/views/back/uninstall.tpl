					<!--[if !IE]>start section<![endif]-->	
					<div class="section table_section">
						<!--[if !IE]>start title wrapper<![endif]-->
						<div class="title_wrapper">
							<h2>{t}Warning: Active instances of this package had been found!{/t}</h2>
							<span class="title_wrapper_left"></span>
							<span class="title_wrapper_right"></span>
						</div>
						<!--[if !IE]>end title wrapper<![endif]-->
						<!--[if !IE]>start section content<![endif]-->

						<div class="section_content">
							<!--[if !IE]>start section content top<![endif]-->
							<div class="sct">
								<div class="sct_left">
									<div class="sct_right">
										<div class="sct_left">
											<div class="sct_right">
											
											
												<!--[if !IE]>start table_wrapper<![endif]-->
												<div class="table_wrapper">
													<div class="table_wrapper_inner">
													<table cellpadding="0" cellspacing="0" width="100%">
														<tbody>
														<tr>
															<th>{t}Instance Name{/t}</th>
															<th>Instance Type</th>
															<th>Instance PlaceHolder</th>
														</tr>

														{if $instances != null}
															{foreach from=$instances key=key item=item}
															 	{cycle values="second,first" assign="placeholdercolor"}
																<tr class='{$placeholdercolor}'>
																	<td>{$item.title}</td>
																	<td>{$item.type}</td>
																	<td>{$item.placeholder}</td>
																</tr>
															 {/foreach}
														{else}
															<tr class='second'>
																<td colspan="3" style="text-align: center;">No instance found, It's safe to uninstall</td>
															</tr>
														{/if}
														
													</tbody></table>
													</div>
												</div>
												<!--[if !IE]>end table_wrapper<![endif]-->
											





												
												<!--[if !IE]>start table menu<![endif]-->
												<!--[if !IE]>end table menu<![endif]-->

												
												
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--[if !IE]>end section content top<![endif]-->
							<!--[if !IE]>start section content bottom<![endif]-->
							<span class="scb"><span class="scb_left"></span><span class="scb_right"></span></span>
							<!--[if !IE]>end section content bottom<![endif]-->
							
						</div>
						
						
						<!--[if !IE]>start section content<![endif]-->
						<br/>
						
						<!--[if !IE]>start title wrapper<![endif]-->
						<div class="title_wrapper">
							<h2>{t}Confirm suppression of this package?{/t}</h2>
							<span class="title_wrapper_left"></span>
							<span class="title_wrapper_right"></span>
						</div>
						

						<div class="section_content">
							<!--[if !IE]>start section content top<![endif]-->
							<div class="sct">
								<div class="sct_left">
									<div class="sct_right">
										<div class="sct_left">
											<div class="sct_right">


												
												
												<!--[if !IE]>end table menu<![endif]-->

												<div id="displayMessageBox">
													<ul class="bullet-red" >
														<li>{t}Removing this package will also remove associated instances!{/t}</li>
													</ul>
												</div>
												
												<!--[if !IE]>start table menu<![endif]-->
												<div class="table_menu">
													<ul class="left">
														<li><a href="{$adminBaseUrl}/packager/execuninstall/{$packageName}/{$packageType}" class="button add_new"><span><span>{t}Yes{/t}</span></span></a></li>
														<li><a href="{$adminBaseUrl}/packager/" class="button add_new"><span><span>{t}Cancel{/t}</span></span></a></li>
													</ul>
												</div>
												
												
												
											</div>
										</div>
									</div>
								</div>
							</div>
							<!--[if !IE]>end section content top<![endif]-->
							<!--[if !IE]>start section content bottom<![endif]-->
							<span class="scb"><span class="scb_left"></span><span class="scb_right"></span></span>
							<!--[if !IE]>end section content bottom<![endif]-->
							
						</div>
						
						

						<!--[if !IE]>end section content<![endif]-->
					</div>
					<!--[if !IE]>end section<![endif]-->
	