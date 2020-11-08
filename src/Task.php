<?php namespace CodeIgniter\Tasks;

/**
 * Class Task
 *
 * Represents a single task that should be scheduled
 * and run periodically.
 */
abstract class Task
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
     * The timezone the event should be evaluated in.
     *
     * @var string
     */
    protected $timezone;

    /**
     * If not empty, lists the allowed environments
     * this can run in.
     *
     * @var string[]
     */
    protected $environments = [];

    /**
     * @param mixed $task
     * @param string $taskType
     */
    public function __construct($task, string $taskType)
    {
        $this->task     = $task;
        $this->taskType = $taskType;
    }

    abstract public function run();

    /**
     * Determines whether this task should be run now
     * according to its schedule, timezone, and environment.
     *
     * @return bool
     */
    public function shouldRun(): bool
    {
    }

    /**
     * Returns the saved task.
     *
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Returns the saved task type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->taskType;
    }

    /**
     * Restricts this task to run within only
     * specified environements.
     *
     * @param mixed ...$environments
     *
     * @return $this
     */
    protected function environments(...$environments)
    {
        $this->environments = $environments;

        return $this;
    }

    /**
     * Checks if it runs within the specified environment.
     *
     * @param string $environment
     *
     * @return bool
     */
    protected function runsInEnvironment(string $environment): bool
    {
        // If nothing specified should run anywhere
        if (empty($this->environments))
        {
            return true;
        }

        return in_array($environment, $this->environments);
    }
}
