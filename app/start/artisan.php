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

Artisan::add(new AuthoritiesImport);
Artisan::add(new AuthoritiesDelete);
Artisan::add(new AuthoritiesDirty);
Artisan::add(new AuthoritiesUnDirty);
Artisan::add(new GraphResetCommand);
