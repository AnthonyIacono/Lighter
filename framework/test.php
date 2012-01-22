<?php

class TestFunction {
    public $description;
    /**
     * @var function
     */
    public $function;

    function __construct($desc, $fn) {
        $this->description = $desc;
        $this->function = $fn;
    }

    public static function Create($desc, $fn) {
        return new TestFunction($desc, $fn);
    }
}

class TestData {
    public $description;
    /**
     * @var function
     */
    public $setup = false;
    /**
     * @var array
     */
    public $functions;

    function __construct($description, $setup, $functions) {
        $this->description = $description;
        $this->setup = $setup;
        $this->functions = $functions;
    }

    public static function Create($description, $setup, $functions) {
        return new TestData($description, $setup, $functions);
    }
}

class Test {
    private function __construct() {

    }
    public static $Tests = array();

    public static function Add($description, $setup, $functions) {
        $test_data = TestData::Create($description, $setup, $functions);
        self::$Tests[] = $test_data;
    }

    public static function Clear() {
        self::$Tests = array();
    }
    public static function Test() {
        $c = count(self::$Tests);
        echo("Running $c tests\n");
        foreach(self::$Tests as $t) {
            echo("Setting up $t->description\n");
            $setup_vars = array();
            if($t->setup !== false) {
                $setup = $t->setup;
                $setup_vars = $setup();
            }
            $success_count = 0;
            $total_count = count($t->functions);
            
            foreach($t->functions as $function) {
                $function = (object)$function;
                $fn = $function->function;
                if($fn($setup_vars) == false) {
                    echo("FAILED: $function->description\n");
                }
                else {
                    $success_count++;
                    echo("PASSED: $function->description\n");
                }
            }
            echo("Done ($success_count / $total_count tests passed)\n");
        }
        self::Clear();
    }
}