<?php // $Revision$

/************************************************************************/
/* phpAdsNew 2                                                          */
/* ===========                                                          */
/*                                                                      */
/* Copyright (c) 2001 by the phpAdsNew developers                       */
/* http://sourceforge.net/projects/phpadsnew                            */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/



// Include required files
require ("config.php");
require ("lib-storage.inc.php");
require ("lib-zones.inc.php");
require ("lib-statistics.inc.php");


// Security check
phpAds_checkAccess(phpAds_Admin);



/*********************************************************/
/* Main code                                             */
/*********************************************************/

if (isset($bannerID) && $bannerID != '')
{
	// Cleanup webserver stored image
	$res = phpAds_dbQuery("
		SELECT
			banner, format
		FROM
			$phpAds_tbl_banners
		WHERE
			bannerID = $bannerID
		") or phpAds_sqlDie();
	if ($row = phpAds_dbFetchArray($res))
	{
		if ($row['format'] == 'web' && $row['banner'] != '')
			phpAds_Cleanup (basename($row['banner']));
	}
	
	// Delete banner
	$res = phpAds_dbQuery("
		DELETE FROM
			$phpAds_tbl_banners
		WHERE
			bannerID = $bannerID
		") or phpAds_sqlDie();
	
	// Delete banner ACLs
	$res = phpAds_dbQuery("
		DELETE FROM
			$phpAds_tbl_acls
		WHERE
			bannerID = $bannerID
		") or phpAds_sqlDie();
	
	// Delete statistics for this banner
	phpAds_deleteStats($bannerID);
}

// Rebuild zone cache
if ($phpAds_zone_cache)
	phpAds_RebuildZoneCache ();

Header("Location: campaign-index.php?campaignID=$campaignID&message=".urlencode($strBannerDeleted));

?>
