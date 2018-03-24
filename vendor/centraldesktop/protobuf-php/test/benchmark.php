<?php

$autoload_path =  __DIR__ . "/../vendor/autoload.php";
$loader = require($autoload_path);
$loader->add('protos', __DIR__);

use DrSlump\Protobuf;
class Benchmark {

    protected $tests = array(
        'DecodeBinarySimple',
        'DecodeJsonSimple',
    );

    public function run($iterations = 1000)
    {
        $profiler = new Benchmark_Profiler(true);
        foreach ($this->tests as $test) {
            $method = 'config' . $test;
            $args = $this->$method();

            $method = 'run' . $test;
            $profiler->enterSection($test);
            for ($i=0; $i<$iterations; $i++) {
                call_user_func_array(array($this, $method), $args);
            }
            $profiler->leaveSection($test);
        }

        $profiler->stop();
        $profiler->display();
    }

    protected function configDecodeBinarySimple()
    {
        return array(
            new Protobuf\Codec\Binary(),
            file_get_contents(__DIR__ . '/protos/simple.bin')
        );
    }

    protected function runDecodeBinarySimple($codec, $data)
    {
        $codec->decode(new test\Simple(), $data);
    }

    protected function configDecodeJsonSimple()
    {
        $codecBin = new Protobuf\Codec\Binary();
        $codecJson = new Protobuf\Codec\Json();

        $bin = $this->configDecodeBinarySimple();
        $simple = $codecBin->decode(new test\Simple(), $bin[1]);
        $data = $codecJson->encode($simple);
        return array($codecJson, $data);
    }

    protected function runDecodeJsonSimple($codec, $data)
    {
        $codec->decode(new test\Simple(), $data);
    }
}


$bench = new Benchmark();
$bench->run(1000);