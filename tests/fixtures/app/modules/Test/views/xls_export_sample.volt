<html>
<head>
    {# Charset is required in order to keep valid import inside Excel/Calc #}
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
</head>
<body>
    {% set export = exporter.getConfig() %}
    <table style="width: 100%; border: 2px solid #000; border-collapse: collapse;">
        <tr>
            {% for header in export.getHeaders() %}
                <th style="border: 2px solid #000;">{{ header }}</th>
            {% endfor %}
        </tr>
        {% for item in export.getData() %}
            <tr>
                {% for value in item %}
                    <td style="border: 2px solid #000;">{{ value }}</td>
                {% endfor %}
            </tr>
        {% endfor %}
    </table>
</body>
</html>