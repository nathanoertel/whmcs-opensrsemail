<style type="text/css">{$css}</style>
<div class="osrs-mailbox-management">
	<div class="osrs-sidebar">
		<ul class="osrs-menu">
			<li class="back"><img class="osrs-image" src="modules/servers/opensrsemail/img/back.png"/> <a href="clientarea.php?action=productdetails&id={$serviceid}">Back To Mailboxes</a></li>
		</ul>
	</div>
	<div class="osrs-main-content">
		<h3>Add Alias</h3>
		{foreach from=$error item=error}
			<p class="alert alert-error">{$error}</p>
		{/foreach}
		<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type={$type}" class="form-stacked" method="post">
			<input type="hidden" name="modaction" value="save-alias" />
			<div>
				<fieldset class="control-group">
					<div class="control-group">
						<label class="control-label" for="password">Alias</label>
						<div class="control">
							<input type="text" name="alias" value="{$alias}" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="workgroup">To Mailbox</label>
						<div class="control">
							<select name="mailbox">
								{foreach from=$mailboxes item=mailbox}
									<option value="{$mailbox.mailbox}">{$mailbox.mailbox}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</fieldset>
				<div class="buttons">
					<button class="btn" type="submit">Save</button>
					<a href="clientarea.php?action=productdetails&id={$serviceid}">Cancel</a>
				</div>
			</div>
		</form>
	</div>
</div>