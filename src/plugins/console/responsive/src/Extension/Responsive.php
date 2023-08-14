<?php

/**
 * @copyright  (C) 2023 Dimitrios Grammatikogiannis
 * @license    GNU General Public License version 3 or later
 */

namespace Dgrammatiko\Plugin\Console\Responsive\Extension;

\defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Application\ApplicationEvents;
use Dgrammatiko\Plugin\Console\Responsive\Extension\RunCommand;

final class Responsive extends CMSPlugin implements SubscriberInterface
{
  public static function getSubscribedEvents(): array
  {
    return [ApplicationEvents::BEFORE_EXECUTE => 'registerCommands'];
  }

  public function registerCommands(): void
  {
    $this->getApplication()->addCommand(new RunCommand());
  }
}
