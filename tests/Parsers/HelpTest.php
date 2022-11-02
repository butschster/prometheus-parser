<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Parsers;

final class HelpTest extends TestCase
{
    public function testParseHelp(): void
    {
        $this->assertAst(
            <<<SCHEMA
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
            ,
            <<<AST
<Document offset="0">
    <Schema offset="0">
        <MetricData offset="0">
            <Help offset="0">
                <T_METRIC_NAME offset="7">go_gc_duration_seconds</T_METRIC_NAME>
                <T_TEXT offset="30">A summary of the pause duration of garbage collection cycles.
</T_TEXT>
            </Help>
            <Type offset="92">
                <T_METRIC_NAME offset="99">go_gc_duration_seconds</T_METRIC_NAME>
                <T_METRIC_TYPE offset="122">summary</T_METRIC_TYPE>
            </Type>
            <Metric offset="129">
                <T_METRIC_NAME offset="130">go_gc_duration_seconds</T_METRIC_NAME>
                <Labels offset="152">
                    <Label offset="153">
                        <T_METRIC_NAME offset="153">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="162">"0"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="167">
                    <T_FLOAT offset="167">3.332e-05</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="176">
                <T_METRIC_NAME offset="177">go_gc_duration_seconds_sum</T_METRIC_NAME>
                <MetricValue offset="204">
                    <T_FLOAT offset="204">0.000298737</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="215">
                <T_METRIC_NAME offset="216">process_start_time_seconds</T_METRIC_NAME>
                <MetricValue offset="243">
                    <T_FLOAT offset="243">1.66740899177e+09</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="260">
                <T_METRIC_NAME offset="261">rr_http_worker_state</T_METRIC_NAME>
                <Labels offset="281">
                    <Label offset="282">
                        <T_METRIC_NAME offset="282">pid</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="286">"11484"</T_QUOTED_STRING>
                    </Label>
                    <Label offset="294">
                        <T_METRIC_NAME offset="294">state</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="300">"ready"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="309">
                    <T_INT offset="309">0</T_INT>
                </MetricValue>
            </Metric>
        </MetricData>
    </Schema>
</Document>
AST
        );
    }
}
