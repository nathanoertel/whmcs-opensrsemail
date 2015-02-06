<style type="text/css">{$css}</style>
<div class="navbar">
	<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=normal&modaction=workgroup" title="Add Workgroup">Add Workgroup</a>
	<a class="btn" href="clientarea.php?action=productdetails&id={$serviceid}" title="Edit Workgroups">Edit Mailboxes</a>
</div>
{if $deleteSuccess}
	<p class="alert alert-success">The workgroup was deleted succssfully.</p>
{/if}
{if $addSuccess}
	<p class="alert alert-success">The workgroup was added succssfully.</p>
{/if}
{foreach from=$error item=error}
	<p class="alert alert-error">{$error}</p>
{/foreach}
{if count($workgroups)}
	<table class="table table-framed table-striped">
		<thead>
			<tr>
				<th>Name</th>
				<th>Mailboxes</th>
				<th>Forwards</th>
				<th>Aliases</th>
				<th class="button-column"></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$workgroups item=workgroup}
				<tr>
					<td>{$workgroup.workgroup}</td>
					<td>{$workgroup.mailbox_count}</td>
					<td>{$workgroup.forward_count}</td>
					<td>{$workgroup.alias_count}</td>
					<td>
						{if $workgroup.mailbox_count == 0 && $workgroup.forward_count == 0 && $workgroup.alias_count == 0}
							<form action="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups" method="post">
								<input type="hidden" name="modaction" value="delete-workgroup" />
								<input type="hidden" name="workgroup" value="{$workgroup.workgroup}" />
								<input class="btn" type="submit" name="submit" value="Delete" title="Delete Workgroup" />
							</form>	
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<div class="alert alert-info">There are no mailboxes</div>
{/if}