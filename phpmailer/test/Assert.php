<?php
class Assert
{
    public function assert($boolean, $message = 0)
    {
        if (! $boolean) {
            $this->fail($message);
        }
    }

    public function assertEquals($expected, $actual, $message = 0)
    {
        if ($expected != $actual) {
            $this->failNotEquals($expected, $actual, "expected", $message);
        }
    }

    public function assertRegexp($regexp, $actual, $message = false)
    {
        if (! preg_match($regexp, $actual)) {
            $this->failNotEquals($regexp, $actual, "pattern", $message);
        }
    }

    public function failNotEquals($expected, $actual, $expected_label, $message = 0)
    {
        // Private function for reporting failure to match.
        $str = $message ? ($message . ' ') : '';
        $str .= "($expected_label/actual)<br>";
        $htmlExpected = htmlspecialchars($expected);
        $htmlActual = htmlspecialchars($actual);
        $str .= sprintf(
            "<pre>%s\n--------\n%s</pre>",
            $htmlExpected,
            $htmlActual
        );
        $this->fail($str);
    }
}
?>