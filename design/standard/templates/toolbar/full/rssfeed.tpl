<div class="toolbar-item {$placement}">
    <div class="toolbox">
        <div class="toolbox-design">

{* default expiry of 10 minutes *}
{cache-block expiry=600}
  {let feed=ezrssfeed($rssurl, $rssitems, $rssimage_check|eq('yes'), $rsschannel_check|eq('yes') )}
  {section show=$feed}
        {section show=$feed.channel}
	  <h2>{$feed.channel.title.content}</h2>
        {section-else}
	   <h2>{$rsstitle}</h2>
        {/section}

        <div class="toolbox-content">

        {section show=$feed.image}
          <a href="{$feed.image.link.content}"><img src="{$feed.image.url.content}" alt="{$feed.image.title.content}"></a>
        {/section}
       <ul>
       {section var=theitem loop=$feed.items}
         <li><a href="{$theitem.link.content}">{$theitem.title.content}</a></li>
           {section show=$rssdesclength|gt(0)}
             <p>{$theitem.description.content|shorten($rssdesclength)}</p>
           {section-else}
             <p>{$theitem.description.content}</p>
           {/section}
       {/section}
       </ul>

       </div>

  {section-else}
    <h2>Error loading RSS.</h2>
  {/section}
  {/let}
{/cache-block}

        </div>
    </div>
</div>
