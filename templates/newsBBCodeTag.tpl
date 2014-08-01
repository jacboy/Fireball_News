<div class="container containerPadding marginTop">
	<div class="containerHeadline">
		<h3><a href="{$_news->getLink()}" data-news-id="{$_news->newsID}" class="newsLink cmsNewsLink" title="{$_news->getTitle()}">{$_news->getTitle()}</a></h3>
		<p><small>
			<span class="username">
				{if $_news->userID != 0}
				<a class="userLink" data-user-id="{$_news->userID}" href="{link controller='User' object=$_news->getUserProfile()}{/link}">
					{$_news->username}
				</a>
				{else}
					{$_news->username}
				{/if}
			</span>
			-
			<a class="permalink" href="{link controller='News' object=$_news application='cms'}{/link}">
				{@$_news->time|time}
			</a>
			-
			<span>
				{implode from=$_news->getCategories() item=category}<a href="{link controller='NewsList' application='cms' object=$category}{/link}">{$category->getTitle()|language}</a>{/implode}
			</span>
		</small></p>
	</div>
	{if CMS_NEWS_NEWS_IMAGES_ATTACHED && $_news->imageID != 0}
		<div class="newsBox128">
			<div class="framed">
				<img src="{@$_news->getImage()->getURL()}" alt="{$_news->getImage()->title}" style="width: 128px;" />
			</div>
			<div class="newsTeaser">
				{if $_news->teaser != ""}<strong>{$_news->teaser}</strong>{else}{@$_news->getExcerpt()}{/if}
			</div>
		</div>
	{else}
		<div class="newsTeaser">
			{if $_news->teaser != ""}<strong>{$_news->teaser}</strong>{else}{@$_news->getExcerpt()}{/if}
		</div>
	{/if}
</div>
