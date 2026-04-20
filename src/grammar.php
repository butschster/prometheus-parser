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
            'T_START_TIMESTAMP' => 'st@',
            'T_METRIC_TYPE' => '(summary|counter|gauge|histogram|gaugehistogram|stateset|info|unknown|untyped)',
            'T_METRIC_NAME' => '[a-zA-Z_:][a-zA-Z0-9_:]*',
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
        11 => new \Phplrt\Parser\Grammar\Repetition(107, 1, INF),
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
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', true),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', true),
        30 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', true),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', true),
        32 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', true),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', true),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        35 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', true),
        36 => new \Phplrt\Parser\Grammar\Alternation([26, 27, 'MetricValue', 28, 29, 30, 31, 32, 33, 34]),
        37 => new \Phplrt\Parser\Grammar\Repetition(35, 0, INF),
        38 => new \Phplrt\Parser\Grammar\Concatenation([36, 37]),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        40 => new \Phplrt\Parser\Grammar\Repetition(38, 0, INF),
        41 => new \Phplrt\Parser\Grammar\Optional(39),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        43 => new \Phplrt\Parser\Grammar\Repetition(42, 0, INF),
        44 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        46 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        51 => new \Phplrt\Parser\Grammar\Optional(11),
        52 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        54 => new \Phplrt\Parser\Grammar\Concatenation([53, 'UnitUnit']),
        55 => new \Phplrt\Parser\Grammar\Repetition(52, 0, INF),
        56 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        57 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        58 => new \Phplrt\Parser\Grammar\Lexeme('T_UNIT', false),
        59 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        60 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        61 => new \Phplrt\Parser\Grammar\Optional(54),
        62 => new \Phplrt\Parser\Grammar\Optional(11),
        63 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        64 => new \Phplrt\Parser\Grammar\Repetition(63, 0, INF),
        65 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        66 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        67 => new \Phplrt\Parser\Grammar\Lexeme('T_EOF', false),
        68 => new \Phplrt\Parser\Grammar\Optional(11),
        69 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        70 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        71 => new \Phplrt\Parser\Grammar\Concatenation([70, 'MetricTimestamp']),
        72 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        73 => new \Phplrt\Parser\Grammar\Concatenation([72, 'MetricStartTimestamp']),
        74 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        75 => new \Phplrt\Parser\Grammar\Repetition(69, 0, INF),
        76 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        77 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        78 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        79 => new \Phplrt\Parser\Grammar\Optional(71),
        80 => new \Phplrt\Parser\Grammar\Optional(73),
        81 => new \Phplrt\Parser\Grammar\Optional(11),
        82 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        83 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        84 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        85 => new \Phplrt\Parser\Grammar\Lexeme('T_NAN', true),
        86 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        87 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        88 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        89 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        90 => new \Phplrt\Parser\Grammar\Lexeme('T_START_TIMESTAMP', false),
        91 => new \Phplrt\Parser\Grammar\Alternation([88, 89]),
        92 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        93 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        94 => new \Phplrt\Parser\Grammar\Repetition(92, 0, INF),
        95 => new \Phplrt\Parser\Grammar\Concatenation([93, 94]),
        96 => new \Phplrt\Parser\Grammar\Optional(95),
        97 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 96]),
        98 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        99 => new \Phplrt\Parser\Grammar\Repetition(97, 0, INF),
        100 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        101 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        102 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        103 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        104 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        105 => new \Phplrt\Parser\Grammar\Repetition(104, 0, INF),
        106 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        107 => new \Phplrt\Parser\Grammar\Concatenation([105, 106]),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([12, 13, 14]),
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        'Eof' => new \Phplrt\Parser\Grammar\Concatenation([64, 65, 66, 67, 68]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([18, 19, 20, 21, 22, 23, 24, 25]),
        'HelpDocstring' => new \Phplrt\Parser\Grammar\Concatenation([40, 41]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([101, 102, 103]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([98, 99, 100]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([74, 75, 76, 77, 78, 'MetricValue', 79, 80, 81]),
        'MetricData' => new \Phplrt\Parser\Grammar\Concatenation([7, 8, 9]),
        'MetricStartTimestamp' => new \Phplrt\Parser\Grammar\Concatenation([90, 91]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Alternation([86, 87]),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([82, 83, 84, 85]),
        'Schema' => new \Phplrt\Parser\Grammar\Concatenation([0, 1]),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([43, 44, 45, 46, 47, 48, 49, 50, 51]),
        'Unit' => new \Phplrt\Parser\Grammar\Concatenation([55, 56, 57, 58, 59, 60, 61, 62]),
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
        'MetricStartTimestamp' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricStartTimestampNode($children);
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