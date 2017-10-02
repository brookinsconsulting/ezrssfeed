# eZ RSS Feed

The eZRSSFeed extension contains a configurable rssfeed toolbar for displaying rssfeeds on your site. It also contains a rssfeed template operator for fetching rssfeeds into a template

Version
=======

* The current version of eZ RSS Feed is 0.1.2

* Last Major update: October 02, 2017


Copyright
=========

* eZ RSS Feed is copyright 1999 - 2017 Brookins Consulting

* See: [COPYRIGHT.md](COPYRIGHT.md) for more information on the terms of the copyright and license

License
=======

eZ RSS Feed is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE.md) file.

eZ RSS Feed is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License or at your
option a later version.

eZ RSS Feed is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
eZ RSS Feed under certain conditions. The GNU GPL license
is distributed with the software, see the file [LICENSE](LICENSE.md).

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with eZ RSS Feed in in the [LICENSE](LICENSE.md) file.

If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using eZ RSS Feed under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


Requirements
============

The following requirements exists for using eZ RSS Feed extension:


### eZ Publish version

* Make sure you use eZ Publish version 5.x (required) or higher.

* Designed and tested with eZ Publish Community Project 2017.1


### PHP version

* Make sure you have PHP 5.x or higher.


Features
========

### Toolbar

As an example of usage there is a toolbar available

### Operator

This solution provides the following eztpl operator:

* Operator: `ezrssfeed`

### Dependencies

* This solution depends on eZ Publish Legacy


Use case
========

This solution was created to provide a simple way to fetch and display rss feed content within your own website templates.

# Installation

## Extension Installation via Composer

Run the following command from your project root to install the extension:

    ```bash
    $ composer require brookinsconsulting/ezrssfeed dev-master;```

## Extension Installation via Manual

Extract the ezrssfeed.tar.gz in your eZ Publish Legacy extension directory. 

## Activate the extension

### Activate the extension (via Admin)
   Either in adminitration interface:
     Go to "Setup"
     Go to "Extensions"
     Check the checkbox beside "ezrssfeed"
     Click "Activate"

### Activate the extension (via ini-files):
     Add the following to your settings/override/site.ini.append.php file:
```
     [ExtensionSettings]
     ActiveExtensions[]=ezrssfeed
```

# Usage

The extension can be used in two ways. Either as a toolbar or as an template operator. It can also be used as an example of how to create a toolbar.

## Toolbar
As an example of usage there is a toolbar available. Once the extension has been installed and activated there will be a "rssfeed" toolbar available in the administration interface. 

The toolbar makes it easy to add rssfeeds to your page. The settings are self explaining.

## Template operator

For more flexibility the operator can be used in your templates. The ezrss operator takes at a minimum one argument, the url of the RSS-feed.

The available parameters are:
* url: 
  The url of the feed to download
  This parameter is required.
* items: 
  The number of items to return (set to 0 for all items) 
  Default value: 4
* channeldata: 
  Return channel data?
  Default value: False
* imagedata: 
  Return rssfeed image data?
  This might not be avaiable in all feeds.
  Default value: False
  
### Example Usage
```
{let slashdot=ezrssfeed("http://slashdot.org/index.rss", 4, true(), false()}
```

This will load the feed from the given url, return the 4 first items, with channeldata,  without imagedata, and store this in the variable slashdot.

## Cache blocks

Making sure the ezrssfeed is inside a {cache-block} is a _very_ good idea, this will increase the performance of the site, and you won't download the feed everytime someone loads you page. 

Some sites may ban you for a period of time if you request the feeds to often.

A complete example using the operator:
```
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
```
The cacheblock has expiry of 300 seconds, which means the feed will be regenerated every 5 minutes.


If you want to see the data eZRSS returns put the following in a template:
```
{let freshmeat_data=ezrssfeed("http://download.freshmeat.net/backend/fm-releases-unix.xml", 0, true(), true() )}
     {$freshmeat_data|attribute(show)}
{/let}
```
Here we call ezrssfeed with a rssfeed url as a argument. The number of items is set to 0 which means every item in the feed. 

And we download both the channeldata and it will then open the url, parse it and return the data. The ```attribute(show)``` displays all available data.


Testing
=====

The solution is configured to work once properly installed and configured.


Troubleshooting
===============

### Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md)


### Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)


# Changelog

v1.2 (2017-10-02)
  - Replaced eZXML library usage with DOMDocument for eZ Publish 5 Compatability. (5.x)

v1.1 ( 2006-08-21 )
 - Added generation of timestamps for each item
 - Fixed broken parsing of some timestamps from the rss
 - Fixed some typos in the readme

v1.0 ( 2005-02-13 )
 - Initial release (3.x)
 
# Credits

- Updated By: Brookins Consulting
- Released By: Kristian Hole
- Originally written by: Ole Morten Halvorsen
