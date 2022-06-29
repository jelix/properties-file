<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2018 Laurent Jouanneau
 * @link        http://jelix.org
 * @licence     MIT
 */
use Jelix\PropertiesFile\Writer;
use Jelix\PropertiesFile\Properties;

class writerTest extends \PHPUnit\Framework\TestCase
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
                    "ii" => "   ".chr(0xc2).chr(160).' bidule',
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
                    "ii" => "   ".chr(0xc2).chr(160).' bidule',
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
            array(
                array(
                    "super.long" => "<p>Un email vous a été envoyé à l'adresse indiquée. Si vous ne le voyez pas ".
                        "dans votre boite au lettre d'ici quelques minutes, vérifiez votre dossier de".
                        " spam. Si vous n'avez pas reçu le mail, peut-être que le nom d'utilisateur ".
                        "et l'adresse que vous avez donné ne sont pas valides. Contactez ".
                        "l'administrateur du site web.</p> <p>Cet email contient un lien vers une ".
                        "page qui vous permettra de changer de mot de passe. Ce lien est valable 48 ".
                        "heures.</p> <p>Tant que vous n'avez pas changé le mot de passe, l'ancien ".
                        "reste valable.</p>",
                ),
                "super.long = <p>Un email vous a été envoyé à l'adresse indiquée. Si vous ne le voyez pas dans votre boite au lettre d'ici quelques minutes, vérifiez votre dossier de spam. Si vous n'avez pas reçu le\\\n".
                "mail, peut-être que le nom d'utilisateur et l'adresse que vous avez donné ne sont pas valides. Contactez l'administrateur du site web.</p> <p>Cet email contient un lien vers une page qui vous\\\n".
                "permettra de changer de mot de passe. Ce lien est valable 48 heures.</p> <p>Tant que vous n'avez pas changé le mot de passe, l'ancien reste valable.</p>\n",
                array("lineLength"=>200, "cutOnlyAtSpace"=>true)
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