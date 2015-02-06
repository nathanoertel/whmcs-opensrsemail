<style type="text/css">{$css}</style>
<div class="navbar">
	{if $addMailbox}
		<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=mailbox" title="Add Mailbox">Add Mailbox</a>
	{else}
		<a class="btn" href="upgrade.php?type=configoptions&id={$serviceid}" title="Add Mailbox">Add Mailbox</a>
	{/if}
	{if $addForward}
		<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=forward" title="Add Forwarding Mailbox">Add Forwarding Mailbox</a>
	{else}
		<a class="btn" href="upgrade.php?type=configoptions&id={$serviceid}" title="Add Forwarding Mailbox">Add Forwarding Mailbox</a>
	{/if}
	{if $addAlias}
		<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=alias" title="Add Alias">Add Alias</a>
	{/if}
	<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups" title="Edit Workgroups">Edit Workgroups</a>
</div>
{if $addedMailbox}
	<p class="alert alert-success">The mailbox has been added successfully.</p>
{/if}
{if $editedMailbox}
	<p class="alert alert-success">The mailbox has been updated successfully.</p>
{/if}
{if $deleteMailbox}
	<p class="alert alert-error">You have more than the allowed number of mailboxes, either <a href="upgrade.php?type=configoptions&id={$serviceid}">add more mailboxes</a> to your account or remove mailboxes.</p>
{/if}
{if $deleteForward}
	<p class="alert alert-error">You have more than the allowed number of forwards, either <a href="upgrade.php?type=configoptions&id={$serviceid}">add more mailboxes</a> to your account or remove forward mailboxes.</p>
{/if}
{if $deleteSuccess}
	<p class="alert alert-success">The mailbox was deleted succssfully.</p>
{/if}
{foreach from=$error item=error}
	<p class="alert alert-error">{$error}</p>
{/foreach}
{if count($mailboxes)}
	<table class="table table-framed table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Workgroup</th>
				<th class="button-column"></th>
				<th class="button-column"></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$mailboxes item=mailbox}
				<tr>
					<td>{$mailbox.mailbox}</td>
					<td>{$mailbox.uctype}</td>
					<td>{$mailbox.workgroup}</td>
					<td>
						{if $mailbox.type != "alias"}
							<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&mailbox={$mailbox.mailbox}&workgroup={$mailbox.workgroup}&type={$mailbox.type}" title="Edit Mailbox">Edit</a>
						{/if}
					</td>
					<td>
						<form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
							<input type="hidden" name="modaction" value="delete-mailbox" />
							<input type="hidden" name="mailbox" value="{$mailbox.mailbox}" />
							<input class="btn" type="submit" name="submit" value="Delete" title="Delete Mailbox" />
						</form>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<div class="alert alert-info">There are no mailboxes</div>
{/if}