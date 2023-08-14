<?php

/**
 * @copyright  (C) 2023 Dimitrios Grammatikogiannis
 * @license    GNU General Public License version 3 or later
 */

namespace Dgrammatiko\Plugin\Console\Responsive\Extension;

defined('_JEXEC') || die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Registry\Registry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCommand extends AbstractCommand
{
  protected static $defaultName = 'responsive:build';
  private $symfonyStyle;

  protected function doExecute(InputInterface $input, OutputInterface $output): int
  {
    $this->symfonyStyle = new SymfonyStyle($input, $output);

    $this->symfonyStyle->title('Building the responsive images...');

    // Check if the plugins are installed/enabled
    if (!PluginHelper::isEnabled('content', 'responsive')) {
      $this->symfonyStyle->error('Plugin "Responsive Images" is not installed/enabled. Check your setup!');
      return 1;
    }
    if (!PluginHelper::isEnabled('filesystem', 'local')) {
      $this->symfonyStyle->error('Plugin "Local" is not installed/enabled. Check your setup!');
      return 1;
    }

    $fs         = PluginHelper::getPlugin('filesystem', 'local');
    $resp       = PluginHelper::getPlugin('content', 'responsive');
    $fsParams   = new Registry($fs->params);
    $respParams = new Registry($resp->params);
    $dirsObj    = $fsParams->get('directories');
    $sizes      = array_map('trim', array_filter(explode(',', $respParams->get('sizes', '320,768,1200')), 'trim'));

    foreach($dirsObj as $name => $dirObj) {
      $this->processFolderRecursively($dirObj->directory, $sizes);
    }

    $this->symfonyStyle->success('Done succesfully!');

    return 0;
  }

  protected function configure(): void
  {
    $this->setDescription('This command will create the source sets for all the images in the media directory');
    $this->setHelp(
      <<<EOF
The <info>%command.name%</info> command will create the source sets for all the images in the media directory
<info>php %command.full_name%</info>
EOF
    );
  }

  private function processFolderRecursively($dir, $sizes): void
  {
    $localDir = JPATH_ROOT . '/' . $dir;

    // Bail out if the library isn't loaded, directory doesn't exist
    if (!is_dir($localDir) || !class_exists('\Ttc\Freebies\Responsive\Helper')) return;

    $directory = new \RecursiveDirectoryIterator($localDir);
    $directory->setFlags(\RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator  = new \RecursiveIteratorIterator($directory);

    // Handle all files in this folder and all sub-folders
    foreach ($iterator as $file) {
      if ($file->isDir() || !in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'])) continue;
      $localFile = str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', $file->getRealPath());
      $this->symfonyStyle->write('Processing: "' . $localFile . '"');
      $this->symfonyStyle->newLine();
      try {
        (new \Ttc\Freebies\Responsive\Helper)->transformImage('<img src="' . $localFile . '" />', $sizes);
      } catch(\Exception $e) {
        throw new \Error('Ooops...');
      }
    }
  }
}
