<?php
//
// PHP framework for testing, based on the design of "JUnit".
//
// Written by Fred Yankowski <fred@ontosys.com>
//            OntoSys, Inc  <http://www.OntoSys.com>
//
// $Id: phpunit.php,v 1.1 2002/03/30 19:32:17 bmatzelle Exp $

// Copyright (c) 2000 Fred Yankowski

// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use, copy,
// modify, merge, publish, distribute, sublicense, and/or sell copies
// of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
// BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
// ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
// CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.
//
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE |
        E_CORE_ERROR | E_CORE_WARNING);

/*
interface Test {
  function run(&$aTestResult);
  function countTestCases();
}
*/

function trace($msg)
{
    return;
    print($msg);
    flush();
}


class Exception
{
    /* Emulate a Java exception, sort of... */
    public $message;
    public function Exception($message)
    {
        $this->message = $message;
    }
    public function getMessage()
    {
        return $this->message;
    }
}
?>