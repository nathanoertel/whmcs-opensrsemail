<style type="text/css">{$css}</style>
<div class="page-header nav-header">
	{if $new}
		<h1>{$lang.addforward}</h1>
	{else}
		<h1>{$lang.editforward}</h1>
	{/if}
</div>
{if $deleteRequired}
	<p class="alert alert-danger">{$lang.deleterequired}</p>
{else}
	{foreach from=$error item=e}
		<p class="alert alert-danger">{$e}</p>
	{/foreach}
	{if $mailbox}
		<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox{if !$new}&mailbox={$mailbox.mailbox}&workgroup={$workgroup}{/if}&type={$type}" class="form-stacked" method="post">
			<input type="hidden" name="modaction" value="save-forward" />
			<div class="row">
				<div class="col-sm-12">
					{if $new}
						<input type="hidden" name="new" value="true" />
						<div class="form-group">
							<label class="control-label" for="mailbox">{$lang.mailboxname}</label>
							<div class="control">
								<input class="form-control small" type="text" name="mailbox" value="{$mailbox.mailbox}" /><span class="row-text">@{$domain}</span>
							</div>
						</div>
					{else}
						<div class="form-group">
							<label class="control-label" for="mailbox">{$lang.mailboxname}</label>
							<div class="control">
								<input type="hidden" name="mailbox" value="{$mailbox.mailbox}" />
								<span class="row-text">{$mailbox.mailbox}@{$domain}</span>
							</div>
						</div>
					{/if}
					<div class="form-group">
						<label class="control-label" for="password">{$lang.forwardto}</label>
						<div class="control">
							<input class="form-control" type="text" name="forwardEmail" value="{$mailbox.forward_email}" />
						</div>
					</div>
					{if $new}
						<div class="form-group">
							<label class="control-label" for="workgroup">{$lang.workgroup}</label>
							<div class="control">
								<select class="form-control" name="workgroup">
									{foreach from=$workgroups item=workgroup}
										<option value="{$workgroup.workgroup}" {if $workgroup.workgroup == $mailbox.workgroup}selected="true"{/if}>{$workgroup.workgroup}</option>
									{/foreach}
								</select>
							</div>
						</div>
					{/if}
				</div>
			</div>
			<button class="btn btn-primary" type="submit">{$lang.save}</button>
			<a class="btn btn-danger" href="clientarea.php?action=productdetails&id={$serviceid}">{$lang.cancel}</a>
		</form>
	{/if}
{/if}