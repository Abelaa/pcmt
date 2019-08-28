<?php
declare(strict_types=1);

namespace Pcmt\Bundle\Command;

use Pcmt\Bundle\Helper\GsCodesHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Exception\Exception;


class PcmtCreateAllAttributes extends ContainerAwareCommand
{
  /**
   * run inside terminal in fpm docker: bin/console $defaultName
   */
  protected static $defaultName = 'pcmt:generate-ref-data-attr-all';

  public function configure()
  {
    parent::configure();
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $codeList = GsCodesHelper::getGsCodes();
    $output->writeln([
      'All Reference Data Attribute Creator',
      '============',
    ]);
    foreach ($codeList as $code) {
      // create as Attribute
      try {
        $command = $this->getApplication()->find('pcmt:generate-ref-data-attr');
        $arguments = [
          'command' => 'pcmt:generate-ref-data-attr',
          'ref-data-name' => $code
        ];
        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);
      } catch (Exception $e) {
        $output->writeln($e);
      }
    }
    $output->writeln('done');
  }

}