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
                array("lineLength"=>80)
            ),
            array(
                array(
                    "aaa"=>"Lorem #Ipsum is\nsimply dummy text ",
                    "ccc"=>"ddd"
                ),
                "aaa = Lorem \\#Ipsum is\\nsimply dummy text\\s\n".
                "ccc = ddd\n",
                array("lineLength"=>80)
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
                array("lineLength"=>80)
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
                "ee = \n".
                "ff = \\s \\# other\n".
                "hh = \n".
                "ii = \\s  \\S bidule\n".
                "jj = truc\n",
                array("lineLength"=>80, "removeTrailingSpace" => true)
            ),
            array(
                array(
                    "long.description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    "exact.max.length" => "Lorem ipsum"),
                "long.description = Lorem ipsum\\\n".
                "dolor sit amet, consectetur ad\\\n".
                "ipiscing elit.\n".
                "exact.max.length = Lorem ipsum\n",
                array("lineLength"=>30, "cutOnlyAtSpace"=>false)
            ),
            array(
                array(
                    "long.description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    "exact.max.length" => "Lorem ips",
                    "big.first.word" => "Loremipsumdolor sit amet, consectetur dipiscing elit.",
                    "with.long.space" => "Praesent a quam a mauris   tempus   placerat. Sed bibendum     mattis erat, quis consequat ",
                ),
                "long.description = Lorem\\\n".
                "ipsum dolor sit amet,\\\n".
                "consectetur adipiscing elit.\n".
                "exact.max.length = Lorem ips\n".
                "big.first.word = Loremipsumdolor\\\n".
                "sit amet, consectetur\\\n".
                "dipiscing elit.\n".
                "with.long.space = Praesent a\\\n".
                "quam a mauris   tempus\\\n".
                "\\s placerat. Sed bibendum   \\s\\\n".
                "mattis erat, quis consequat\\s\n"
                ,
                array("lineLength"=>28, "cutOnlyAtSpace"=>true)
            ),
            array(
                array(
                    "long.description" => "Lorem ipsu#m dolor \nsit amet, consectetur adipiscing elit.",
                    "exact.max.length" => "Lorem i#psum"
                ),
                "# This is \n".
                "# a header comment\n".
                "long.description = Lorem ipsu\\#\\\n".
                "m dolor \\nsit amet, consectetu\\\n".
                "r adipiscing elit.\n".
                "exact.max.length = Lorem i\\#ps\\\n".
                "um\n",
                array("lineLength"=>30, "cutOnlyAtSpace"=>false, "headerComment"=> "This is \na header comment")
            ),
        );
    }

    /**
     * @dataProvider getPropertiesContent
     */
    public function testWriterString($properties, $expected, $options){
        $props = new Properties($properties);
        $writer = new Writer ();
        $result = $writer->writeToString($props, $options);

        $this->assertEquals($expected, $result);
    }
}