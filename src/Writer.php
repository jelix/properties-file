<?php
/**
 * @author      Laurent Jouanneau
 * @copyright   2018 Laurent Jouanneau
 *
 * @link        https://jelix.org
 * @licence     MIT
 */
namespace Jelix\PropertiesFile;

class Writer {

    const DEFAULT_OPTIONS = array(
        "lineLength" => 120,
        "spaceAroundEqual" => true,
        "headerComment" => "",
        "removeTrailingSpace" => false,
        "cutOnlyAtSpace" => true
    );

    function writeToFile(Properties $properties, $fileName, $options = array()) {

        $options = array_merge(self::DEFAULT_OPTIONS, $options);

        $f = @fopen($fileName, 'w');

        if ($f === false) {
            throw new \Exception('Cannot open the properties file ' . $fileName);
        }

        $lineWriter = function($line) use ($f) {
            fwrite($f, $line);
        };

        $this->writeContent($lineWriter, $properties, $options);
        fclose($f);
    }

    function writeToString(Properties $properties, $options = array()) {

        $options = array_merge(self::DEFAULT_OPTIONS, $options);

        $o = new \StdClass;
        $o->content = '';

        $lineWriter = function($line) use ($o) {
            $o->content .= $line;
        };

        $this->writeContent($lineWriter, $properties, $options);
        return $o->content;
    }

    protected function writeContent(callable $writer, Properties $properties, $options) {

        if ($options["headerComment"]) {
            $writer('# '.str_replace("\n", "\n# ",$options["headerComment"])."\n");
        }

        $equal = ($options["spaceAroundEqual"]? ' = ': '=');

        foreach($properties->getIterator() as $key => $value) {
            $line = $key . $equal;


            $value = mb_ereg_replace("#", "\\#", $value);
            $value = mb_ereg_replace(utf8_encode(chr(160)), "\\S", $value);
            $value = mb_ereg_replace("\n", "\\n", $value);
            if ($options["removeTrailingSpace"]) {
                $value = rtrim($value, " ");
            }

            $startLen = mb_strlen($line);
            $valueLen = mb_strlen($value);
            if ($valueLen + $startLen > $options['lineLength']) {
                $value = $this->chunkSplit($value,
                    $options['lineLength'] - $startLen,
                    $options['lineLength'],
                    $options['cutOnlyAtSpace']);
            }
            $value = mb_ereg_replace(" $", "\\s", $value);
            $value = mb_ereg_replace("^ ", "\\s", $value);
            $line .= $value;

            $writer($line."\n");
        }
    }


    protected function chunkSplit($string, $firstLineLength, $lineLength, $cutOnlyAtSpace) {
        $valueLen = mb_strlen($string);
        $start = 0;
        $line = "";
        if ($valueLen > $firstLineLength) {
            list ($cut, $cutLength) = $this->cutString($string, $start, $firstLineLength, $cutOnlyAtSpace);
            $line .= $cut;
            $valueLen -= $cutLength;
            $start += $cutLength;
        }

        while ($valueLen > $lineLength) {
            list ($cut, $cutLength) = $this->cutString($string, $start, $lineLength, $cutOnlyAtSpace);
            $line .= $cut;
            $valueLen -= $cutLength;
            $start += $cutLength;
        }
        if ($valueLen > 0) {
            $line .= mb_strcut($string, $start, $valueLen);
        }
        $line = preg_replace('/(\\n )/mu', "\n\\s", $line);
        return preg_replace('/( \\\\\\n)/mu', "\\s\\\n", $line);
    }

    protected function cutString($string, $start, $lineLength, $cutOnlyAtSpace) {

        $cut = mb_strcut($string, $start, $lineLength);
        $length = mb_strlen($cut);

        if ($cutOnlyAtSpace) {
            $cut2 = preg_replace('/(\s+?\S+)?$/u', '', mb_strcut($string, $start, $lineLength+1));
            $length2 = mb_strlen($cut2);
            if ($length < $length2) {
                $lastchar = mb_strcut($string, $start+$lineLength, 1);
                if ($lastchar != ' ') {
                    $cut = $cut2.preg_replace('/^(\S+)(.*)/u', '${1}', mb_strcut($string, $start+$lineLength+1));
                    $length = mb_strlen($cut);
                }
            }
            else if ($length > $length2) {
                $cut = $cut2;
                $length = $length2;
            }
        }
        else {
            if (substr($cut, -1) == '\\') { //escape character
                $cut = mb_strcut($string, $start, $lineLength+1);
                $length = $lineLength + 1;
            }
        }

        if (mb_strcut($string, $start+$length, 1) == ' ') {
            $length++;
        }
        return array($cut."\\\n", $length);
    }

}