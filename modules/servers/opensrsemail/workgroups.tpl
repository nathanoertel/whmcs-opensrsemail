<style type="text/css">{$css}</style>
<div class="navbar">
	<a class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=normal&modaction=workgroup" title="{$lang.addworkgroup}">{$lang.addworkgroup}</a>
	<a class="btn btn-primary" href="clientarea.php?action=productdetails&id={$serviceid}" title="{$lang.editmailboxes}">{$lang.editmailboxes}</a>
</div>
{if $deleteSuccess}
	<p class="alert alert-success">{$lang.workgroupdeletesuccess}</p>
{/if}
{if $addSuccess}
	<p class="alert alert-success">{$lang.workgroupaddsuccess}</p>
{/if}
{foreach from=$error item=error}
	<p class="alert alert-danger">{$error}</p>
{/foreach}
{if count($workgroups)}
	<table class="table table-framed table-striped">
		<thead>
			<tr>
				<th>{$lang.name}</th>
				<th>{$lang.mailboxes}</th>
				<th>{$lang.forwards}</th>
				<th>{$lang.aliases}</th>
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
								<input class="btn btn-danger" type="submit" name="submit" value="{$lang.delete}" title="{$lang.delete}" />
							</form>	
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<div class="alert alert-info">{$lang.noworkgroups}</div>
{/if}