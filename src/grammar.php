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
            'T_METRIC_TYPE' => '(summary|counter|gauge|histogram|gaugehistogram|stateset|info|unknown|untyped)',
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
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        11 => new \Phplrt\Parser\Grammar\Repetition(102, 1, INF),
        12 => new \Phplrt\Parser\Grammar\Repetition(10, 0, INF),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMENT', true),
        14 => new \Phplrt\Parser\Grammar\Optional(11),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        17 => new \Phplrt\Parser\Grammar\Concatenation([16, 'HelpDocstring']),
        18 => new \Phplrt\Parser\Grammar\Repetition(15, 0, INF),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_HELP', false),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        24 => new \Phplrt\Parser\Grammar\Optional(17),
        25 => new \Phplrt\Parser\Grammar\Optional(11),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', true),
        31 => new \Phplrt\Parser\Grammar\Alternation([26, 27, 'MetricValue', 28, 29]),
        32 => new \Phplrt\Parser\Grammar\Repetition(30, 0, INF),
        33 => new \Phplrt\Parser\Grammar\Concatenation([31, 32]),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        35 => new \Phplrt\Parser\Grammar\Repetition(33, 0, INF),
        36 => new \Phplrt\Parser\Grammar\Optional(34),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        38 => new \Phplrt\Parser\Grammar\Repetition(37, 0, INF),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        46 => new \Phplrt\Parser\Grammar\Optional(11),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        49 => new \Phplrt\Parser\Grammar\Concatenation([48, 'UnitUnit']),
        50 => new \Phplrt\Parser\Grammar\Repetition(47, 0, INF),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_UNIT', false),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        55 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        56 => new \Phplrt\Parser\Grammar\Optional(49),
        57 => new \Phplrt\Parser\Grammar\Optional(11),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        59 => new \Phplrt\Parser\Grammar\Repetition(58, 0, INF),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        62 => new \Phplrt\Parser\Grammar\Lexeme('T_EOF', false),
        63 => new \Phplrt\Parser\Grammar\Optional(11),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        66 => new \Phplrt\Parser\Grammar\Concatenation([65, 'MetricTimestamp']),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        68 => new \Phplrt\Parser\Grammar\Concatenation([67, 'MetricStartTimestamp']),
        69 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        70 => new \Phplrt\Parser\Grammar\Repetition(64, 0, INF),
        71 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        72 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        74 => new \Phplrt\Parser\Grammar\Optional(66),
        75 => new \Phplrt\Parser\Grammar\Optional(68),
        76 => new \Phplrt\Parser\Grammar\Optional(11),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        79 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        80 => new \Phplrt\Parser\Grammar\Lexeme('T_NAN', true),
        81 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', false),
        86 => new \Phplrt\Parser\Grammar\Alternation([83, 84]),
        87 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        89 => new \Phplrt\Parser\Grammar\Repetition(87, 0, INF),
        90 => new \Phplrt\Parser\Grammar\Concatenation([88, 89]),
        91 => new \Phplrt\Parser\Grammar\Optional(90),
        92 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 91]),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        94 => new \Phplrt\Parser\Grammar\Repetition(92, 0, INF),
        95 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        96 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        97 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        99 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        100 => new \Phplrt\Parser\Grammar\Repetition(99, 0, INF),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        102 => new \Phplrt\Parser\Grammar\Concatenation([100, 101]),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([12, 13, 14]),
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        'Eof' => new \Phplrt\Parser\Grammar\Concatenation([59, 60, 61, 62, 63]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([18, 19, 20, 21, 22, 23, 24, 25]),
        'HelpDocstring' => new \Phplrt\Parser\Grammar\Concatenation([35, 36]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([96, 97, 98]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([93, 94, 95]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([69, 70, 71, 72, 73, 'MetricValue', 74, 75, 76]),
        'MetricData' => new \Phplrt\Parser\Grammar\Concatenation([7, 8, 9]),
        'MetricStartTimestamp' => new \Phplrt\Parser\Grammar\Concatenation([85, 86]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Alternation([81, 82]),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([77, 78, 79, 80]),
        'Schema' => new \Phplrt\Parser\Grammar\Concatenation([0, 1]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([38, 39, 40, 41, 42, 43, 44, 45, 46]),
        'Unit' => new \Phplrt\Parser\Grammar\Concatenation([50, 51, 52, 53, 54, 55, 56, 57]),
        'UnitUnit' => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
    ],
    'reducers' => [
        'Comment' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\CommentNode($children);
        },
        'Eof' => static function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\EofNode($children);
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