# Prometheus metrics parser for PHP

[![PHP Version Require](https://poser.pugx.org/butschster/prometheus-parser/require/php)](https://packagist.org/packages/butschster/prometheus-parser)
[![Latest Stable Version](https://poser.pugx.org/butschster/prometheus-parser/v/stable)](https://packagist.org/packages/butschster/prometheus-parser)
[![phpunit](https://github.com/butschster/prometheus-parser/actions/workflows/phpunit.yml/badge.svg)](https://github.com/butschster/prometheus-parser/actions)
[![psalm](https://github.com/butschster/prometheus-parser/actions/workflows/psalm.yml/badge.svg)](https://github.com/butschster/prometheus-parser/actions)
[![Total Downloads](https://poser.pugx.org/butschster/prometheus-parser/downloads)](https://packagist.org/packages/butschster/prometheus-parser)

![Github cover Prometheus parser](https://user-images.githubusercontent.com/773481/199663705-3540ce54-086e-476e-bf91-cd607c98df9f.jpg)

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
# HELP http_requests_total The total number of HTTP requests.
# TYPE http_requests_total counter
http_requests_total{method="post",code="200"} 1027 1395066363000
http_requests_total{method="post",code="400"}    3 1395066363000

# Escaping in label values:
msdos_file_access_time_seconds{path="C:\\DIR\\FILE.TXT",error="Cannot find file:\n\"FILE.TXT\""} 1.458255915e9

# Minimalistic line:
metric_without_timestamp_and_labels 12.47

# A weird metric from before the epoch:
something_weird{problem="division by zero"} +Inf -3982045

# A histogram, which has a pretty complex representation in the text format:
# HELP http_request_duration_seconds A histogram of the request duration.
# TYPE http_request_duration_seconds histogram
http_request_duration_seconds_bucket{le="0.05"} 24054
http_request_duration_seconds_bucket{le="0.1"} 33444
http_request_duration_seconds_bucket{le="0.2"} 100392
http_request_duration_seconds_bucket{le="0.5"} 129389
http_request_duration_seconds_bucket{le="1"} 133988
http_request_duration_seconds_bucket{le="+Inf"} 144320
http_request_duration_seconds_sum 53423
http_request_duration_seconds_count 144320

# Finally a summary, which has a complex representation, too:
# HELP rpc_duration_seconds A summary of the RPC duration in seconds.
# TYPE rpc_duration_seconds summary
rpc_duration_seconds{quantile="0.01"} 3102
rpc_duration_seconds{quantile="0.05"} 3272
rpc_duration_seconds{quantile="0.5"} 4773
rpc_duration_seconds{quantile="0.9"} 9001
rpc_duration_seconds{quantile="0.99"} 76656
rpc_duration_seconds_sum 1.7560473e+07
rpc_duration_seconds_count 2693
    SCHEMA
);
```

### Schema data

```php
$metrics = $schema->getMetrics(); // array of Metric

$metrics['http_requests_total']->description; // The total number of HTTP requests.
$metrics['http_requests_total']->type; // counter
$metrics['http_requests_total']->name; // http_requests_total

foreach ($metrics['go_gc_duration_seconds'] as $metric) {
    $metric->name; // go_gc_duration_seconds
    $metric->value; // Value
    $metric->timestamp; // Timestamp
    $metric->lables; // Array of labels
}
```

# Enjoy!

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.

