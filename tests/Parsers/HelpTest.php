<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Parsers;

final class HelpTest extends TestCase
{
    public function testParseHelp(): void
    {
        $this->assertAst(
            <<<'SCHEMA'
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
            <<<'AST'
<Document offset="0">
    <Schema offset="0">
        <MetricData offset="0">
            <Help offset="0">
                <T_METRIC_NAME offset="7">http_requests_total</T_METRIC_NAME>
                <HelpDocstring offset="27">
                    <T_TEXT offset="27">The total number of HTTP requests.</T_TEXT>
                </HelpDocstring>
            </Help>
            <Type offset="62">
                <T_METRIC_NAME offset="69">http_requests_total</T_METRIC_NAME>
                <T_METRIC_TYPE offset="89">counter</T_METRIC_TYPE>
            </Type>
            <Metric offset="97">
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
            <Metric offset="162">
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
            <Metric offset="228">
                <Comment offset="228">
                    <T_COMMENT offset="228"># Escaping in label values:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="256">msdos_file_access_time_seconds</T_METRIC_NAME>
                <Labels offset="286">
                    <Label offset="287">
                        <T_METRIC_NAME offset="287">path</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="292">"C:\\DIR\\FILE.TXT"</T_QUOTED_STRING>
                    </Label>
                    <Label offset="312">
                        <T_METRIC_NAME offset="312">error</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="318">"Cannot find file:\n\"FILE.TXT\""</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="353">
                    <T_FLOAT offset="353">1.458255915e9</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="368">
                <Comment offset="368">
                    <T_COMMENT offset="368"># Minimalistic line:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="389">metric_without_timestamp_and_labels</T_METRIC_NAME>
                <MetricValue offset="425">
                    <T_FLOAT offset="425">12.47</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="432">
                <Comment offset="432">
                    <T_COMMENT offset="432"># A weird metric from before the epoch:</T_COMMENT>
                </Comment>
                <T_METRIC_NAME offset="472">something_weird</T_METRIC_NAME>
                <Labels offset="487">
                    <Label offset="488">
                        <T_METRIC_NAME offset="488">problem</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="496">"division by zero"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="516">
                    <T_INF offset="516">+Inf</T_INF>
                </MetricValue>
                <MetricTimestamp offset="521">
                    <T_INT offset="521">-3982045</T_INT>
                </MetricTimestamp>
            </Metric>
        </MetricData>
        <MetricData offset="531">
            <Comment offset="531">
                <T_COMMENT offset="531"># A histogram, which has a pretty complex representation in the text format:</T_COMMENT>
            </Comment>
            <Help offset="608">
                <T_METRIC_NAME offset="615">http_request_duration_seconds</T_METRIC_NAME>
                <HelpDocstring offset="645">
                    <T_TEXT offset="645">A histogram of the request duration.</T_TEXT>
                </HelpDocstring>
            </Help>
            <Type offset="682">
                <T_METRIC_NAME offset="689">http_request_duration_seconds</T_METRIC_NAME>
                <T_METRIC_TYPE offset="719">histogram</T_METRIC_TYPE>
            </Type>
            <Metric offset="729">
                <T_METRIC_NAME offset="729">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="765">
                    <Label offset="766">
                        <T_METRIC_NAME offset="766">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="769">"0.05"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="777">
                    <T_INT offset="777">24054</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="783">
                <T_METRIC_NAME offset="783">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="819">
                    <Label offset="820">
                        <T_METRIC_NAME offset="820">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="823">"0.1"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="830">
                    <T_INT offset="830">33444</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="836">
                <T_METRIC_NAME offset="836">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="872">
                    <Label offset="873">
                        <T_METRIC_NAME offset="873">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="876">"0.2"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="883">
                    <T_INT offset="883">100392</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="890">
                <T_METRIC_NAME offset="890">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="926">
                    <Label offset="927">
                        <T_METRIC_NAME offset="927">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="930">"0.5"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="937">
                    <T_INT offset="937">129389</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="944">
                <T_METRIC_NAME offset="944">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="980">
                    <Label offset="981">
                        <T_METRIC_NAME offset="981">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="984">"1"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="989">
                    <T_INT offset="989">133988</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="996">
                <T_METRIC_NAME offset="996">http_request_duration_seconds_bucket</T_METRIC_NAME>
                <Labels offset="1032">
                    <Label offset="1033">
                        <T_METRIC_NAME offset="1033">le</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1036">"+Inf"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1044">
                    <T_INT offset="1044">144320</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1051">
                <T_METRIC_NAME offset="1051">http_request_duration_seconds_sum</T_METRIC_NAME>
                <MetricValue offset="1085">
                    <T_INT offset="1085">53423</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1091">
                <T_METRIC_NAME offset="1091">http_request_duration_seconds_count</T_METRIC_NAME>
                <MetricValue offset="1127">
                    <T_INT offset="1127">144320</T_INT>
                </MetricValue>
            </Metric>
        </MetricData>
        <MetricData offset="1135">
            <Comment offset="1135">
                <T_COMMENT offset="1135"># Finally a summary, which has a complex representation, too:</T_COMMENT>
            </Comment>
            <Help offset="1197">
                <T_METRIC_NAME offset="1204">rpc_duration_seconds</T_METRIC_NAME>
                <HelpDocstring offset="1225">
                    <T_TEXT offset="1225">A summary of the RPC duration in seconds.</T_TEXT>
                </HelpDocstring>
            </Help>
            <Type offset="1267">
                <T_METRIC_NAME offset="1274">rpc_duration_seconds</T_METRIC_NAME>
                <T_METRIC_TYPE offset="1295">summary</T_METRIC_TYPE>
            </Type>
            <Metric offset="1303">
                <T_METRIC_NAME offset="1303">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1323">
                    <Label offset="1324">
                        <T_METRIC_NAME offset="1324">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1333">"0.01"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1341">
                    <T_INT offset="1341">3102</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1346">
                <T_METRIC_NAME offset="1346">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1366">
                    <Label offset="1367">
                        <T_METRIC_NAME offset="1367">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1376">"0.05"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1384">
                    <T_INT offset="1384">3272</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1389">
                <T_METRIC_NAME offset="1389">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1409">
                    <Label offset="1410">
                        <T_METRIC_NAME offset="1410">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1419">"0.5"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1426">
                    <T_INT offset="1426">4773</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1431">
                <T_METRIC_NAME offset="1431">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1451">
                    <Label offset="1452">
                        <T_METRIC_NAME offset="1452">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1461">"0.9"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1468">
                    <T_INT offset="1468">9001</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1473">
                <T_METRIC_NAME offset="1473">rpc_duration_seconds</T_METRIC_NAME>
                <Labels offset="1493">
                    <Label offset="1494">
                        <T_METRIC_NAME offset="1494">quantile</T_METRIC_NAME>
                        <T_QUOTED_STRING offset="1503">"0.99"</T_QUOTED_STRING>
                    </Label>
                </Labels>
                <MetricValue offset="1511">
                    <T_INT offset="1511">76656</T_INT>
                </MetricValue>
            </Metric>
            <Metric offset="1517">
                <T_METRIC_NAME offset="1517">rpc_duration_seconds_sum</T_METRIC_NAME>
                <MetricValue offset="1542">
                    <T_FLOAT offset="1542">1.7560473e+07</T_FLOAT>
                </MetricValue>
            </Metric>
            <Metric offset="1556">
                <T_METRIC_NAME offset="1556">rpc_duration_seconds_count</T_METRIC_NAME>
                <MetricValue offset="1583">
                    <T_INT offset="1583">2693</T_INT>
                </MetricValue>
            </Metric>
        </MetricData>
    </Schema>
</Document>
AST
        );
    }
}
