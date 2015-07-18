<style type="text/css">{$css}</style>
<div class="page-header nav-header">
	<h1>{$lang.addalias}</h1>
</div>
{foreach from=$error item=e}
	<p class="alert alert-danger">{$e}</p>
{/foreach}
<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox&type={$type}" class="form-stacked" method="post">
	<input type="hidden" name="modaction" value="save-alias" />
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<label class="control-label" for="alias">{$lang.aliasname}</label>
				<div class="control">
					<input class="form-control" type="text" name="alias" value="{$alias}" />
				</div>
			</div>
			<div class="form-group">
				<label class="control-label" for="mailbox">{$lang.mailboxname}</label>
				<div class="control">
					<select class="form-control" name="mailbox">
						{foreach from=$mailboxes item=mailbox}
							<option value="{$mailbox.mailbox}">{$mailbox.mailbox}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-primary" type="submit">{$lang.save}</button>
	<a class="btn btn-danger" href="clientarea.php?action=productdetails&id={$serviceid}">{$lang.cancel}</a>
</form>