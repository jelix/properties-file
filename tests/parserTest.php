<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2018 Laurent Jouanneau
 * @link        http://jelix.org
 * @licence     MIT
 */
use Jelix\PropertiesFile\Parser;
use Jelix\PropertiesFile\Properties;

class parserTest extends \PHPUnit\Framework\TestCase
{
    public function testUnknownFile(){
        try {
            $parser = new Parser ();
            $prop = new Properties();
            $parser->parseFromFile(__DIR__.'/unknown.properties', $prop);
            self::fail('should throw an exception when trying reading unknownfile');
        }catch(Exception $e){
            $this->assertEquals('Cannot load the properties file '.__DIR__.'/unknown.properties', $e->getMessage(),
                'should throw the right exception when trying reading unknownfile (wrong message: '.$e->getMessage().')');
        }
    }

    public function getPropertiesContent(){
        return array(
            array('test_A.properties', []),
            array('test_B.properties', array("aaa"=>"bbb","ccc"=>"")),
            array('test_C.properties', array("aaa"=>"bbb","ccc"=>"ddd")),
            array('test_D.properties', array("module.description"=>"Tests\nunitaires jelix")),
            array('test_E.properties', array("module.description"=>"Tests\ \\\\unitaires jelix")),
            array('test_F.properties', array("module.description"=>"Tests unitaires jelix" )),
            array('test_G.properties', array(
                "module.description" => "Tests unitaires jelix",
                "ooo" => "bbbb",
                "bbb" => " ",
                "ddd" => "lorem ipsum &#65; <html> &quote; test &gt;",
                "ee" => " ",
                "ff" => "  # other",
                "hh" => "    ",
                "ii" => "   ".chr(0xc2).chr(160).' bidule',
                "jj" => "truc"
            )),
            array('test_H.properties', array("module.description" => "Tests unitaires # jelix", "ooo" => "bbbb",)),
            array('test_I.properties',  array("module.description" => "Tests unitaires # jelix",
                "ooo" => " bb bb  ",)),
            array('test_J.properties', array(
                "text.key" => "bug 639 there shouldn't have a notice during the parsing of this property ",
                "text.key2" => "same problem but with spaces at the end of the last line ",
                "text.key3" => "youpa",
            ))
        );
    }

    /**
     * @dataProvider getPropertiesContent
     */
    public function testParserFiles($file, $expected){
        $parser = new Parser ();
        $props = new Properties();
        $parser->parseFromFile(__DIR__.'/assets/'.$file, $props);

        $strings = $props->getAllProperties();
        $this->assertEquals($expected, $strings);
    }

}