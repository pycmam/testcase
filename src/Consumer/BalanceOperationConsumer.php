<?php

namespace App\Consumer;

use App\Exception\AbortOperationExceptionInterface;
use App\Exception\RetryOperationExceptionInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Psr\Log\LoggerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use App\Factory\BalanceOperationFactory;

class BalanceOperationConsumer implements ConsumerInterface
{
    protected $logger;
    protected $operationFactory;
    protected $id;


    /**
     * BalanceOperationConsumer constructor.
     *
     * @param LoggerInterface         $logger
     * @param BalanceOperationFactory $factory
     */
    public function __construct(LoggerInterface $logger, BalanceOperationFactory $factory)
    {
        $this->logger = $logger;
        $this->operationFactory = $factory;
        $this->id = uniqid();

        $this->logger->info("Consumer {$this->id} started");
    }


    /**
     * @param AMQPMessage $message
     *
     * @return bool|mixed
     */
    public function execute(AMQPMessage $message)
    {
        $payload = json_decode($message->getBody(), true);

        try {

            $operation = $this->operationFactory->create($payload['operation']);

            $result = $operation
                ->bindLockId(getmypid())
                ->bindParams($payload['params'])
                ->execute();

            $this->logger->debug(sprintf('%s | %s: %s %s', $this->id,
                $result ? 'Success' : 'Failed', $payload['operation'], json_encode($payload['params'])));

            return $result;
        } catch (AbortOperationExceptionInterface $exception) {

            $this->logger->error(sprintf('%s | Aborted: %s, Error: %s (%s)',
                $this->id, $payload['operation'], get_class($exception), $exception->getMessage()));

            return true; // отдаем ack=true, чтобы не ставить задачу обратно в очередь

        } catch (\Exception | RetryOperationExceptionInterface $exception) {

            $this->logger->error(sprintf('%s | Returned to queue: %s, Error: %s (%s)',
                $this->id, $payload['operation'], get_class($exception), $exception->getMessage()));

            return false; // отдаем ack=false, возвращаем задачу в очередь
        }
    }
}