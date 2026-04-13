<?php

declare(strict_types=1);

/**
 * @var array{
 *     initial: array-key,
 *     tokens: array{
 *         default: array<non-empty-string, non-empty-string>,
 *         ...
 *     },
 *     skip: list<non-empty-string>,
 *     grammar: array<array-key, \Phplrt\Parser\Grammar\RuleInterface>,
 *     reducers: array<array-key, callable(\Phplrt\Parser\Context, mixed):mixed>,
 *     transitions?: array<array-key, mixed>
 * }
 */
return [
    'initial' => 'Document',
    'tokens' => [
        'default' => [
            'T_WHITESPACE' => '[\\x20\\t]+',
            'T_HELP' => 'HELP',
            'T_TYPE' => 'TYPE',
            'T_UNIT' => 'UNIT',
            'T_EOF' => 'EOF',
            'T_COMMENT' => '^\\#(?![\\x20\\t]+(?:HELP|TYPE|UNIT|EOF))[\\x20\\t]+([^\\n\\\\]|\\\\[n"\\\\])+$',
            'T_FLOAT' => '[+-]?[0-9]*\\.[0-9]+([eE][+-]?[0-9]+)?|[+-]?[0-9]+[eE][+-]?[0-9]+',
            'T_INF' => '[+-](?i)inf(inity)?',
            'T_INT' => '[+-]?[0-9]+',
            'T_NAN' => '(?i)nan',
            'T_EQUAL' => '\\=',
            'T_HASH' => '\\#',
            'T_LBRACE' => '{',
            'T_RBRACE' => '}',
            'T_COMMA' => '\\,',
            'T_EOR' => '\\r',
            'T_EOL' => '\\n',
            'T_START_TIMESTAMP' => 'st@',
            'T_METRIC_TYPE' => '(summary|counter|gaugehistogram|gauge|histogram|stateset|info|unknown|untyped)',
            'T_METRIC_NAME' => '[a-zA-Z_:][a-zA-Z0-9_:]*',
            'T_QUOTED_STRING' => '\\"([^\\n"\\\\]|\\\\[n"\\\\])+\\"',
            'T_TEXT' => '([^\\n\\\\]|\\\\[n"\\\\])+',
        ],
    ],
    'skip' => [],
    'transitions' => [],
    'grammar' => [
        0 => new \Phplrt\Parser\Grammar\Repetition('MetricData', 0, INF),
        1 => new \Phplrt\Parser\Grammar\Optional('Eof'),
        2 => new \Phplrt\Parser\Grammar\Optional('Unit'),
        3 => new \Phplrt\Parser\Grammar\Concatenation(['Help', 'Type', 2]),
        4 => new \Phplrt\Parser\Grammar\Optional('Unit'),
        5 => new \Phplrt\Parser\Grammar\Optional('Help'),
        6 => new \Phplrt\Parser\Grammar\Concatenation(['Type', 4, 5]),
        7 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        8 => new \Phplrt\Parser\Grammar\Alternation([3, 6]),
        9 => new \Phplrt\Parser\Grammar\Repetition('Metric', 0, INF),
        10 => new \Phplrt\Parser\Grammar\Concatenation([7, 8, 9]),
        11 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        12 => new \Phplrt\Parser\Grammar\Repetition('Metric', 1, INF),
        13 => new \Phplrt\Parser\Grammar\Concatenation([11, 12]),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        15 => new \Phplrt\Parser\Grammar\Repetition(123, 1, INF),
        16 => new \Phplrt\Parser\Grammar\Repetition(14, 0, INF),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMENT', true),
        18 => new \Phplrt\Parser\Grammar\Optional(15),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        23 => new \Phplrt\Parser\Grammar\Concatenation([22, 'HelpDocstring']),
        24 => new \Phplrt\Parser\Grammar\Repetition(19, 0, INF),
        25 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_HELP', false),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        29 => new \Phplrt\Parser\Grammar\Alternation([20, 21]),
        30 => new \Phplrt\Parser\Grammar\Optional(23),
        31 => new \Phplrt\Parser\Grammar\Optional(15),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', true),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', true),
        37 => new \Phplrt\Parser\Grammar\Alternation([32, 33, 'MetricValue', 34, 35]),
        38 => new \Phplrt\Parser\Grammar\Repetition(36, 0, INF),
        39 => new \Phplrt\Parser\Grammar\Concatenation([37, 38]),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        41 => new \Phplrt\Parser\Grammar\Repetition(39, 0, INF),
        42 => new \Phplrt\Parser\Grammar\Optional(40),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        46 => new \Phplrt\Parser\Grammar\Repetition(43, 0, INF),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        51 => new \Phplrt\Parser\Grammar\Alternation([44, 45]),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        54 => new \Phplrt\Parser\Grammar\Optional(15),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        59 => new \Phplrt\Parser\Grammar\Concatenation([58, 'UnitUnit']),
        60 => new \Phplrt\Parser\Grammar\Repetition(55, 0, INF),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_UNIT', false),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        65 => new \Phplrt\Parser\Grammar\Alternation([56, 57]),
        66 => new \Phplrt\Parser\Grammar\Optional(59),
        67 => new \Phplrt\Parser\Grammar\Optional(15),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        69 => new \Phplrt\Parser\Grammar\Repetition(68, 0, INF),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        72 => new \Phplrt\Parser\Grammar\Lexeme('T_EOF', false),
        73 => new \Phplrt\Parser\Grammar\Optional(15),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        76 => new \Phplrt\Parser\Grammar\Concatenation([75, 'MetricTimestamp']),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        78 => new \Phplrt\Parser\Grammar\Concatenation([77, 'MetricStartTimestamp']),
        79 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        80 => new \Phplrt\Parser\Grammar\Concatenation([79, 'Exemplar']),
        81 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        82 => new \Phplrt\Parser\Grammar\Repetition(74, 0, INF),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        84 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        86 => new \Phplrt\Parser\Grammar\Optional(76),
        87 => new \Phplrt\Parser\Grammar\Optional(78),
        88 => new \Phplrt\Parser\Grammar\Optional(80),
        89 => new \Phplrt\Parser\Grammar\Optional(15),
        90 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        91 => new \Phplrt\Parser\Grammar\Concatenation([90, 'MetricTimestamp']),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        94 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        95 => new \Phplrt\Parser\Grammar\Optional(91),
        96 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_NAN', true),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        104 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', false),
        105 => new \Phplrt\Parser\Grammar\Alternation([102, 103]),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        107 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        108 => new \Phplrt\Parser\Grammar\Repetition(106, 0, INF),
        109 => new \Phplrt\Parser\Grammar\Concatenation([107, 108]),
        110 => new \Phplrt\Parser\Grammar\Optional(109),
        111 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 110]),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        113 => new \Phplrt\Parser\Grammar\Repetition(111, 0, INF),
        114 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        115 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        116 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        117 => new \Phplrt\Parser\Grammar\Alternation([115, 116]),
        118 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        119 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        120 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        121 => new \Phplrt\Parser\Grammar\Repetition(120, 0, INF),
        122 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        123 => new \Phplrt\Parser\Grammar\Concatenation([121, 122]),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([16, 17, 18]),
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        'Eof' => new \Phplrt\Parser\Grammar\Concatenation([69, 70, 71, 72, 73]),
        'Exemplar' => new \Phplrt\Parser\Grammar\Concatenation([92, 93, 'Labels', 94, 'MetricValue', 95]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([24, 25, 26, 27, 28, 29, 30, 31]),
        'HelpDocstring' => new \Phplrt\Parser\Grammar\Concatenation([41, 42]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([117, 118, 119]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([112, 113, 114]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([81, 82, 83, 84, 85, 'MetricValue', 86, 87, 88, 89]),
        'MetricData' => new \Phplrt\Parser\Grammar\Alternation([10, 13]),
        'MetricStartTimestamp' => new \Phplrt\Parser\Grammar\Concatenation([104, 105]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Alternation([100, 101]),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([96, 97, 98, 99]),
        'Schema' => new \Phplrt\Parser\Grammar\Concatenation([0, 1]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([46, 47, 48, 49, 50, 51, 52, 53, 54]),
        'Unit' => new \Phplrt\Parser\Grammar\Concatenation([60, 61, 62, 63, 64, 65, 66, 67]),
        'UnitUnit' => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
    ],
    'reducers' => [
        'Comment' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\CommentNode($children);
        },
        'Eof' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\EofNode($children);
        },
        'Exemplar' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\ExemplarNode($children);
        },
        'Help' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\HelpNode($children);
        },
        'HelpDocstring' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\HelpDocstringNode($children);
        },
        'Label' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelNode($children);
        },
        'Labels' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelsNode($children);
        },
        'Metric' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricNode($children);
        },
        'MetricData' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricDataNode($children);
        },
        'MetricStartTimestamp' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricStartTimestampNode($children);
        },
        'MetricTimestamp' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricTimestampNode($children);
        },
        'MetricValue' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricValueNode($children);
        },
        'Schema' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\SchemaNode($children);
        },
        'Type' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\TypeNode($children);
        },
        'Unit' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\UnitNode($children);
        },
        'UnitUnit' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\UnitUnitNode($children);
        },
    ],
];