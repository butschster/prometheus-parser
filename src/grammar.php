<?php
return [
    'initial' => 'Document',
    'tokens' => [
        'default' => [
            'T_WHITESPACE' => '\\s+',
            'T_HELP' => 'HELP',
            'T_TYPE' => 'TYPE',
            'T_FLOAT' => '[0-9]+\\.[0-9]+(e(\\-|\\+)[0-9]+)?',
            'T_INT' => '[0-9]+',
            'T_METRIC_TYPE' => '(summary|counter|gauge|histogram|untyped)',
            'T_METRIC_NAME' => '[a-z_]+',
            'T_COMMA' => '\\,',
            'T_TEXT' => '[a-zA-Z0-9\\s\\.\\,\\!\\?]+',
            'T_QUOTED_STRING' => '(\'{3}|["\']{1})([^\'"][\\s\\S]*?)\\1',
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
        0 => new \Phplrt\Parser\Grammar\Repetition('Metric', 0, INF),
        'Help' => new \Phplrt\Parser\Grammar\Concatenation([2, 3, 4, 5, 6, 7, 8, 9]),
        'Label' => new \Phplrt\Parser\Grammar\Concatenation([37, 38, 39]),
        'Labels' => new \Phplrt\Parser\Grammar\Concatenation([34, 35, 36]),
        'Metric' => new \Phplrt\Parser\Grammar\Concatenation([21, 22, 23, 24, 'MetricValue', 25]),
        'MetricData' => new \Phplrt\Parser\Grammar\Concatenation(['Help', 'Type', 0]),
        'MetricValue' => new \Phplrt\Parser\Grammar\Alternation([26, 27]),
        'Schema' => new \Phplrt\Parser\Grammar\Repetition('MetricData', 0, INF),
        'Type' => new \Phplrt\Parser\Grammar\Concatenation([11, 12, 13, 14, 15, 16, 17, 18, 19]),
        1 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        2 => new \Phplrt\Parser\Grammar\Repetition(1, 0, INF),
        3 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        4 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        5 => new \Phplrt\Parser\Grammar\Lexeme('T_HELP', false),
        6 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        7 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        8 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        9 => new \Phplrt\Parser\Grammar\Lexeme('T_TEXT', true),
        10 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        11 => new \Phplrt\Parser\Grammar\Repetition(10, 0, INF),
        12 => new \Phplrt\Parser\Grammar\Lexeme('T_HASH', false),
        13 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        14 => new \Phplrt\Parser\Grammar\Lexeme('T_TYPE', false),
        15 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        16 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        17 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        18 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_TYPE', true),
        19 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        20 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        21 => new \Phplrt\Parser\Grammar\Repetition(20, 0, INF),
        22 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        23 => new \Phplrt\Parser\Grammar\Optional('Labels'),
        24 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        25 => new \Phplrt\Parser\Grammar\Optional('Eol'),
        26 => new \Phplrt\Parser\Grammar\Lexeme('T_FLOAT', true),
        27 => new \Phplrt\Parser\Grammar\Lexeme('T_INT', true),
        28 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        29 => new \Phplrt\Parser\Grammar\Lexeme('T_COMMA', false),
        30 => new \Phplrt\Parser\Grammar\Repetition(28, 0, INF),
        31 => new \Phplrt\Parser\Grammar\Concatenation([29, 30]),
        32 => new \Phplrt\Parser\Grammar\Optional(31),
        33 => new \Phplrt\Parser\Grammar\Concatenation(['Label', 32]),
        34 => new \Phplrt\Parser\Grammar\Lexeme('T_LBRACE', false),
        35 => new \Phplrt\Parser\Grammar\Repetition(33, 0, INF),
        36 => new \Phplrt\Parser\Grammar\Lexeme('T_RBRACE', false),
        37 => new \Phplrt\Parser\Grammar\Lexeme('T_METRIC_NAME', true),
        38 => new \Phplrt\Parser\Grammar\Lexeme('T_EQUAL', false),
        39 => new \Phplrt\Parser\Grammar\Lexeme('T_QUOTED_STRING', true),
        40 => new \Phplrt\Parser\Grammar\Lexeme('T_WHITESPACE', false),
        41 => new \Phplrt\Parser\Grammar\Repetition(40, 0, INF),
        42 => new \Phplrt\Parser\Grammar\Lexeme('T_EOL', false),
        'Eol' => new \Phplrt\Parser\Grammar\Concatenation([41, 42])
    ],
    'reducers' => [
        'Schema' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\SchemaNode($children);
        },
        'MetricData' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\MetricDataNode($children);
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
        'Labels' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelsNode($children);
        },
        'Label' => function (\Phplrt\Parser\Context $ctx, $children) {
            return new \Butschster\Prometheus\Ast\LabelNode($children);
        }
    ]
];