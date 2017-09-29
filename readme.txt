----------------------------------
eZRSS operator for eZ publish 3.5+
        Kristian Hole
----------------------------------

Originally written by: 
Ole Morten Halvorsen

Installation
------------
1. Extract the ezrssfeed.tar.gz in your eZ publish 3 extension directory. 

2. Activate the extension. 
   Either in adminitration interface:
     Go to "Setup"
     Go to "Extensions"
     Check the checkbox beside "ezrssfeed"
     Click "Activate"
   OR in ini-files:
     Add the following to your settings/override/site.ini.append.php file
     [ExtensionSettings]
     ActiveExtensions[]=ezrssfeed


Usage
-----

The extension can be used in two ways. Either as a toolbar or as an template operator. It
can also be used as an example of how to create a toolbar.

1) Toolbar
As an example of usage there is a toolbar available. Once the extension has been installed
and activated there will be a "rssfeed" toolbar available in the administration interface. 
The toolbar makes it easy to add rssfeeds to your page. The settings are self explaining.

2) Template operator
For more flexibility the operator can be used in your templates. The ezrss operator 
takes at a minimum one argument, the url of the RSS-feed.

The available parameters are:
url: 
  The url of the feed to download
  This parameter is required.
items: 
  The number of items to return (set to 0 for all items) 
  Default value: 4
channeldata: 
  Return channel data?
  Default value: False
imagedata: 
  Return rssfeed image data?
  This might not be avaiable in all feeds.
  Default value: False
  
Example: {let slashdot=ezrssfeed("http://slashdot.org/index.rss", 4, true(), false()}

This will load the feed from the given url, return the 4 first items, with channeldata, 
without imagedata, and stor this in the variable slashdot.

Making sure the ezrssfeed is inside a {cache-block} is a _very_ good idea, this will increase
the performance of the site, and you won't download the feed everytime someone loads you page. 
Some sites may ban you for a period of time if you request the feeds to often.

A complete example using the operator:

{cache-block expiry=300}
  {let feed=ezrssfeed("http://slashdot.org/index.rss", 10, true(), true() )}
     <h2>
       <a href="{$feed.image.link.content}"><img src="{$feed.image.url.content}" alt="{$feed.image.title.content}"></a>
       <a href="{$feed.channel.link.content}">{$feed.channel.title.content}</a>
     </h2>
    <ul>
    {section var=theitem loop=$feed.items}
      <li><a href="{$theitem.link.content}">{$theitem.title.content}</a></li>
      <p>{$theitem.description.content|shorten(80)}</p>
    {/section}
    </ul>
  {/let}
{/cache-block} 

The cacheblock has expiry of 300 seconds, which means the feed will be regenerated every 5 minutes.


If you want to see the data eZRSS returns put the following in a template:
{let freshmeat_data=ezrssfeed("http://download.freshmeat.net/backend/fm-releases-unix.xml", 0, true(), true() )}
     {$freshmeat_data|attribute(show)}
{/let}

Here we call ezrssfeed with a rssfeed url as a argument. The number of items is set to 0 which means every
item in the feed. And we download both the channeldata an

 It will then open the url, parse it and return
the data. The attribute(show) displays all available data.

Changelog
---------

v1.1 ( 2006-08-21 )
 - Added generation of timestamps for each item
 - Fixed broken parsing of some timestamps from the rss
 - Fixed some typos in the readme

v1.0 ( 2005-02-13 )
 - Initial release
