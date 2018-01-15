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
Artisan::add(new AuthoritiesCorrectMarcId);
Artisan::add(new AmelibAddIdentifierId);
Artisan::add(new DrylImportMagazines);
Artisan::add(new DrylImportBooks);
Artisan::add(new DrylCompletionLevel);
Artisan::add(new DrylCorrectItemLocation);
Artisan::add(new DrylCorrectItemDate);
Artisan::add(new DrylCorrectOwner);
Artisan::add(new GraphResetCommand);
Artisan::add(new TestrgDeleteTestVertices);
Artisan::add(new LogInfoCommand);
Artisan::add(new GraphDumpCommand);
Artisan::add(new GraphShowNodeCommand);
Artisan::add(new GraphShowNodeNeigbourhood);
Artisan::add(new KohaProcessTransactions);
Artisan::add(new WorkerSample);
Artisan::add(new WorkerEdit);
Artisan::add(new ClientSample);
