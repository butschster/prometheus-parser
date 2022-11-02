<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Parsers;

final class HelpTest extends TestCase
{
    public function testParseHelp(): void
    {
        $this->assertAst(
            <<<SCHEMA
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0"} 3.332e-05
go_gc_duration_seconds_sum 0.000298737
process_start_time_seconds 1.66740899177e+09
rr_http_worker_state{pid="11484",state="ready"} 0
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
