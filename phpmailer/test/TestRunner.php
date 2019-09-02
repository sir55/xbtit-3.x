<?php
class TestRunner
{
    /* Run a suite of tests and report results. */
    public function run($suite)
    {
        $result = new TextTestResult;
        $suite->run($result);
        $result->report();
    }
}
?>