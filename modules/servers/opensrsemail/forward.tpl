<style type="text/css">{$css}</style>
<div class="osrs-mailbox-management">
	<div class="osrs-sidebar">
		<ul class="osrs-menu">
			<li class="back"><img class="osrs-image" src="modules/servers/opensrsemail/img/back.png"/> <a href="clientarea.php?action=productdetails&id={$serviceid}">Back To Mailboxes</a></li>
		</ul>
	</div>
	<div class="osrs-main-content">
		{if $deleteRequired}
			<p class="alert alert-error">You have more than the allowed number of forwards, either <a href="upgrade.php?type=configoptions&id={$serviceid}">add more forwards</a> to your account or remove forwards.</p>
		{else}
			{if $new}
				<h3>Add Forwarding Mailbox</h3>
			{else}
				<h3>Edit Forwarding Mailbox</h3>
			{/if}
			{foreach from=$error item=error}
				<p class="alert alert-error">{$error}</p>
			{/foreach}
			{if $mailbox}
				<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox{if !$new}&mailbox={$mailbox.mailbox}&workgroup={$workgroup}{/if}&type={$type}" class="form-stacked" method="post">
					<input type="hidden" name="modaction" value="save-forward" />
					<div>
						<fieldset class="control-group">
							{if $new}
								<input type="hidden" name="new" value="true" />
								<div class="control-group">
									<label class="control-label" for="mailbox">Mailbox Name</label>
									<div class="control">
										<input class="small" type="text" name="mailbox" value="{$mailbox.mailbox}" /><span class="row-text">@{$domain}</span>
									</div>
								</div>
							{else}
								<div class="control-group">
									<label class="control-label" for="mailbox">Mailbox Name</label>
									<div class="control">
										<input type="hidden" name="mailbox" value="{$mailbox.mailbox}" />
										<span class="row-text">{$mailbox.mailbox}@{$domain}</span>
									</div>
								</div>
							{/if}
							<div class="control-group">
								<label class="control-label" for="password">Forward To (comma separated email addresses)</label>
								<div class="control">
									<input type="text" name="forwardEmail" value="{$mailbox.forward_email}" />
								</div>
							</div>
							{if $new}
								<div class="control-group">
									<label class="control-label" for="workgroup">Workgroup</label>
									<div class="control">
										<select name="workgroup">
											{foreach from=$workgroups item=workgroup}
												<option value="{$workgroup.workgroup}" {if $workgroup.workgroup == $mailbox.workgroup}selected="true"{/if}>{$workgroup.workgroup}</option>
											{/foreach}
										</select>
									</div>
								</div>
							{/if}
						</fieldset>
						<div class="buttons">
							<button class="btn" type="submit">Save</button>
							<a href="clientarea.php?action=productdetails&id={$serviceid}">Cancel</a>
						</div>
					</div>
				</form>
			{/if}
		{/if}
	</div>
</div>