<style type="text/css">{$css}</style>
<div class="osrs-mailbox-management">
	<div class="osrs-sidebar">
		<ul class="osrs-menu">
			<li class="back"><img class="osrs-image" src="modules/servers/opensrsemail/img/back.png"/> <a href="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups">Back To Workgroups</a></li>
		</ul>
	</div>
	<div class="osrs-main-content">
		<h3>Add Workgroup</h3>
		{foreach from=$error item=error}
			<p class="alert alert-error">{$error}</p>
		{/foreach}
		<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox" class="form-stacked" method="post">
			<input type="hidden" name="modaction" value="save-workgroup" />
			<div>
				<fieldset class="control-group">
					<div class="control-group">
						<label class="control-label" for="workgroup">Workgroup</label>
						<div class="control">
							<input type="text" name="workgroup" value="{$workgroup.workgroup}" />
						</div>
					</div>
				</fieldset>
				<div class="buttons">
					<button class="btn" type="submit">Save</button>
					<a href="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups">Cancel</a>
				</div>
			</div>
		</form>
	</div>
</div>