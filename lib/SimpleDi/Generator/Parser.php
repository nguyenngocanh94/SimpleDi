<?php


namespace SimpleDi\Generator;


class Parser
{
    public function extractPhpClasses(string $path): array
    {
        $code = file_get_contents($path);
        $tokens = @token_get_all($code);
        $namespace = $class = $classLevel = $level = NULL;
        $classes = [];
        while (list(, $token) = each($tokens)) {
            switch (is_array($token) ? $token[0] : $token) {
                case T_NAMESPACE:
                    $namespace = ltrim($this->fetch($tokens, [T_STRING, T_NS_SEPARATOR]) . '\\', '\\');
                    break;

                case T_CLASS:
                    if ($name = $this->fetch($tokens, T_STRING)) {
                        $classes[] = $namespace . $name;
                    }
                    break;
                case T_INTERFACE:
                case T_TRAIT:
                    break;
            }
        }
        return $classes;
    }

    private function fetch(&$tokens, $take): ?string
    {
        $res = NULL;
        while ($token = current($tokens)) {
            list($token, $s) = is_array($token) ? $token : [$token, $token];
            if (in_array($token, (array) $take, TRUE)) {
                $res .= $s;
            } elseif (!in_array($token, [T_DOC_COMMENT, T_WHITESPACE, T_COMMENT], TRUE)) {
                break;
            }
            next($tokens);
        }
        return $res;
    }
}