<?php
/**
 * Created by PhpStorm.
 * User: rustam
 * Date: 22.03.2018
 * Time: 15:36
 */

namespace App\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

abstract class BaseBalanceCommand extends Command
{
    protected $producer;


    public function __construct(ContainerInterface $container)
    {
        $this->producer = $container->get('old_sound_rabbit_mq.balance_producer');

        parent::__construct();
    }

}
