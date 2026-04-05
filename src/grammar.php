<?php
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
            'T_METRIC_TYPE' => '(summary|counter|gauge|histogram|gaugehistogram|stateset|info|unknown|untyped)',
            'T_METRIC_NAME' => '[a-z_:][a-z0-9_:]*',
            'T_QUOTED_STRING' => '\\"([^\\n"\\\\]|\\\\[n"\\\\])+\\"',
            'T_TEXT' => '([^\\n\\\\]|\\\\[n"\\\\])+',
        ],
    ],
    'skip' => [
        
    ],
    'transitions' => [
        
    ],
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
        11 => new \Phplrt\Parser\Grammar\Repetition(92, 1, INF),
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
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', true),
        30 => new \Phplrt\Parser\Grammar\Alternation([26, 27, 'MetricValue', 28]),
        31 => new \Phplrt\Parser\Grammar\Repetition(29, 0, INF),
        32 => new \Phplrt\Parser\Grammar\Concatenation([30, 31]),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        34 => new \Phplrt\Parser\Grammar\Repetition(32, 0, INF),
        35 => new \Phplrt\Parser\Grammar\Optional(33),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        37 => new \Phplrt\Parser\Grammar\Repetition(36, 0, INF),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        41 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        43 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        45 => new \Phplrt\Parser\Grammar\Optional(11),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        48 => new \Phplrt\Parser\Grammar\Concatenation([47, 'UnitUnit']),
        49 => new \Phplrt\Parser\Grammar\Repetition(46, 0, INF),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_UNIT', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        54 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        55 => new \Phplrt\Parser\Grammar\Optional(48),
        56 => new \Phplrt\Parser\Grammar\Optional(11),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        58 => new \Phplrt\Parser\Grammar\Repetition(57, 0, INF),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        61 => new \Phplrt\Parser\Grammar\Lexeme('T_EOF', false),
        62 => new \Phplrt\Parser\Grammar\Optional(11),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        64 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        65 => new \Phplrt\Parser\Grammar\Concatenation([64, 'MetricTimestamp']),
        66 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        67 => new \Phplrt\Parser\Grammar\Repetition(63, 0, INF),
        68 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        69 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        71 => new \Phplrt\Parser\Grammar\Optional(65),
        72 => new \Phplrt\Parser\Grammar\Optional(11),
        73 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        74 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        75 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_NAN', true),
        77 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        79 => new \Phplrt\Parser\Grammar\Repetition(77, 0, INF),
        80 => new \Phplrt\Parser\Grammar\Concatenation([78, 79]),
        81 => new \Phplrt\Parser\Grammar\Optional(80),
        82 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 81]),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        84 => new \Phplrt\Parser\Grammar\Repetition(82, 0, INF),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        87 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        90 => new \Phplrt\Parser\Grammar\Repetition(89, 0, INF),
        91 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        92 => new \Phplrt\Parser\Grammar\Concatenation([90, 91]),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([12, 13, 14]),
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        'Eof' => new \Phplrt\Parser\Grammar\Concatenation([58, 59, 60, 61, 62]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([18, 19, 20, 21, 22, 23, 24, 25]),
        'HelpDocstring' => new \Phplrt\Parser\Grammar\Concatenation([34, 35]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([86, 87, 88]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([83, 84, 85]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([66, 67, 68, 69, 70, 'MetricValue', 71, 72]),
        'MetricData' => new \Phplrt\Parser\Grammar\Concatenation([7, 8, 9]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([73, 74, 75, 76]),
        'Schema' => new \Phplrt\Parser\Grammar\Concatenation([0, 1]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([37, 38, 39, 40, 41, 42, 43, 44, 45]),
        'Unit' => new \Phplrt\Parser\Grammar\Concatenation([49, 50, 51, 52, 53, 54, 55, 56]),
        'UnitUnit' => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
    ],
    'reducers' => [
        'Comment' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\CommentNode($children);
        },
        'Eof' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\EofNode($children);
        },
        'Help' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\HelpNode($children);
        },
        'HelpDocstring' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\HelpDocstringNode($children);
        },
        'Label' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelNode($children);
        },
        'Labels' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelsNode($children);
        },
        'Metric' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricNode($children);
        },
        'MetricData' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricDataNode($children);
        },
        'MetricTimestamp' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricTimestampNode($children);
        },
        'MetricValue' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricValueNode($children);
        },
        'Schema' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\SchemaNode($children);
        },
        'Type' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\TypeNode($children);
        },
        'Unit' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\UnitNode($children);
        },
        'UnitUnit' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\UnitUnitNode($children);
        },
    ],
];