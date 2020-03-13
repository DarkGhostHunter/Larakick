<?php

return [

    // If we enable history to rollback changes
    'history' => false,

    // Max history rollbacks
    'max' => 10,

    // Were we will put the files backed up.
    // Files here are all in a directory called app saved as `larakick_2020-01-01_00-00-00` with
    // a fresh copy of "app", "database" and "routes" directories.
    'path' => storage_path('larakick')

];
