<?php

namespace App\DI;

use DI\ContainerBuilder;

class ExtendedContainerBuilder extends ContainerBuilder
{
  /**
   * Adds the definitions from given file in the "Settings" folder, if exists.
   */
  public function addDefinitionFromFile(string $filename, string $settingsFolderName = "Settings"): bool
  {
    $settingsFolder = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), $settingsFolderName]);
    $settingsFile = $settingsFolder . DIRECTORY_SEPARATOR . $filename;
    $addDefinitions = file_exists($settingsFile);
    if ($addDefinitions) {
      $this->addDefinitions($settingsFile);
    }
    return $addDefinitions;
  }

  /**
   * Adds the definitions from given file in the "Settings" folder, if exists.
   * Tries a fallback file, if the first file was not found.
   * This can be used for Production/Development settings.
   */
  public function addDefinitionFromFileWithFallback(string $filename, string $fallbackfile, string $settingsFolderName = "Settings"): bool
  {
    if (! $this->addDefinitionFromFile($filename, $settingsFolderName)) {
      return $this->addDefinitionFromFile($fallbackfile, $settingsFolderName);
    }
    return true;
  }
}
