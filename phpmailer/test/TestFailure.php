<?php
class TestFailure
{
    /* Record failure of a single TestCase, associating it with the
       exception(s) that occurred */
    public $fFailedTestName;
    public $fExceptions;

    public function TestFailure(&$test, &$exceptions)
    {
        $this->fFailedTestName = $test->name();
        $this->fExceptions = $exceptions;
    }

    public function getExceptions()
    {
        return $this->fExceptions;
    }
    public function getTestName()
    {
        return $this->fFailedTestName;
    }
}
?>