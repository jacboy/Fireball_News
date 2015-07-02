{if $templateNameApplication === 'cms' && $templateName === 'newsAdd' && CMS_NEWS_COMMENTS}
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" id="enableComments" name="enableComments"{if $enableComments} checked="checked"{/if} />
			{lang}cms.news.comments.enable{/lang}
		</label>
		<small>{lang}cms.news.comments.enable.description{/lang}</small>
	</dd>
{/if}
