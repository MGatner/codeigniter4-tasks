<?php namespace CodeIgniter\Tasks;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and ran periodically.
 *
 * @package CodeIgniter\Tasks
 */
class Task
{
    use FrequenciesTrait;

    /**
     * The command, shell command, or Closure
     * that should be ran.
     *
     * @var mixed
     */
    protected $task;

    /**
     * The task type, either 'callable', 'command', or 'shell'
     *
     * @var string
     */
    protected $taskType;

    /**
     * The crontab expression for this task
     *
     * @var string
     */
    protected $expression = '* * * * *';

    /**
     * The timezone the event should be evaluated in.
     *
     * @var string
     */
    protected $timezone;

    /**
     * If not empty, lists the allowed environments
     * this can run in.
     *
     * @var array
     */
    protected $environments = [];

    public function __construct($task, string $taskType)
    {
        $this->task = $task;
        $this->taskType = $taskType;
    }

    public function run()
    {

    }

    public function shouldRun()
    {

    }
}
