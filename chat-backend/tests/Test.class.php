<?php namespace Tests;

class Test{

    // runs the methods in the test class
    public function run(){
        echo "\n==== Running tests =====\n";
        
        $nonTestFunctions = [ "run", "runBeforeEach", "invokeMethod", "runAfterEach" ];
        $shouldRunBeforeEach = method_exists($this, 'runBeforeEach');
        $shouldRunAfterEach = method_exists($this, 'runAfterEach');
        
        $tests = get_class_methods($this);
        foreach($tests as $testFunc){
            if( in_array($testFunc, $nonTestFunctions )){ continue; } 
            if( $shouldRunBeforeEach ){ $this->runBeforeEach(); }
            try{
                $this->{$testFunc}();
                echo "[{$testFunc}] PASSED \n";
            }
            catch (\Exception $err) {
                echo "[{$testFunc}] FAILED : {$err->getMessage()}\n";
            }
            if( $shouldRunBeforeEach ){ $this->runBeforeEach(); }
        }
        echo "========================\n\n";
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array()){
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}

trait runBeforeEach{
    abstract function runBeforeEach();
}

trait runAfterEach{
    abstract function runAfterEach();
}

class Assert{
    static function isTrue($a, $msg="Values is false"){
        if($a === false) throw new \Exception($msg);
    }
    static function isFalse($a, $msg="Values is true"){
        if($a === true) throw new \Exception($msg);
    }
    static function isEqual($a,$b, $msg="Values are not equal"){
        if($a !== $b) throw new \Exception($msg);
    }
    static function isNotEqual($a,$b, $msg="Values are equal"){
        if($a === $b) throw new \Exception($msg);
    }
}


