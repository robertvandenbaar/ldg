<?php

use Ldg\Search;
use Ldg\Setting;
use Ldg\Model\Image;

define('BASE_DIR', dirname(__FILE__));

require 'app/vendor/autoload.php';

if (php_sapi_name() != 'cli')
{
	die('This file should be run on the commandline');
}

echo "\n\nStart\n";
echo "--------------------------\n";

// reset search index
$search = new Search();
$search->resetIndex();

function createCroppedImagesRecursively($baseDir, $search)
{
	foreach (scandir($baseDir) as $file)
	{
		if(substr($file, 0, 1) == '.')
		{
			continue;
		}

		$fullPath = $baseDir . '/' . $file;

		if (is_dir($fullPath))
		{
			createCroppedImagesRecursively($fullPath, $search);
			continue;
		}

		$image = new Image($fullPath);

		if (in_array($image->getExtension(), Setting::get('supported_extensions')))
		{
			echo "Updating thumbnail and mid-size image for " . $fullPath . "\n";

			// to speed up the cron process the detail images are not generated if the default option is full size
			if (!Setting::get('full_size_by_default'))
			{
				$image->updateDetail();
			}
			$image->updateThumbnail();
			$image->updateIndex($search);
		}
	}
}

createCroppedImagesRecursively(Setting::get('image_base_dir'), $search);
$search->save();

echo "\n\nFinished\n";
echo "--------------------------\n";
echo "Make sure you set the correct permissions on the cache folder (or use sudo -u)\n";
echo "and all its subfolders and files so the user the apache\n";
echo "process runs on is able to write to the folder\n";
