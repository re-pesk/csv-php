<?php
// It overrides some default option values.
// Note that the values passed in command line will overwrite the ones below.
$commandLine = $this->commandLine();
// $commandLine->option('ff', 'default', 1);
$commandLine->option('coverage', 'default', 4);
$commandLine->option('reporter', 'default', 'verbose');
