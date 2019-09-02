<?php
class TestResult
{
  /* Collect the results of running a set of TestCases. */
    var $fFailures = [];
    var $fRunTests = 0;
    var $fStop = false;

    function TestResult()
    {
    }

    function _endTest($test) /* protected */
    {
        /* specialize this for end-of-test action, such as progress
         reports  */
    }

    function getFailures()
    {
        return $this->fFailures;
    }

    function run($test)
    {
        /* Run a single TestCase in the context of this TestResult */
        $this->_startTest($test);
        $this->fRunTests++;

        $test->runBare();

        /* this is where JUnit would catch AssertionFailedError */
        $exceptions = $test->getExceptions();
        if ($exceptions) {
            $this->fFailures[] = new TestFailure(&$test, &$exceptions);
        }
        $this->_endTest($test);
    }

    function countTests()
    {
        return $this->fRunTests;
    }

    function shouldStop()
    {
        return $this->fStop;
    }

    function _startTest($test) /* protected */
    {
        /* specialize this for start-of-test actions */
    }

    function stop()
    {
        /* set indication that the test sequence should halt */
        $fStop = true;
    }

    function countFailures()
    {
        return count($this->fFailures);
    }
}
?>