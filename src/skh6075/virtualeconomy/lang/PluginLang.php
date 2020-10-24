<?php


namespace skh6075\virtualeconomy\lang;

class PluginLang{

    private string $lang;

    private array $translates = [];


    public function __construct () {
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function setLang (string $lang): PluginLang{
        $this->lang = $lang;
        return $this;
    }

    /**
     * @param array $translates
     * @return $this
     */
    public function setTranslates (array $translates): PluginLang{
        $this->translates = $translates;
        return $this;
    }

    /**
     * @param string $key
     * @param array $replaces
     * @param bool $pushPrefix
     * @return string
     */
    public function translate (string $key, array $replaces = [], bool $pushPrefix = true): string{
        $format = $pushPrefix ? $this->translates ["prefix"] ?? "" : "";
        $format .= $this->translates [$key] ?? "";

        foreach ($replaces as $old => $new) {
            $format = str_replace ($old, $new, $format);
        }
        return $format;
    }
}