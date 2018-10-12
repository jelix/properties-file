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
        "headerComment" => ""
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
            $value = mb_ereg_replace("^ ", "\\s", $value);
            $value = mb_ereg_replace(" $", "\\s", $value);

            $startLen = mb_strlen($line);
            $valueLen = mb_strlen($value);
            if ($valueLen + $startLen > $options['lineLength']) {
                $line .= $this->chunkSplit($value,
                    $options['lineLength'] - $startLen,
                    $options['lineLength']);
            }
            else {
                $line .= $value;
            }

            $writer($line."\n");
        }
    }


    protected function chunkSplit($string, $firstLineLength, $lineLength) {
        $valueLen = mb_strlen($string);
        $start = 0;
        $line = "";
        if ($valueLen > $firstLineLength) {
            $cut = mb_strcut($string, $start, $firstLineLength);
            if (substr($cut, -1) == '\\') {
                $line .= mb_strcut($string, $start, $firstLineLength+1)."\\\n";
                $valueLen -= $firstLineLength + 1;
                $start += $firstLineLength + 1;
            }
            else {
                $line .= $cut."\\\n";
                $valueLen -= $firstLineLength;
                $start += $firstLineLength;
            }
        }

        while ($valueLen > $lineLength) {
            $cut = mb_strcut($string, $start, $lineLength);
            if (substr($cut, -1) == '\\') {
                $line .= mb_strcut($string, $start, $lineLength+1)."\\\n";
                $valueLen -= $lineLength + 1;
                $start += $lineLength + 1;
            }
            else {
                $line .= $cut."\\\n";
                $valueLen -= $lineLength;
                $start += $lineLength;
            }
        }
        if ($valueLen > 0) {
            $line .= mb_strcut($string, $start, $valueLen);
        }
        return $line;
    }


}