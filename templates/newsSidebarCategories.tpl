
	{if CMS_NEWS_SIDEBAR_CATEGORIES}
		<fieldset>
			<legend>{lang}cms.news.category.categories{/lang}</legend>

			<ol class="sidebarNestedCategoryList newsSidebarCategoryList">
			{foreach from=$categoryList item=categoryItem}
				{if $categoryItem->isAccessible()}
				<li{if $category|isset && $category->categoryID == $categoryItem->categoryID} class="active"{/if}>
					<a href="{link application='cms' controller='NewsList' object=$categoryItem->getDecoratedObject()}{/link}">{$categoryItem->getTitle()}</a>
					{if $categoryItem->getUnreadNews()}<span class="badge badgeUpdate">{#$categoryItem->getUnreadNews()}</span>{else}<span class="badge">{#$categoryItem->getNews()}</span>{/if}
					{if $categoryItem->hasChildren()}
						<ol>
							{foreach from=$categoryItem item=subCategoryItem}
								{if $subCategoryItem->isAccessible()}
								<li{if $category|isset && $category->categoryID == $subCategoryItem->categoryID} class="active"{/if}>
									<a href="{link application='cms' controller='NewsList' object=$subCategoryItem->getDecoratedObject()}{/link}">{$subCategoryItem->getTitle()}</a>
									<span class="badge">{#$subCategoryItem->getNews()}</span>
								</li>
								{/if}
							{/foreach}
						</ol>
					{/if}
				</li>
				{/if}
			{/foreach}
		</ol>
		</fieldset>
	{/if}

