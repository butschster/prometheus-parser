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
        15 => new \Phplrt\Parser\Grammar\Repetition(128, 1, INF),
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
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', true),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', true),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', true),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', true),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', true),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', true),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', true),
        42 => new \Phplrt\Parser\Grammar\Alternation([32, 33, 'MetricValue', 34, 35, 36, 37, 38, 39, 40]),
        43 => new \Phplrt\Parser\Grammar\Repetition(41, 0, INF),
        44 => new \Phplrt\Parser\Grammar\Concatenation([42, 43]),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        46 => new \Phplrt\Parser\Grammar\Repetition(44, 0, INF),
        47 => new \Phplrt\Parser\Grammar\Optional(45),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        51 => new \Phplrt\Parser\Grammar\Repetition(48, 0, INF),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        56 => new \Phplrt\Parser\Grammar\Alternation([49, 50]),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        59 => new \Phplrt\Parser\Grammar\Optional(15),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        64 => new \Phplrt\Parser\Grammar\Concatenation([63, 'UnitUnit']),
        65 => new \Phplrt\Parser\Grammar\Repetition(60, 0, INF),
        66 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_UNIT', false),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        70 => new \Phplrt\Parser\Grammar\Alternation([61, 62]),
        71 => new \Phplrt\Parser\Grammar\Optional(64),
        72 => new \Phplrt\Parser\Grammar\Optional(15),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        74 => new \Phplrt\Parser\Grammar\Repetition(73, 0, INF),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_EOF', false),
        78 => new \Phplrt\Parser\Grammar\Optional(15),
        79 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        81 => new \Phplrt\Parser\Grammar\Concatenation([80, 'MetricTimestamp']),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        83 => new \Phplrt\Parser\Grammar\Concatenation([82, 'MetricStartTimestamp']),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        85 => new \Phplrt\Parser\Grammar\Concatenation([84, 'Exemplar']),
        86 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        87 => new \Phplrt\Parser\Grammar\Repetition(79, 0, INF),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        89 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        90 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        91 => new \Phplrt\Parser\Grammar\Optional(81),
        92 => new \Phplrt\Parser\Grammar\Optional(83),
        93 => new \Phplrt\Parser\Grammar\Optional(85),
        94 => new \Phplrt\Parser\Grammar\Optional(15),
        95 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        96 => new \Phplrt\Parser\Grammar\Concatenation([95, 'MetricTimestamp']),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        100 => new \Phplrt\Parser\Grammar\Optional(96),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        104 => new \Phplrt\Parser\Grammar\Lexeme('T_NAN', true),
        105 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        107 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        108 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        109 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', false),
        110 => new \Phplrt\Parser\Grammar\Alternation([107, 108]),
        111 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        112 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        113 => new \Phplrt\Parser\Grammar\Repetition(111, 0, INF),
        114 => new \Phplrt\Parser\Grammar\Concatenation([112, 113]),
        115 => new \Phplrt\Parser\Grammar\Optional(114),
        116 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 115]),
        117 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        118 => new \Phplrt\Parser\Grammar\Repetition(116, 0, INF),
        119 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        120 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        121 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        122 => new \Phplrt\Parser\Grammar\Alternation([120, 121]),
        123 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        124 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        125 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        126 => new \Phplrt\Parser\Grammar\Repetition(125, 0, INF),
        127 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        128 => new \Phplrt\Parser\Grammar\Concatenation([126, 127]),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([16, 17, 18]),
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        'Eof' => new \Phplrt\Parser\Grammar\Concatenation([74, 75, 76, 77, 78]),
        'Exemplar' => new \Phplrt\Parser\Grammar\Concatenation([97, 98, 'Labels', 99, 'MetricValue', 100]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([24, 25, 26, 27, 28, 29, 30, 31]),
        'HelpDocstring' => new \Phplrt\Parser\Grammar\Concatenation([46, 47]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([122, 123, 124]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([117, 118, 119]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([86, 87, 88, 89, 90, 'MetricValue', 91, 92, 93, 94]),
        'MetricData' => new \Phplrt\Parser\Grammar\Alternation([10, 13]),
        'MetricStartTimestamp' => new \Phplrt\Parser\Grammar\Concatenation([109, 110]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Alternation([105, 106]),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([101, 102, 103, 104]),
        'Schema' => new \Phplrt\Parser\Grammar\Concatenation([0, 1]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([51, 52, 53, 54, 55, 56, 57, 58, 59]),
        'Unit' => new \Phplrt\Parser\Grammar\Concatenation([65, 66, 67, 68, 69, 70, 71, 72]),
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