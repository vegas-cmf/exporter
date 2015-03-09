<table style="width: 100%; border: 2px solid #000; border-collapse: collapse;">
    <tr>
        {% for header in exporter.getHeaders() %}
            <th style="border: 2px solid #000;">{{ header }}</th>
        {% endfor %}
    </tr>
    {% for item in exporter.getData() %}
        <tr>
            {% for value in item %}
                <td style="border: 2px solid #000;">{{ value }}</td>
            {% endfor %}
        </tr>
    {% endfor %}
</table>