<style type="text/css">{$css}</style>
<div class="page-header nav-header">
	<h1>{$lang.addworkgroup}</h1>
</div>	
{foreach from=$error item=error}
	<p class="alert alert-danger">{$error}</p>
{/foreach}
<form action="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=mailbox" class="form-stacked" method="post">
	<input type="hidden" name="modaction" value="save-workgroup" />
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<label class="control-label" for="workgroup">{$lang.workgroup}</label>
				<div class="control">
					<input class="form-control" type="text" name="workgroup" value="{$workgroup.workgroup}" />
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-primary" type="submit">{$lang.save}</button>
	<a class="btn btn-danger" href="clientarea.php?action=productdetails&id={$serviceid}&modaction=workgroups">{$lang.cancel}</a>
</form>