# Prometheus metrics parser for PHP

[![PHP Version Require](https://poser.pugx.org/butschster/prometheus-parser/require/php)](https://packagist.org/packages/butschster/prometheus-parser)
[![Latest Stable Version](https://poser.pugx.org/butschster/prometheus-parser/v/stable)](https://packagist.org/packages/butschster/prometheus-parser)
[![phpunit](https://github.com/butschster/prometheus-parser/actions/workflows/phpunit.yml/badge.svg)](https://github.com/butschster/prometheus-parser/actions)
[![psalm](https://github.com/butschster/prometheus-parser/actions/workflows/psalm.yml/badge.svg)](https://github.com/butschster/prometheus-parser/actions)
[![Total Downloads](https://poser.pugx.org/butschster/prometheus-parser/downloads)](https://packagist.org/packages/butschster/prometheus-parser)

![Github cover Prometheus parser](https://user-images.githubusercontent.com/773481/199663705-3540ce54-086e-476e-bf91-cd607c98df9f.jpg)

Welcome to the Prometheus Metrics Parser! This package makes it easy to extract valuable information from metrics in the Prometheus text-based format and the **OpenMetrics 2.0** format. Whether you're looking to analyze your metrics data, integrate it into other systems, or just want a better way to visualize it, this package has you covered.

With just a few lines of code, you can easily extract valuable insights from your Prometheus metrics.

## Requirements

- PHP 8.1 and above

## Quick start

To install the package, run the following command from the root directory of your project:

```shell
composer require butschster/prometheus-parser
```

That's it!


## Usage

To get started, simply pass a string containing your Prometheus metric data to the `parse()` method. The method will return a schema object with metric objects, each of which contains the following properties:

```php
use Butschster\Prometheus\ParserFactory;

$parser = ParserFactory::create();

$schema = $parser->parse(<<<'SCHEMA'
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
$metrics = $schema->getMetrics(); // array of MetricDataNode

$metrics['http_requests_total']->description; // The total number of HTTP requests.
$metrics['http_requests_total']->type; // counter
$metrics['http_requests_total']->name; // http_requests_total
$metrics['http_requests_total']->unit; // null (if not set)
$metrics['http_requests_total']->eof; // true if # EOF was present (OpenMetrics)

foreach ($metrics['go_gc_duration_seconds'] as $metric) {
    $metric->name; // go_gc_duration_seconds
    $metric->value; // Value
    $metric->timestamp; // Timestamp
    $metric->startTimestamp; // Start timestamp (st@... syntax, OpenMetrics)
    $metric->labels; // Array of LabelNode objects
    $metric->exemplar; // ExemplarNode or null (OpenMetrics)
}
```

---

## OpenMetrics 2.0 features

### `# EOF` marker

OpenMetrics expositions must end with `# EOF`. The `SchemaNode::$eof` property is `true` when the marker is present and `null` for plain Prometheus format.

```php
$schema->eof; // true or null
```

### `# UNIT` directive

```php
# TYPE http_request_duration_seconds gauge
# UNIT http_request_duration_seconds seconds
```

```php
$metrics['http_request_duration_seconds']->unit; // "seconds"
```

### Exemplars

OpenMetrics allows an optional exemplar attached to each sample — typically used to carry a trace ID that corresponds to the measurement.

```
http_requests_total{code="200"} 1027 1395066363.000 # {trace_id="abc123"} 1.0 1395066363.000
```

```php
$metric->exemplar;             // ExemplarNode|null
$metric->exemplar->value;      // 1.0
$metric->exemplar->timestamp;  // 1395066363.000 or null
$metric->exemplar->labels;     // array of LabelNode
$metric->exemplar->labels[0]->name;  // "trace_id"
$metric->exemplar->labels[0]->value; // "abc123"
```

### Sub-metric grouping

Counter, histogram, summary and gaugehistogram families have well-known suffixes (`_total`, `_created`, `_sum`, `_count`, `_bucket`, `_gsum`, `_gcount`). `MetricDataNode` exposes convenience accessors:

```php
// counter
$metrics['requests']->getTotal();    // MetricNode for requests_total
$metrics['requests']->getCreated();  // MetricNode for requests_created (optional)

// histogram / summary
$metrics['http_request_duration']->getSum();     // MetricNode for _sum
$metrics['http_request_duration']->getCount();   // MetricNode for _count
$metrics['http_request_duration']->getBuckets(); // MetricNode[] for _bucket
$metrics['http_request_duration']->getCreated(); // MetricNode for _created

// gaugehistogram
$metrics['http_request_size']->getGSum();   // MetricNode for _gsum
$metrics['http_request_size']->getGCount(); // MetricNode for _gcount
```

### Unicode metric and label names

OpenMetrics 2.0 supports quoted identifiers for metric and label names containing characters not allowed in plain Prometheus format (dots, hyphens, etc.):

```
# TYPE "my.metric.name" gauge
# HELP "my.metric.name" A metric with dots in its name.
# UNIT "my.metric.name" seconds

# Quoted label names:
my_metric{"unicode.label"="value", regular_label="other"} 1
```

### Headerless (bare) metric blocks

Metrics without a `# TYPE` / `# HELP` header are valid — they are parsed with `type = "unknown"` and the family name derived from the first sample's name:

```
bare_metric{code="200"} 50
bare_metric{code="400"} 5
```

### Extended metric types

In addition to the standard `gauge`, `counter`, `summary` and `histogram` types, the following OpenMetrics-specific types are supported:

- `gaugehistogram`
- `stateset`
- `info`
- `unknown`
- `untyped`

### Start timestamps (`st@`)

```
foo_total 17.0 1520879607.789 st@1520430000.123
```

```php
$metric->startTimestamp; // 1520430000.123
```

---

## Validation layer (OpenMetrics strict mode)

Attach one or more validators to the parser to enforce OpenMetrics correctness rules. Validators are called after each successful parse and throw a `ValidationException` subclass on violation.

```php
use Butschster\Prometheus\ParserFactory;
use Butschster\Prometheus\Validation\InfoTypeValidator;
use Butschster\Prometheus\Validation\StateSetTypeValidator;
use Butschster\Prometheus\Validation\UnitSuffixValidator;

$parser = ParserFactory::create();
$parser->addValidator(new InfoTypeValidator());    // info families must end in _info and have value=1
$parser->addValidator(new StateSetTypeValidator()); // stateset samples must have value 0 or 1
$parser->addValidator(new UnitSuffixValidator());  // family name must end with _<unit>
```

### Available validators

| Validator | Rule |
|-----------|------|
| `InfoTypeValidator` | Family name must end with `_info`; all sample values must be `1`. |
| `StateSetTypeValidator` | Each sample must have a label named after the family; sample value must be `0` or `1`. |
| `UnitSuffixValidator` | When `# UNIT` is declared, the family name must end with `_<unit>`. |

### Implementing a custom validator

```php
use Butschster\Prometheus\Ast\SchemaNode;
use Butschster\Prometheus\Exceptions\ValidationException;
use Butschster\Prometheus\Validation\ValidatorInterface;

final class MyValidator implements ValidatorInterface
{
    public function validate(SchemaNode $schema): void
    {
        foreach ($schema->getMetrics() as $family) {
            // ... your rules ...
            // throw new ValidationException('...') on violation
        }
    }
}
```

---

## Structured exceptions

| Exception | When |
|-----------|------|
| `ParseException` | Base class for all parse failures. |
| `UnexpectedTokenException` | Parser encountered an unexpected token (extends `ParseException`). |
| `ValidationException` | Base class for all validation failures. |
| `InvalidMetricValueException` | A metric value violates a rule (extends `ValidationException`). |
| `InvalidLabelValueException` | A label value violates a rule (extends `ValidationException`). |
| `InvalidUnitSuffixException` | Family name does not end with declared unit (extends `ValidationException`). |

---

# Enjoy!

---

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.

