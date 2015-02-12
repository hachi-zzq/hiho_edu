<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new CleanSubtitleFT);
Artisan::add(new UpdateSolrDocument);
Artisan::add(new UpdateAllPlayId);
Artisan::add(new UpdateServerIp);
Artisan::add(new VideoTitleCorrect);
Artisan::add(new MigrateCategoryAndDepartment);
Artisan::add(new CleanUserTestData());
Artisan::add(new Init_admin_data());