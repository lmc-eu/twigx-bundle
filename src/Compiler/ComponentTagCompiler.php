<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\Compiler;

/**
 * Transforms <Tags /> to twig embed tags
 * Most parts shamelessly stolen form Laravel's ComponentTagCompiler {@link https://laravel.com/api/8.x/Illuminate/View/Compilers/ComponentTagCompiler.html}
 */
class ComponentTagCompiler
{
    protected string $source;

    private string $twigPathAlias;

    public function __construct(string $source, string $twigPathAlias)
    {
        $this->source = $source;
        $this->twigPathAlias = $twigPathAlias;
    }

    public function compile(): string
    {
        $value = $this->source;
        $value = $this->compileSelfClosingTags($value);
        $value = $this->compileOpeningTags($value);

        return $this->compileClosingTags($value);
    }

    /**
     * Compile the opening tags within the given string.
     */
    protected function compileOpeningTags(string $value): string
    {
        $pattern = "/
            <
                \s*
                ([[A-Z]\w+]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (\{\#\s*(.*?)\s*\#\})                        # Capture any sequence between {# and #}
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return (string) preg_replace_callback(
            $pattern,
            function (array $matches) {
                $attributes = $this->getAttributesFromAttributeString($matches['attributes']);
                $name = $matches[1];

                return $this->componentStartString($name, $attributes) . '{% block content %}';
            },
            $value
        );
    }

    protected function componentStartString(string $component, string $attributes): string
    {
        return "{% embed \"@" . $this->twigPathAlias . "/" . lcfirst($component) . ".twig\" with { props: $attributes } %}";
    }

    /**
     * Compile the closing tags within the given string.
     */
    protected function compileClosingTags(string $value): string
    {
        // replace </Alert> with {% endblock %}{% endembed %}
        return (string) preg_replace("/<\/\s*([[A-Z]\w+]*)\s*>/", '{% endblock %}{% endembed %}', $value);
    }

    /**
     * Compile the self-closing tags within the given string.
     */
    protected function compileSelfClosingTags(string $value): string
    {
        $pattern = "/
            <
                \s*
                ([[A-Z]\w+]*)                                            # First capturing group - component name
                (?<attributes>                                           # Named capturing group - attributes
                    (?:                                                  # Non-capturing group
                        \s+                                              # Matches zero or more whitespace character
                        (?:                                              # Non-capturing group
                            (?:                                          # Non-capturing group
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}  # Matches attributes between {{ and }}
                            )                                            # End of non-capturing group
                            |                                            # or
                            (?:\{\#.*\#\})                               # Matches Twig comments, non-capturing
                            |                                            # or
                            (?:                                          # Non-capturing group
                                [\w\-:.@]+                               # Matches list of one or more words and characters -:.@ literally
                                (                                        # Third capturing group
                                    =                                    # Matches the character =
                                    (?:                                  # Non-capturing group
                                        \\\"[^\\\"]*\\\"                 # Matches list of characters
                                        |                                # or
                                        \'[^\']*\'                       # Matches list of characters
                                        |                                # or
                                        [^\'\\\"=<>]+                    # Matches list of characters
                                    )                                    # End of non-capturing group
                                )?                                       # Matches group zero or one time
                            )                                            # End of non-capturing group
                        )                                                # End of non-capturing group
                    )*                                                   # End of non-capturing group
                    \s*                                                  # Matches whitespace character zero or more
                )                                                        # End of non-capturing group
            \/>                                                          # Matches string literally
        /x";

        return (string) preg_replace_callback(
            $pattern,
            function (array $matches) {
                $attributes = $this->getAttributesFromAttributeString($matches['attributes']);
                $name = $matches[1];

                return $this->componentStartString($name, $attributes) . '{% endembed %}';
            },
            $value
        );
    }

    private function valueParser(?string $value, string $attribute): string
    {
        if ($value === null) {
            return 'true';
        }

        // enable an argument that begins with a colon
        if (\str_starts_with($attribute, ':')) {
            return $this->stripQuotes($value);
        }

        // `"{{ value }} "` -> `{{ value }}`
        // `"{ value } "` -> `{ value }`
        // `"{{value}} "` -> `{{value}}`
        // `"{value} "` -> `{value}`
        $valueWithoutQuotes = trim($this->stripQuotes($value));

        // `{{ value }}` or `{{value}}`
        if (\str_starts_with($valueWithoutQuotes, '{{') && (mb_strpos($valueWithoutQuotes, '}}') === mb_strlen($valueWithoutQuotes) - 2)) {
            return trim(mb_substr($valueWithoutQuotes, 2, -2));
        }

        // `{ value }` or `{value}`
        if (\str_starts_with($valueWithoutQuotes, '{') && (mb_strpos($valueWithoutQuotes, '}') === mb_strlen($valueWithoutQuotes) - 1)) {
            return trim(mb_substr($valueWithoutQuotes, 1, -1));
        }

        return $value;
    }

    protected function getAttributesFromAttributeString(string $attributeString): string
    {
        $attributeString = $this->parseAttributeBag($attributeString);
        $attributeString = $this->pourOutCommentsFromAttributeBag($attributeString);

        $pattern = '/
            (?<attribute>[\w\-:.@]+)
            (
                =
                (?<value>
                    (
                        \"[^\"]+\"       # Capture all that is between "..." but not `"`
                        |                # or
                        \\\'[^\\\']+\\\' # Capture all that is between \'...\' but not `\'`
                        |                # or
                        \{[^\{]+\}       # Capture all that is between {...} but not `}`
                        |                # or
                        \{\{[^\}]+\}\}   # Capture all that is between {...} but not `}`
                        |                # or
                        [^>]+            # Capture any character but not `>`
                    )
                )
            )?
        /x';

        if (! preg_match_all($pattern, $attributeString, $matches, PREG_SET_ORDER)) {
            return '{}';
        }

        $attributes = [];

        foreach ($matches as $match) {
            $attribute = $match['attribute'];
            $value = $this->valueParser($match['value'] ?? null, $attribute);

            // enable an argument that begins with a colon
            if (\str_starts_with($attribute, ':')) {
                $attribute = str_replace(':', '', $attribute);
            }

            $attributes[$attribute] = $value;
        }

        $out = '{';
        foreach ($attributes as $key => $value) {
            $key = "'$key'";
            $out .= "$key: $value,";
        }

        return rtrim($out, ',') . '}';
    }

    /**
     * Strip any quotes from the given string.
     */
    public function stripQuotes(string $value): string
    {
        return \str_starts_with($value, '"') || \str_starts_with($value, '\'')
            ? mb_substr($value, 1, -1)
            : $value;
    }

    /**
     * Parse the attribute bag in a given attribute string into it's fully-qualified syntax.
     */
    protected function parseAttributeBag(string $attributeString): string
    {
        $pattern = "/
            (?:^|\s+)                                        # start of the string or whitespace between attributes
            \{\{\s*(\\\$attributes(?:[^}]+?(?<!\s))?)\s*\}\} # exact match of attributes variable being echoed
        /x";

        return (string) preg_replace($pattern, ' :attributes="$1"', $attributeString);
    }

    /**
     * Remove Twig comments from given attribute string
     */
    protected function pourOutCommentsFromAttributeBag(string $attributeString): string
    {
        $pattern = "/
            (                                               # First capturing group
                \{\#                                        # Match {# literally
                    (                                       # Second capturing group
                        (?!\#\}).+?                         # Use negative lookahead to match until the first occurrence of #}
                    )                                       # End of second capturing group
                \#\}                                        # Match #} literally
            )                                               # End of first capturing group
        /x";

        return (string) preg_replace($pattern, '', $attributeString);
    }
}
