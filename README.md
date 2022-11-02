# Prometheus metrics parser for PHP

[![PHP Version Require](https://poser.pugx.org/>butschster/prometheus-parser/require/php)](https://packagist.org/packages/>butschster/prometheus-parser)
[![Latest Stable Version](https://poser.pugx.org/>butschster/prometheus-parser/v/stable)](https://packagist.org/packages/>butschster/prometheus-parser)
[![phpunit](https://github.com/>butschster/prometheus-parser/actions/workflows/phpunit.yml/badge.svg)](https://github.com/>butschster/prometheus-parser/actions)
[![psalm](https://github.com/>butschster/prometheus-parser/actions/workflows/psalm.yml/badge.svg)](https://github.com/>butschster/prometheus-parser/actions)
[![Codecov](https://codecov.io/gh/>butschster/prometheus-parser/branch/master/graph/badge.svg)](https://codecov.io/gh/>butschster/prometheus-parser/)
[![Total Downloads](https://poser.pugx.org/>butschster/prometheus-parser/downloads)](https://packagist.org/packages/>butschster/prometheus-parser)

## Requirements

- PHP 8.1 and above

## Quick start

From the command line run

```shell
composer require butschster/prometheus-parser
```

That's it!


## Usage

```php
use Butschster\Prometheus\ParserFactory;

$parser = ParserFactory::create();

$schema = $parser->parse(<<<SCHEMA
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0"} 3.332e-05
go_gc_duration_seconds{quantile="0.25"} 3.332e-05
go_gc_duration_seconds{quantile="0.5"} 4.716e-05
go_gc_duration_seconds{quantile="0.75"} 0.000218257
go_gc_duration_seconds{quantile="1"} 0.000218257
go_gc_duration_seconds_sum 0.000298737
go_gc_duration_seconds_count 3
# HELP go_goroutines Number of goroutines that currently exist.
# TYPE go_goroutines gauge
go_goroutines 28
    SCHEMA
);
```

### Schema data

```php
$metrics = $schema->getMetrics(); // array of Metric

$metrics['go_gc_duration_seconds']->description; // A summary of the pause duration of garbage collection cycles.
$metrics['go_gc_duration_seconds']->type; // summary
$metrics['go_gc_duration_seconds']->name; // go_gc_duration_seconds

foreach ($metrics['go_gc_duration_seconds'] as $metric) {
    $metric->name; // go_gc_duration_seconds
    $metric->value; // Value
    $metric->lables; // Array of labels
}
```

# Enjoy!

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.

