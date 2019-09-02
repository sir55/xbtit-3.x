<?php
class TestSuite /* implements Test */
{
  /* Compose a set of Tests (instances of TestCase or TestSuite), and
     run them all. */
    var $fTests = [];

    function TestSuite($classname = false)
    {
        if ($classname) {
            // Find all methods of the given class whose name starts with
            // "test" and add them to the test suite.  We are just _barely_
            // able to do this with PHP's limited introspection...  Note
            // that PHP seems to store method names in lower case, and we
            // have to avoid the constructor function for the TestCase class
            // superclass.  This will fail when $classname starts with
            // "Test" since that will have a constructor method that will
            // get matched below and then treated (incorrectly) as a test
            // method.  So don't name any TestCase subclasses as "Test..."!
            if (floor(phpversion()) >= 4) {
            // PHP4 introspection, submitted by Dylan Kuhn
                $names = get_class_methods($classname);
                while (list($key, $method) = each($names)) {
                    if (preg_match('/^test/', $method) && $method != "testcase") {
                        $this->addTest(new $classname($method));
                    }
                }
            } else {
                $dummy = new $classname("dummy");
                $names = (array) $dummy;
                while (list($key, $value) = each($names)) {
                    $type = gettype($value);
                    if ($type == "user function" && preg_match('/^test/', $key)
                    && $key != "testcase") {
                            $this->addTest(new $classname($key));
                    }
                }
            }
        }
    }

    function addTest($test)
    {
        /* Add TestCase or TestSuite to this TestSuite */
        $this->fTests[] = $test;
    }

    function run(&$testResult)
    {
        /* Run all TestCases and TestSuites comprising this TestSuite,
         accumulating results in the given TestResult object. */
        reset($this->fTests);
        while (list($na, $test) = each($this->fTests)) {
            if ($testResult->shouldStop()) {
                break;
            }
            $test->run(&$testResult);
        }
    }

    function countTestCases()
    {
        /* Number of TestCases comprising this TestSuite (including those
         in any constituent TestSuites) */
        $count = 0;
        reset($fTests);
        while (list($na, $test_case) = each($this->fTests)) {
            $count += $test_case->countTestCases();
        }
        return $count;
    }
}
?>