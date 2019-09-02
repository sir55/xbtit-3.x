<?php
class TestCase extends Assert /* implements Test */
{
  /* Defines context for running tests.  Specific context -- such as
     instance variables, global variables, global state -- is defined
     by creating a subclass that specializes the setUp() and
     tearDown() methods.  A specific test is defined by a subclass
     that specializes the runTest() method. */
    var $fName;
    var $fResult;
    var $fExceptions = [];

    function TestCase($name)
    {
        $this->fName = $name;
    }

    function run($testResult = 0)
    {
        /* Run this single test, by calling the run() method of the
         TestResult object which will in turn call the runBare() method
         of this object.  That complication allows the TestResult object
         to do various kinds of progress reporting as it invokes each
         test.  Create/obtain a TestResult object if none was passed in.
         Note that if a TestResult object was passed in, it must be by
         reference. */
        if (! $testResult) {
            $testResult = $this->_createResult();
        }
        $this->fResult = $testResult;
        $testResult->run(&$this);
        $this->fResult = 0;
        return $testResult;
    }

    function countTestCases()
    {
        return 1;
    }

    function runTest()
    {
        $name = $this->name();
        // Since isset($this->$name) is false, no way to run defensive checks
        $this->$name();
    }

    function setUp() /* expect override */
    {
        //print("TestCase::setUp()<br>\n");
    }

    function tearDown() /* possible override */
    {
        //print("TestCase::tearDown()<br>\n");
    }

  ////////////////////////////////////////////////////////////////


    function _createResult() /* protected */
    {
        /* override this to use specialized subclass of TestResult */
        return new TestResult;
    }

    function fail($message = 0)
    {
        //printf("TestCase::fail(%s)<br>\n", ($message) ? $message : '');
        /* JUnit throws AssertionFailedError here.  We just record the
         failure and carry on */
        $this->fExceptions[] = new Exception(&$message);
    }

    function error($message)
    {
        /* report error that requires correction in the test script
         itself, or (heaven forbid) in this testing infrastructure */
        printf('<b>ERROR: ' . $message . '</b><br>');
        $this->fResult->stop();
    }

    function failed()
    {
        return count($this->fExceptions);
    }

    function getExceptions()
    {
        return $this->fExceptions;
    }

    function name()
    {
        return $this->fName;
    }

    function runBare()
    {
        $this->setup();
        $this->runTest();
        $this->tearDown();
    }
}
?>