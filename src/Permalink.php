<?php

namespace Frogg;

class Permalink
{

    private $prefix;
    private $title;
    private $suffix;

    /**
     * Default constructor for class Frogg\Permalink
     *
     * @param string $title  The original string from where the permalink will be created
     * @param string $prefix Default value is ''
     * @param string $suffix Default value is ''
     */
    public function __construct($title, $prefix = '', $suffix = '')
    {
        $this->title  = $title;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    /**
     * Sets one prefix for the permalink
     *
     * @param string $prefix The string that will be used as a prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets one suffix for the permalink
     *
     * @param string $suffix The string that will be used as a suffix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * Creates the permalink
     *
     * @return string
     */
    public function create() : string
    {
        return $this->prefix . self::createSlug($this->title) . $this->suffix;
    }

    /**
     * Creates the first version of the Permalink
     *
     * @param string $title Text that used to create the permalink
     *
     * @return string
     */
    public static function createSlug($title) : string
    {
        $normalizeChars = [
            'Á' => 'A',
            'À' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Å' => 'A',
            'Ä' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'É' => 'E',
            'È' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Í' => 'I',
            'Ì' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'Eth',
            'Ñ' => 'N',
            'Ó' => 'O',
            'Ò' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ú' => 'U',
            'Ù' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',

            'á' => 'a',
            'à' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'å' => 'a',
            'ä' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'eth',
            'ñ' => 'n',
            'ó' => 'o',
            'ò' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ý' => 'y',

            'ß' => 'sz',
            'þ' => 'thorn',
            'ÿ' => 'y',
        ];

        $title = str_replace(["&lt;", "&gt;", '&amp;', '&#039;', '&quot;', '&lt;', '&gt;'], [
            "",
            "",
            'e',
            '',
            '',
            '',
            '',
        ], htmlspecialchars_decode($title, ENT_NOQUOTES));
        $title = strtr($title, $normalizeChars);
        $title = html_entity_decode(strtolower($title));

        $a = [
            '/ +/'                 => '-',
            '/ \(/'                => '-',
            '/\) /'                => '-',
            '/_+/'                 => '-',
            '/\//'                 => '-',
            "/[^a-zA-Z0-9\\-_+ ]/" => '',
            '/-+/'                 => '-',
            '/^-/'                 => '',
            '/-$/'                 => '',
        ];

        return trim(preg_replace(array_keys($a), array_values($a), $title), '\\-_.+ ');
    }

}
