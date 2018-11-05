<?php
namespace DDesrosiers\SilexAnnotations\AnnotationReader;

class DocBlock
{
    const LINE_ENDINGS = ["\r\n","\n\r","\r"];

    private $docBlockString;

    public function __construct(string $docBlockString)
    {
        $this->docBlockString = $docBlockString;
    }

    /**
     * @param $annotationName
     * @return array|null
     */
    public function parseAnnotation($annotationName): ?array
    {
        $annotation = explode("@$annotationName(", $this->docBlockString)[1];

        if ($annotation === null) {
            return null;
        }

        $lines = isset($annotation) ? $this->splitLines($annotation) : [];

        $def = [];
        foreach ($lines as $line) {
            $tokens = $this->tokenizeLine($line);
            if (strlen($tokens[0]) > 0) {
                $def[$tokens[0]][] = (count($tokens) === 1) ? [] : explode(', ', $tokens[1]);
            }
            if ($this->endsWith($line, ')')) {
                break;
            }
        }

        return $def;
    }

    /**
     * @param string $str
     * @return array
     */
    private function splitLines(string $str): array
    {
        return explode("\n", str_replace(self::LINE_ENDINGS,"\n", trim($str)));
    }

    private function tokenizeLine(string $str): array
    {
        $trimmedLine = trim($str, " \t*)");

        return explode(' => ', $trimmedLine);
    }

    /**
     * @param string $str
     * @param string $endsWith (last character)
     * @return bool
     */
    private function endsWith(string $str, string $endsWith): bool
    {
        $str = trim($str);
        return $str[strlen($str)-1] === $endsWith;
    }
}