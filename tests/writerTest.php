<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2018 Laurent Jouanneau
 * @link        http://jelix.org
 * @licence     MIT
 */
use Jelix\PropertiesFile\Writer;
use Jelix\PropertiesFile\Properties;

class writerTest extends PHPUnit_Framework_TestCase
{
    public function getPropertiesContent(){
        return array(
            array(
                array(
                    "aaa"=>"bbb",
                    "ccc"=>""
                ),
                "aaa = bbb\n".
                "ccc = \n",
                80
            ),
            array(
                array(
                    "aaa"=>"Lorem #Ipsum is\nsimply dummy text ",
                    "ccc"=>"ddd"
                ),
                "aaa = Lorem \\#Ipsum is\\nsimply dummy text\\s\n".
                "ccc = ddd\n",
                80
            ),
            array(
                array(
                    "html" => "lorem <ipsum>&#65; <html> &quote; test &gt;",
                    "ee" => " ",
                    "ff" => "  # other",
                    "hh" => "    ",
                    "ii" => "   ".utf8_encode(chr(160)).' bidule',
                    "jj" => "truc "
                ),
                "html = lorem <ipsum>&\\#65; <html> &quote; test &gt;\n".
                "ee = \\s\n".
                "ff = \\s \\# other\n".
                "hh = \\s  \\s\n".
                "ii = \\s  \\S bidule\n".
                "jj = truc\\s\n",
                80
            ),
            array(
                array(
                    "long.description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    "exact.max.length" => "Lorem ipsum"),
                "long.description = Lorem ipsum\\\n".
                " dolor sit amet, consectetur a\\\n".
                "dipiscing elit.\n".
                "exact.max.length = Lorem ipsum\n",
                30
            ),
            array(
                array(
                    "long.description" => "Lorem ipsu#m dolor \nsit amet, consectetur adipiscing elit.",
                    "exact.max.length" => "Lorem i#psum"
                ),
                "long.description = Lorem ipsu\\#\\\n".
                "m dolor \\nsit amet, consectetu\\\n".
                "r adipiscing elit.\n".
                "exact.max.length = Lorem i\\#ps\\\n".
                "um\n",
                30
            ),
        );
    }

    /**
     * @dataProvider getPropertiesContent
     */
    public function testWriterString($properties, $expected, $linelength){
        $props = new Properties($properties);
        $writer = new Writer ();
        $result = $writer->writeToString($props, array("lineLength"=>$linelength));

        $this->assertEquals($expected, $result);
    }
}