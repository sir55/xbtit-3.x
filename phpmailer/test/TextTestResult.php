<?php
class TextTestResult extends TestResult
{
    /* Specialize TestResult to produce text/html report */
    public function TextTestResult()
    {
        $this->TestResult();  // call superclass constructor
    }

    public function report()
    {
        /* report result of test run */
        $nRun = $this->countTests();
        $nFailures = $this->countFailures();
        printf("<p>%s test%s run<br>", $nRun, ($nRun == 1) ? '' : 's');
        printf("%s failure%s.<br>\n", $nFailures, ($nFailures == 1) ? '' : 's');
        if ($nFailures == 0) {
            return;
        }

        print("<ol>\n");
        $failures = $this->getFailures();
        while (list($i, $failure) = each($failures)) {
            $failedTestName = $failure->getTestName();
            printf("<li>%s\n", $failedTestName);

            $exceptions = $failure->getExceptions();
            print("<ul>");
            while (list($na, $exception) = each($exceptions)) {
                printf("<li>%s\n", $exception->getMessage());
            }
            print("</ul>");
        }
        print("</ol>\n");
    }

    public function _startTest($test)
    {
        printf("%s ", $test->name());
        flush();
    }

    public function _endTest($test)
    {
        $outcome = $test->failed()
         ? "<font color=\"red\">FAIL</font>"
         : "<font color=\"green\">ok</font>";
        printf("$outcome<br>\n");
        flush();
    }
}
?>