<?php
return [
    'initial' => 'Document',
    'tokens' => [
        'default' => [
            'T_WHITESPACE' => '\\s+',
            'T_HELP' => 'HELP',
            'T_TYPE' => 'TYPE',
            'T_COMMENT' => '^\\#\\s[a-zA-Z0-9\\s\\.\\,\\!\\?\\:]+\\:',
            'T_FLOAT' => '[0-9]+\\.[0-9]+(e(\\+|\\-)?[0-9]+)?',
            'T_INF' => '(\\+|\\-)Inf',
            'T_INT' => '(\\-)?[0-9]+',
            'T_METRIC_TYPE' => '(summary|counter|gauge|histogram|untyped)',
            'T_METRIC_NAME' => '[a-z_]+',
            'T_COMMA' => '\\,',
            'T_TEXT' => '[a-zA-Z0-9\\s\\.\\,\\!\\?\\:]+',
            'T_QUOTED_STRING' => '\\"([a-zA-Z0-9\\s\\.\\,\\!\\?\\:\\\\\\"\\+]+)\\"',
            'T_EQUAL' => '\\=',
            'T_HASH' => '\\#',
            'T_LBRACE' => '{',
            'T_RBRACE' => '}',
            'T_DOT' => '\\.',
            'T_EOR' => '\\\\r',
            'T_EOL' => '\\\\n',
            'T_EOI' => '\\\\0',
        ],
    ],
    'skip' => [
        
    ],
    'transitions' => [
        
    ],
    'grammar' => [
        'Document' => new \Phplrt\Parser\Grammar\Concatenation(['Schema']),
        0 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        'Comment' => new \Phplrt\Parser\Grammar\Concatenation([3, 4, 5]),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([7, 8, 9, 10, 11, 12, 13, 14, 15]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([48, 49, 50]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([45, 46, 47]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([29, 30, 31, 32, 33, 'MetricValue', 34, 35]),
        'MetricData' => new \Phplrt\Parser\Grammar\Concatenation([0, 'Help', 'Type', 1]),
        'MetricTimestamp' => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([36, 37, 38]),
        'Schema' => new \Phplrt\Parser\Grammar\Repetition('MetricData', 0, INF),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([17, 18, 19, 20, 21, 22, 23, 24, 25]),
        1 => new \Phplrt\Parser\Grammar\Repetition('Metric', 0, INF),
        2 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        3 => new \Phplrt\Parser\Grammar\Repetition(2, 0, INF),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMENT', true),
        5 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        7 => new \Phplrt\Parser\Grammar\Repetition(6, 0, INF),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_HELP', false),
        11 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        15 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        17 => new \Phplrt\Parser\Grammar\Repetition(16, 0, INF),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        19 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        21 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        23 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        25 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        28 => new \Phplrt\Parser\Grammar\Concatenation([27, 'MetricTimestamp']),
        29 => new \Phplrt\Parser\Grammar\Repetition('Comment', 0, INF),
        30 => new \Phplrt\Parser\Grammar\Repetition(26, 0, INF),
        31 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        32 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        33 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        34 => new \Phplrt\Parser\Grammar\Optional(28),
        35 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_INF', true),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        41 => new \Phplrt\Parser\Grammar\Repetition(39, 0, INF),
        42 => new \Phplrt\Parser\Grammar\Concatenation([40, 41]),
        43 => new \Phplrt\Parser\Grammar\Optional(42),
        44 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 43]),
        45 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        46 => new \Phplrt\Parser\Grammar\Repetition(44, 0, INF),
        47 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        48 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        49 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        50 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        51 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        52 => new \Phplrt\Parser\Grammar\Repetition(51, 0, INF),
        53 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        'Eol' => new \Phplrt\Parser\Grammar\Concatenation([52, 53])
    ],
    'reducers' => [
        'Schema' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\SchemaNode($children);
        },
        'MetricData' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricDataNode($children);
        },
        'Comment' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\CommentNode($children);
        },
        'Help' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\HelpNode($children);
        },
        'Type' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\TypeNode($children);
        },
        'Metric' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricNode($children);
        },
        'MetricValue' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricValueNode($children);
        },
        'MetricTimestamp' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricTimestampNode($children);
        },
        'Labels' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelsNode($children);
        },
        'Label' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelNode($children);
        }
    ]
];