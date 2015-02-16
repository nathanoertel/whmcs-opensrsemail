<style type="text/css">{$css}</style>
<div class="navbar">
	<a class="btn btn-info" href="http://mail.{$domain}" title="{$lang.logintowebmail}" target="_blank">{$lang.logintowebmail}</a>
	<a class="btn btn-info" href="modules/servers/opensrsemail/Email_Client_Configuration.pdf" title="{$lang.setupinstructions}" target="_blank">{$lang.setupinstructions}</a>
</div>
<div class="navbar">
	{if $addMailbox}
		<a class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=mailbox" title="{$lang.addmailbox}">{$lang.addmailbox}</a>
	{else}
		<a class="btn btn-success" href="upgrade.php?type=configoptions&id={$serviceid}" title="{$lang.addmailbox}">{$lang.addmailbox}</a>
	{/if}
	{if $addForward}
		<a class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=forward" title="{$lang.addforward}">{$lang.addforward}</a>
	{else}
		<a class="btn btn-success" href="upgrade.php?type=configoptions&id={$serviceid}" title="{$lang.addforward}">{$lang.addforward}</a>
	{/if}
	{if $addAlias}
		<a class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type=alias" title="{$lang.addalias}">{$lang.addalias}</a>
	{/if}
	<a class="btn btn-primary" href="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups" title="{$lang.editworkgroups}">{$lang.editworkgroups}</a>
</div>
{if $addedMailbox}
	<p class="alert alert-success">{$lang.mailboxaddsuccess}</p>
{/if}
{if $editedMailbox}
	<p class="alert alert-success">{$lang.mailboxeditsuccess}</p>
{/if}
{if $deleteMailbox}
	<p class="alert alert-danger">{$lang.mailboxdeleterequired}</p>
{/if}
{if $deleteForward}
	<p class="alert alert-danger">{$lang.forwarddeleterequired}</p>
{/if}
{if $deleteSuccess}
	<p class="alert alert-success">{$lang.mailboxdeletesuccess}</p>
{/if}
{foreach from=$error item=error}
	<p class="alert alert-error">{$error}</p>
{/foreach}
{if count($mailboxes)}
	<table class="table table-framed table-striped">
		<thead>
			<tr>
				<th>{$lang.name}</th>
				<th>{$lang.type}</th>
				<th>{$lang.workgroup}</th>
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
							<a class="btn btn-primary" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&mailbox={$mailbox.mailbox}&workgroup={$mailbox.workgroup}&type={$mailbox.type}" title="{$lang.edit}">{$lang.edit}</a>
						{/if}
					</td>
					<td>
						<form action="clientarea.php?action=productdetails&id={$serviceid}" method="post">
							<input type="hidden" name="modaction" value="delete-mailbox" />
							<input type="hidden" name="mailbox" value="{$mailbox.mailbox}" />
							<input class="btn btn-danger" type="submit" name="submit" value="{$lang.delete}" title="{$lang.delete}" />
						</form>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<div class="alert alert-info">{$lang.nomailboxes}</div>
{/if}