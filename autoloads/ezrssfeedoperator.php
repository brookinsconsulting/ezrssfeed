<?php	 		 	
//
// Definition of eZRSSFeedOperator class
//
// Created on: <27-Mar-2003 13:43:10 oh>
//
// Copyright (C) 1999-2003 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "eZ publish professional licences" may use this
// file in accordance with the "eZ publish professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" is available at
// http://ez.no/home/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

/*! \file eZRSSFeedOperator.php
*/

include_once( "lib/ezutils/classes/ezdebug.php" );

/*!
  \class eZRSSFeedOperator eZRSSFeedoperator.php
  \brief The class eZRSSFeedOperator does

*/
class eZRSSFeedOperator
{
    /*!
     Initializes the object with the name $name, default is "ezrss".
    */
    function eZRSSFeedOperator( $name = "ezrssfeed" )
    {
        eZDebug::createAccumulatorGroup( 'rssfeed_total', 'eZRSSfeed extension Total' );
	$this->Operators = array( $name );
    }

    /*!
Returns the template operators.
    */
    function &operatorList()
    {
        return $this->Operators;
    }

    function namedParameterList()
    {
        return array( 'rss_url' => array( 'type' => 'string',
                                           'required' => true,
                                           'default' => false ),
		      'rss_items' => array( 'type' => 'integer',
		                            'required' => false,
					    'default' => 4),
		      'rss_channeldata' => array( 'type' => 'boolean',
						  'required' => false,
						  'default' => false),
		      'rss_imagedata' => array( 'type' => 'boolean',
		                                'required' => false,
			                        'default' => false)
		    );
    }

    function getNodes( $dom, $node, $limit = 'disabled', $uniqeNode = 0 )
    {
        $elements =& $dom->elementsByName( $node );
        $i=0;

        foreach( $elements as $element )
        {
            // Is the limit disabled or is it reached?
            if ( $limit != 'disabled' and $limit <= $i )
                break;

            // Loop through every child and grab the content
            foreach ( $element->children() as $childNode )
            {
                if ( $uniqeNode == 0 ) {
                    $elementArray[$i][$childNode->name()]['content'] = $childNode->textContent();
                }
                else {
                    $elementArray[$childNode->name()]['content'] = $childNode->textContent();
                }
            }
            $i++;
        }
        return( $elementArray );
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        include_once( "lib/ezxml/classes/ezxml.php" );

        eZDebug::accumulatorStart( 'rssfeed_total' );
        eZDebug::accumulatorStart( 'rssfeed_load', 'rssfeed_total', 'RSSfeed load' );

        // Open file
        $fp = fopen( $namedParameters['rss_url'], "r" );
        if ( !$fp )
        {
            return false;
        }
	
	$rssFileContent = "";

        // Get the content of the file
        while( !feof( $fp ) )
        {
            $rssFileContent .= fread( $fp, 1024 );
        }
        fclose( $fp );

        eZDebug::accumulatorStop( 'rssfeed_load' );
        eZDebug::accumulatorStart( 'rssfeed_createdom', 'rssfeed_total', 'RSSfeed create DOM' );

        // New xml parser.
        $xml = new eZXML();
        $dom =& $xml->domTree( $rssFileContent );
        if ( !$dom )
        {
            return false;
        }

        // Get <channel> information
        if ( $namedParameters['rss_channeldata'] == 'enabled' )
            $rssData['channel'] = $this->getNodes( $dom, 'channel', 'disabled', 1 );

        // Get <image> information
        if ( $namedParameters['rss_imagedata'] == 'enabled' )
            $rssData['image'] = $this->getNodes( $dom, 'image', 'disabled', 1 );

        // Get every <item>
        $rssData['items'] = $this->getNodes( $dom, 'item', $namedParameters['rss_items'], 0 );

        // Generate timestamp
        foreach ( $rssData['items'] as $index => $item )
        {
            $date = false;
            if (isset( $item['pubDate']['content'] ) )
                $date = $item['pubDate']['content'];
            if (isset( $item['date']['content'] ) )
                $date = $item['date']['content'];

            $rssData['items'][$index]['timestamp'] = $this->getTimestamp( $date );
        }

        // Return data
        $operatorValue = $rssData;
        eZDebug::accumulatorStop( 'rssfeed_createdom' );
        eZDebug::accumulatorStop( 'rssfeed_total' );
    }

    function formatRSSDate( $params )
    {
        list($a, $s, $h, $m) = $params;
        return $s . ((int)(($h * 60 + $m) / 60));
    }

    function getTimestamp( $dateStr )
    {
        /* Compensate for strototime's missing ability to parse some date formats */
        if( preg_match("@[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}@", $dateStr ) )
        {
            $dateStr = str_replace( array( 'T', '+' ), array( ' ', ' +' ), $dateStr );
            $dateStr = preg_replace_callback( '@([+-])([0-9]{2}):([0-9]{2})@', array( $this, 'formatRSSDate') , $dateStr );
            $timestamp = strtotime( $dateStr );
        }
        else
        {
            $timestamp = strtotime( $dateStr );
        }
        return $timestamp;
    }


    var $Operators;
}
?>
