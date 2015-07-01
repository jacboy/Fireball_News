{include file='documentHeader'}
<head>
	<title>{lang}cms.news.{$action}{/lang} - {lang}cms.page.news{/lang} - {PAGE_TITLE|language}</title>
	{include file='headInclude' application='wcf'}
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS.News.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Search.User('#authors', null, false, [ ], true);

			WCF.Language.addObject({
				//'cms.news.image.select': '{lang}cms.news.image.select{/lang}',
				'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
			});

			//new CMS.News.Image.Form($('#imageSelect'), $('#imageID'));
			new WCF.Category.NestedList();
			new WCF.Message.FormGuard();

			//use acp file picker
			new CMS.ACP.File.Picker($('#filePicker > .button'), 'imageID', {
			{if $image|isset}
				{@$image->fileID}: {
					fileID: {@$image->fileID},
					title: '{$image->getTitle()}',
					formattedFilesize: '{@$image->filesize|filesize}'
				}
			{/if}
		}, { fileType: 'image' });
			new CMS.ACP.File.Preview();
		
			WCF.Message.Submit.registerButton('text', $('#messageContainer > .formSubmit > input[type=submit]'));
		});
		//]]>
	</script>
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}">
{include file='header'}
<header class="boxHeadline">
	<h1>{lang}cms.news.{@$action}{/lang}</h1>
</header>

{include file='userNotice'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

{include file='formError'}

	<form id="messageContainer" class="jsFormGuard" method="post" action="{if $action == 'add'}{link controller='NewsAdd' application='cms'}{/link}{else}{link controller='NewsEdit' application='cms' id=$newsID}{/link}{/if}">
		<div class="container containerPadding marginTop">
			<fieldset>
				<legend>{lang}cms.news.category.categories{/lang}</legend>
				<small>{lang}cms.news.category.categories.description{/lang}</small>
				<ol class="nestedCategoryList doubleColumned jsCategoryList">
				{foreach from=$categoryList item=categoryItem}
					{if $categoryItem->isAccessible()}
					<li>
						<div>
							<div class="containerHeadline">
								<h3><label{if $categoryItem->getDescription()} class="jsTooltip" title="{$categoryItem->getDescription()}"{/if}><input type="checkbox" name="categoryIDs[]" value="{@$categoryItem->categoryID}" class="jsCategory"{if $categoryItem->categoryID|in_array:$categoryIDs}checked="checked" {/if}/> {$categoryItem->getTitle()}</label></h3>
							</div>

							{if $categoryItem->hasChildren()}
								<ol>
									{foreach from=$categoryItem item=subCategoryItem}
										{if $subCategoryItem->isAccessible()}
										<li>
											<label{if $subCategoryItem->getDescription()} class="jsTooltip" title="{$subCategoryItem->getDescription()}"{/if}><input type="checkbox" name="categoryIDs[]" value="{@$subCategoryItem->categoryID}" class="jsChildCategory"{if $subCategoryItem->categoryID|in_array:$categoryIDs}checked="checked" {/if}/> {$subCategoryItem->getTitle()}</label>
										</li>
										{/if}
									{/foreach}
								</ol>
							{/if}
						</div>
					</li>
					{/if}
				{/foreach}
			</ol>
				{if $errorField == 'categoryIDs'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}cms.news.categories.error.{@$errorType}{/lang}
					{/if}
				</small>
				{/if}
				{event name='categoryFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}wcf.global.form.data{/lang}</legend>
				{if $action =='add'}{include file='messageFormMultilingualism'}{/if}

				<dl{if $errorField == 'subject'} class="formError"{/if}>
					<dt><label for="subject">{lang}wcf.global.title{/lang}</label></dt>
					<dd>
						<input type="text" id="subject" name="subject" value="{$subject}" required="required" maxlength="255" class="long" />
						{if $errorField == 'subject'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{elseif $errorType == 'censoredWordsFound'}
									{lang}wcf.message.error.censoredWordsFound{/lang}
								{else}
									{lang}cms.news.subject.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				{if MODULE_TAGGING}{include file='tagInput'}{/if}

				<dl {if $errorField == 'teaser'}class="formError"{/if}>
					<dt><label for="teaser">{lang}cms.news.teaser{/lang}</label></dt>
					<dd>
						<textarea id="teaser" name="teaser" rows="5" cols="40">{$teaser}</textarea>
						<small>{lang}cms.news.teaser.description{/lang}</small>
						{if $errorField == 'teaser'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}cms.news.teaser.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>

				<dl class="newsImageSelect">
					<dt><label for="image">{lang}cms.news.image{/lang}</label></dt>
					<dd>
						<div id="filePicker">							
							<ul class="formAttachmentList clearfix"></ul>
							<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
						</div>
					</dd>
				</dl>

				<dl{if $errorField == 'authors'} class="formError"{/if}>
					<dt><label for="authors">{lang}cms.news.authors{/lang}</label></dt>
					<dd>
						<textarea id="authors" name="authors" class="long" cols="40" rows="2">{$authors}</textarea>
						{if $errorField == 'authors'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}cms.news.authors.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
						<small>{lang}cms.news.authors.description{/lang}</small>
					</dd>
				</dl>

				{event name='informationFields'}
			</fieldset>

			<fieldset class="jsOnly">
					<legend>{lang}cms.news.time.toPublish{/lang}</legend>

					<dl {if $errorField == 'time'} class="formError"{/if}>
						<dt><label for="time">{lang}cms.news.time.toPublish{/lang}</label></dt>
						<dd>
							<input data-ignore-timezone="1" type="datetime" class="medium" id="time" name="time" value="{$time}"/>
						</dd>
					</dl>
			</fieldset>
			<fieldset>
				<legend>{lang}cms.news.message{/lang}</legend>

				<dl class="wide{if $errorField == 'text'} formError{/if}">
					<dt><label for="text">{lang}cms.news.message{/lang}</label></dt>
					<dd>
						<textarea id="text" name="text" rows="20" cols="40">{$text}</textarea>
						{if $errorField == 'text'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{elseif $errorType == 'tooLong'}
									{lang}wcf.message.error.tooLong{/lang}
								{elseif $errorType == 'censoredWordsFound'}
									{lang}wcf.message.error.censoredWordsFound{/lang}
								{else}
									{lang}cms.news.message.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			{event name='messageFields'}
		</fieldset>

		{include file='messageFormTabs' wysiwygContainerID='text'}

		{event name='fieldsets'}
		</div>
		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
			{@SECURITY_TOKEN_INPUT_TAG}
			{include file='messageFormPreviewButton'}
		</div>
	</form>
{include file='footer'}
{include file='wysiwyg'}

</body>
</html>
