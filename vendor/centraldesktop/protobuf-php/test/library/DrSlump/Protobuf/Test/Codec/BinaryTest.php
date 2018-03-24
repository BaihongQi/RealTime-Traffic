<?php

namespace DrSlump\Protobuf\Test\Codec;

use DrSlump\Protobuf;
use DrSlump\Protobuf\Codec;
use DrSlump\Protobuf\Test\protos;

class BinaryTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $codec = new Codec\Binary();
        Protobuf\Protobuf::setDefaultCodec($codec);

        $this->bin_simple = file_get_contents(dirname(__DIR__) . '/protos/simple.bin');
        $this->bin_book = file_get_contents(dirname(__DIR__) . '/protos/addressbook.bin');
        $this->bin_repeated_string = file_get_contents(dirname(__DIR__) . '/protos/repeated-string.bin');
        $this->bin_repeated_int32 = file_get_contents(dirname(__DIR__) . '/protos/repeated-int32.bin');
        $this->bin_repeated_nested = file_get_contents(dirname(__DIR__) . '/protos/repeated-nested.bin');
        $this->bin_ext = file_get_contents(dirname(__DIR__) . '/protos/extension.bin');
    }

    function testSerializeSimpleMessageComparingTypesWithProtoc()
    {
        $max = pow(2, 54)-1;
        $min = -$max;

        $fields = array(
            'double' => array(1, 0.1, 1.0, -1, -0.1, -100000, 123456789.12345, -123456789.12345),
            'float'  => array(1, 0.1, 1.0, -1, -0.1, -100000, 12345.123, -12345.123),
            'int64'  => array(0, 1, -1, 123456789123456789, -123456789123456789, $min),
            'uint64' => array(0, 1, 1000, 123456789123456789, PHP_INT_MAX, $max),
            'int32'  => array(0, 1, -1, 123456789, -123456789),
            'fixed64'  => array(0, 1, 1000, 123456789123456789),
            'fixed32'  => array(0, 1, 1000, 123456789),
            'bool'  => array(0, 1),
            'string'  => array("", "foo"),
            'bytes'  => array("", "foo"),
            'uint32'  => array(0, 1, 1000, 123456789),
            'sfixed32'  => array(0, 1, -1, 123456789, -123456789),
            'sfixed64'  => array(0, 1, -1, 123456789123456789, -123456789123456789),
            'sint32'  => array(0, 1, -1, 123456789, -123456789),
            'sint64' => array(0, 1, -1, 123456789123456789, -123456789123456789, $min, $max),
        );

        foreach ($fields as $field=>$values) {
            foreach ($values as $value) {
                $simple = new protos\simple();
                $simple->$field = $value;
                $bin = Protobuf\Protobuf::encode($simple);

                if (is_string($value)) $value = '"' . $value . '"';

                exec("echo '$field: $value' | protoc --encode=DrSlump.Protobuf.Test.protos.Simple -Itest test/library/DrSlump/Protobuf/Test/protos/simple.proto", $out);

                $out = implode("\n", $out);

                $printValue = var_export($value, true);
                $this->assertEquals(bin2hex($bin), bin2hex($out), "Encoding $field with value $printValue");
            }
        }

        foreach ($fields as $field=>$values) {
            foreach ($values as $value) {
                $cmdValue = is_string($value)
                          ? '"' . $value . '"'
                          : $value;

                exec("echo '$field: $cmdValue' | protoc --encode=DrSlump.Protobuf.Test.protos.Simple -Itest test/library/DrSlump/Protobuf/Test/protos/simple.proto", $out);
                $out = implode("\n", $out);

                $simple = Protobuf\Protobuf::decode('\DrSlump\Protobuf\Test\protos\simple', $out);

                // Hack the comparison for float precision
                if (is_float($simple->$field)) {
                    $precision = strlen($value) - strpos($value, '.');
                    $simple->$field = round($simple->$field, $precision);
                }

                $printValue = var_export($value, true);
                $this->assertEquals($simple->$field, $value, "Decoding $field with value $printValue");
            }
        }
    }

    function testSerializeEnumComparingWithProtoc()
    {
        $complex = new protos\Complex();

        exec("echo 'enum: FOO' | protoc --encode=DrSlump.Protobuf.Test.protos.Complex -Itest test/library/DrSlump/Protobuf/Test/protos/complex.proto", $protocbin);
        $protocbin = implode("\n", $protocbin);

        // Check encoding an enum
        $complex->enum = protos\Complex\Enum::FOO;
        $encbin = Protobuf\Protobuf::encode($complex);

        $this->assertEquals(bin2hex($encbin), bin2hex($protocbin), "Encoding Enum field");

        // Check decoding an enum
        $complex = Protobuf\Protobuf::decode('\DrSlump\Protobuf\Test\protos\Complex', $protocbin);
        $this->assertEquals($complex->enum, protos\Complex\Enum::FOO, "Decoding Enum field");
    }

    function testSerializeNestedMessageComparingWithProtoc()
    {
        exec("echo 'nested { foo: \"FOO\" }' | protoc --encode=DrSlump.Protobuf.Test.protos.Complex -Itest test/library/DrSlump/Protobuf/Test/protos/complex.proto", $protocbin);
        $protocbin = implode("\n", $protocbin);

        // Encoding
        $complex = new protos\Complex();
        $complex->nested = new protos\Complex\Nested();
        $complex->nested->foo = 'FOO';
        $encbin = Protobuf\Protobuf::encode($complex);

        $this->assertEquals(bin2hex($encbin), bin2hex($protocbin), "Encoding nested message");

        // Decoding
        $complex = Protobuf\Protobuf::decode('\DrSlump\Protobuf\Test\protos\Complex', $protocbin);
        $this->assertEquals($complex->nested->foo, 'FOO', "Decoding nested message");
    }

    function testSerializeMessageWithRepeatedFields()
    {
        $repeated = new protos\Repeated();
        $repeated->addString('one');
        $repeated->addString('two');
        $repeated->addString('three');
        $bin = Protobuf\Protobuf::encode($repeated);
        $this->assertEquals($bin, $this->bin_repeated_string);

        $repeated = new protos\Repeated();
        $repeated->addInt(1);
        $repeated->addInt(2);
        $repeated->addInt(3);
        $bin = Protobuf\Protobuf::encode($repeated);
        $this->assertEquals($bin, $this->bin_repeated_int32);


        $repeated = new protos\Repeated();
        $nested = new protos\Repeated\Nested();
        $nested->setId(1);
        $repeated->addNested($nested);
        $nested = new protos\Repeated\Nested();
        $nested->setId(2);
        $repeated->addNested($nested);
        $nested = new protos\Repeated\Nested();
        $nested->setId(3);
        $repeated->addNested($nested);
        $bin = Protobuf\Protobuf::encode($repeated);
        $this->assertEquals($bin, $this->bin_repeated_nested);
    }

    function testSerializeComplexMessage()
    {
        $book = new protos\AddressBook();
        $person = new protos\Person();
        $person->name = 'John Doe';
        $person->id = 2051;
        $person->email = 'john.doe@gmail.com';
        $phone = new protos\PhoneNumber;
        $phone->number = '1231231212';
        $phone->type = protos\PhoneType::HOME;
        $person->addPhone($phone);
        $phone = new protos\PhoneNumber;
        $phone->number = '55512321312';
        $phone->type = protos\PhoneType::MOBILE;
        $person->addPhone($phone);
        $book->addPerson($person);

        $person = new Person();
        $person->name = 'Iván Montes';
        $person->id = 23;
        $person->email = 'drslump@pollinimini.net';
        $phone = new PhoneNumber;
        $phone->number = '3493123123';
        $phone->type = PhoneType::WORK;
        $person->addPhone($phone);
        $book->addPerson($person);

        $bin = Protobuf\Protobuf::encode($book);
        $this->assertEquals($bin, $this->bin_book);
    }

    function testSerializeMessageWithExtendedFields()
    {
        $ext = new ExtA();
        $ext->first = 'FIRST';
        $ext['test\ExtB\second'] = 'SECOND';
        $bin = Protobuf\Protobuf::encode($ext);
        $this->assertEquals($bin, $this->bin_ext);
    }

    function testUnserializeSimpleMessage()
    {
        $simple = Protobuf\Protobuf::decode('Tests\Simple', $this->bin_simple);
        $this->assertInstanceOf($simple, 'Tests\Simple');
        $this->assertEquals($simple->string, 'foo');
        $this->assertEquals($simple->int32, -123456789);
    }

    function testUnserializeMessageWithRepeatedFields()
    {
        $repeated = Protobuf\Protobuf::decode('Tests\Repeated', $this->bin_repeated_string);
        $this->assertInstanceOf($repeated, 'Tests\Repeated');
        $this->assertEquals($repeated->getString(), array('one', 'two', 'three'));

        $repeated = Protobuf\Protobuf::decode('Tests\Repeated', $this->bin_repeated_int32);
        $this->assertInstanceOf($repeated, 'Tests\Repeated');
        $this->assertEquals($repeated->getInt(), array(1,2,3));

        $repeated = Protobuf\Protobuf::decode('Tests\Repeated', $this->bin_repeated_nested);
        $this->assertInstanceOf($repeated, 'Tests\Repeated');
        foreach ($repeated->getNested() as $i=>$nested) {
            $this->assertEquals($nested->getId(), $i+1);
        }
    }

    function testUnserializeComplexMessage()
    {
        $complex = Protobuf\Protobuf::decode('Tests\AddressBook', $this->bin_book);
        $this->assertEquals(count($complex->person), 2);
        $this->assertEquals($complex->getPerson(0)->name, 'John Doe');
        $this->assertEquals($complex->getPerson(1)->name, 'Iván Montes');
        $this->assertEquals($complex->getPerson(0)->getPhone(1)->number, '55512321312');
    }

    function testUnserializeMessageWithExtendedFields()
    {
        $ext = Protobuf\Protobuf::decode('Tests\ExtA', $this->bin_ext);
        $this->assertEquals($ext->first, 'FIRST');
        $this->assertEquals($ext['test\ExtB\second'], 'SECOND');
    }

    function testMultiCodecSimpleMessage()
    {
        $jsonCodec = new Codec\Json();
        $simple = Protobuf\Protobuf::decode('Tests\Simple', $this->bin_simple);
        $json = $jsonCodec->encode($simple);
        $simple = $jsonCodec->decode(new Simple(), $json);
        $bin = Protobuf\Protobuf::encode($simple);
        $this->assertEquals($bin, $this->bin_simple);
    }

    function testMultiCodecMessageWithRepeatedFields()
    {
        $jsonCodec = new Codec\Json();
        $repeated = Protobuf\Protobuf::decode('Tests\Repeated', $this->bin_repeated_nested);
        $json = $jsonCodec->encode($repeated);
        $repeated = $jsonCodec->decode(new Repeated(), $json);
        $bin = Protobuf\Protobuf::encode($repeated);
        $this->assertEquals($bin, $this->bin_repeated_nested);
    }
    
}
