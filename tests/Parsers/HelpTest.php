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
                <T_METRIC_NAME offset="7">http_requests_total</T_METRIC_NAME>
                <T_TEXT offset="27">The total number of HTTP requests.
</T_TEXT>
            </Help>
            <Type offset="62">
                <T_METRIC_NAME offset="69">http_requests_total</T_METRIC_NAME>
                <T_METRIC_TYPE offset="89">counter</T_METRIC_TYPE>
            </Type>
            <Metric offset="96">
                <T_METRIC_NAME offset="97">http_requests_total</T_METRIC_NAME>
                <Labels offset="116">
                    <Label offset="117">
                        <T_METRIC_NAME offset="117">method</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="124">"post"</T_QUOTED_STRING>
                    </Label>
                    <Label offset="131">
                        <T_METRIC_NAME offset="131">code</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="136">"200"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="143">
                    <T_INT offset="143">1027</T_INT>
                </MetricValue>
                <MetricTimestamp offset="148">
                    <T_INT offset="148">1395066363000</T_INT>
                </MetricTimestamp>
            </Metric>
            <Metric offset="161">
                <T_METRIC_NAME offset="162">http_requests_total</T_METRIC_NAME>
                <Labels offset="181">
                    <Label offset="182">
                        <T_METRIC_NAME offset="182">method</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="189">"post"</T_QUOTED_STRING>
                    </Label>
                    <Label offset="196">
                        <T_METRIC_NAME offset="196">code</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="201">"400"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="211">
                    <T_INT offset="211">3</T_INT>
                </MetricValue>
                <MetricTimestamp offset="213">
                    <T_INT offset="213">1395066363000</T_INT>
                </MetricTimestamp>
            </Metric>
            <Metric offset="226">
                <Comment offset="226">
                    <T_COMMENT offset="228"># Escaping in label values:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="256">msdos_file_access_time_seconds</T_METRIC_NAME>
                <Labels offset="286">
                    <Label offset="287">
                        <T_METRIC_NAME offset="287">path</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="292">"C:\DIR\FILE.TXT"</T_QUOTED_STRING>
                    </Label>
                    <Label offset="310">
                        <T_METRIC_NAME offset="310">error</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="316">"Cannot find file:
\"FILE.TXT\""</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="350">
                    <T_FLOAT offset="350">1.458255915e9</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="363">
                <Comment offset="363">
                    <T_COMMENT offset="365"># Minimalistic line:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="386">metric_without_timestamp_and_labels</T_METRIC_NAME>
                <MetricValue offset="422">
                    <T_FLOAT offset="422">12.47</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="427">
                <Comment offset="427">
                    <T_COMMENT offset="429"># A weird metric from before the epoch:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="469">something_weird</T_METRIC_NAME>
                <Labels offset="484">
                    <Label offset="485">
                        <T_METRIC_NAME offset="485">problem</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="493">"division by zero"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="513">
                    <T_INF offset="513">+Inf</T_INF>
                </MetricValue>
                <MetricTimestamp offset="518">
                    <T_INT offset="518">-3982045</T_INT>
                </MetricTimestamp>
            </Metric>
        </MetricData>
        <MetricData offset="526">
            <Comment offset="526">
                <T_COMMENT offset="528"># A histogram, which has a pretty complex representation in the text format:</T_COMMENT>
            </Comment>
            <Help offset="604">
                <T_METRIC_NAME offset="612">http_request_duration_seconds</T_METRIC_NAME>
                <T_TEXT offset="642">A histogram of the request duration.
</T_TEXT>
            </Help>
            <Type offset="679">
                <T_METRIC_NAME offset="686">http_request_duration_seconds</T_METRIC_NAME>
                <T_METRIC_TYPE offset="716">histogram</T_METRIC_TYPE>
            </Type>
            <Metric offset="725">
                <T_METRIC_NAME offset="726">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="762">
                    <Label offset="763">
                        <T_METRIC_NAME offset="763">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="766">"0.05"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="774">
                    <T_INT offset="774">24054</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="779">
                <T_METRIC_NAME offset="780">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="816">
                    <Label offset="817">
                        <T_METRIC_NAME offset="817">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="820">"0.1"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="827">
                    <T_INT offset="827">33444</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="832">
                <T_METRIC_NAME offset="833">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="869">
                    <Label offset="870">
                        <T_METRIC_NAME offset="870">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="873">"0.2"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="880">
                    <T_INT offset="880">100392</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="886">
                <T_METRIC_NAME offset="887">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="923">
                    <Label offset="924">
                        <T_METRIC_NAME offset="924">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="927">"0.5"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="934">
                    <T_INT offset="934">129389</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="940">
                <T_METRIC_NAME offset="941">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="977">
                    <Label offset="978">
                        <T_METRIC_NAME offset="978">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="981">"1"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="986">
                    <T_INT offset="986">133988</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="992">
                <T_METRIC_NAME offset="993">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="1029">
                    <Label offset="1030">
                        <T_METRIC_NAME offset="1030">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1033">"+Inf"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1041">
                    <T_INT offset="1041">144320</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1047">
                <T_METRIC_NAME offset="1048">http_request_duration_seconds_sum</T_METRIC_NAME>
                <MetricValue offset="1082">
                    <T_INT offset="1082">53423</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1087">
                <T_METRIC_NAME offset="1088">http_request_duration_seconds_count</T_METRIC_NAME>
                <MetricValue offset="1124">
                    <T_INT offset="1124">144320</T_INT>
                </MetricValue>
            </Metric>
        </MetricData>
        <MetricData offset="1130">
            <Comment offset="1130">
                <T_COMMENT offset="1132"># Finally a summary, which has a complex representation, too:</T_COMMENT>
            </Comment>
            <Help offset="1193">
                <T_METRIC_NAME offset="1201">rpc_duration_seconds</T_METRIC_NAME>
                <T_TEXT offset="1222">A summary of the RPC duration in seconds.
</T_TEXT>
            </Help>
            <Type offset="1264">
                <T_METRIC_NAME offset="1271">rpc_duration_seconds</T_METRIC_NAME>
                <T_METRIC_TYPE offset="1292">summary</T_METRIC_TYPE>
            </Type>
            <Metric offset="1299">
                <T_METRIC_NAME offset="1300">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1320">
                    <Label offset="1321">
                        <T_METRIC_NAME offset="1321">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1330">"0.01"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1338">
                    <T_INT offset="1338">3102</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1342">
                <T_METRIC_NAME offset="1343">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1363">
                    <Label offset="1364">
                        <T_METRIC_NAME offset="1364">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1373">"0.05"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1381">
                    <T_INT offset="1381">3272</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1385">
                <T_METRIC_NAME offset="1386">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1406">
                    <Label offset="1407">
                        <T_METRIC_NAME offset="1407">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1416">"0.5"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1423">
                    <T_INT offset="1423">4773</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1427">
                <T_METRIC_NAME offset="1428">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1448">
                    <Label offset="1449">
                        <T_METRIC_NAME offset="1449">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1458">"0.9"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1465">
                    <T_INT offset="1465">9001</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1469">
                <T_METRIC_NAME offset="1470">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1490">
                    <Label offset="1491">
                        <T_METRIC_NAME offset="1491">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1500">"0.99"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1508">
                    <T_INT offset="1508">76656</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1513">
                <T_METRIC_NAME offset="1514">rpc_duration_seconds_sum</T_METRIC_NAME>
                <MetricValue offset="1539">
                    <T_FLOAT offset="1539">1.7560473e+07</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="1552">
                <T_METRIC_NAME offset="1553">rpc_duration_seconds_count</T_METRIC_NAME>
                <MetricValue offset="1580">
                    <T_INT offset="1580">2693</T_INT>
                </MetricValue>
            </Metric>
        </MetricData>
    </Schema>
</Document>
AST
        );
    }
}
