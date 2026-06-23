import re

with open('resources/views/adminNew/index.blade.php', 'r') as f:
    content = f.read()

# 1. Update global filter JS to trigger jenjang filters
content = content.replace(
    "const kabId = $(this).val();\n        $('.filter-kabupaten').val(kabId).trigger('change');",
    "const kabId = $(this).val();\n        $('.filter-kabupaten').val(kabId).trigger('change');\n    });\n\n    $('#global-filter-jenjang').change(function() {\n        const jenId = $(this).val();\n        $('.filter-jenjang').val(jenId).trigger('change');"
)

# 2. Add Jenjang Dropdown right after each filter-tahun dropdown block
# Find all `<select id="filter-tahun*"` and their parent div closing tags
# We will use regex to find `<div class="col-md-3">...<select id="filter-tahun...</div>` and append the jenjang HTML.
pattern = re.compile(r'(<div class="col-md-3">\s*<label for="filter-tahun([^"]*)">.*?</select>\s*</div>)', re.DOTALL)

def replacer(match):
    original_block = match.group(1)
    suffix = match.group(2)
    jenjang_html = f"""
                                <div class="col-md-3">
                                    <label for="filter-jenjang{suffix}">Filter Jenjang:</label>
                                    <select id="filter-jenjang{suffix}" name="jenjang" class="select2 form-select filter-jenjang">
                                        <option value="all">All</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMK">SMK</option>
                                        <option value="SKh">SKh</option>
                                    </select>
                                </div>"""
    return original_block + jenjang_html

content = pattern.sub(replacer, content)

# 3. Update JS fetch calls to include jenjang.
# fetchChartData(month, year, kabupaten) -> fetchChartData(month, year, kabupaten, jenjang)
# find `function fetchChartData(month = 'all', year = 'all', kabupaten = 'all') {`
content = content.replace(
    "function fetchChartData(month = 'all', year = 'all', kabupaten = 'all') {",
    "function fetchChartData(month = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {"
)
content = content.replace(
    "`/admin/chart-data?bln=${month}&tahun=${filterYear}&kabupaten=${kabupaten}`",
    "`/admin/chart-data?bln=${month}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`"
)
content = content.replace(
    "fetchChartData('all', currentYear, 'all');",
    "fetchChartData('all', currentYear, 'all', 'all');"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-1').val();",
    "const kabupaten = $('#filter-kabupaten-1').val();\n                const jenjang = $('#filter-jenjang').val();"
)
content = content.replace(
    "$('#filter-bln, #filter-tahun, #filter-kabupaten-1').change(function() {",
    "$('#filter-bln, #filter-tahun, #filter-kabupaten-1, #filter-jenjang').change(function() {"
)
content = content.replace(
    "fetchChartData(month, year, kabupaten);",
    "fetchChartData(month, year, kabupaten, jenjang);"
)

# Do similar for chart 2, 3, 4, 5, 6
def patch_fetch(content, func_name, fetch_url, ids_to_listen, default_call):
    content = content.replace(
        f"function {func_name}(month = 'all', year = 'all', pengawas = 'all', kabupaten = 'all') {{",
        f"function {func_name}(month = 'all', year = 'all', pengawas = 'all', kabupaten = 'all', jenjang = 'all') {{"
    )
    content = content.replace(
        f"function {func_name}(pengawas = 'all', year = 'all', kabupaten = 'all') {{",
        f"function {func_name}(pengawas = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {{"
    )
    content = content.replace(
        f"function {func_name}(month = 'all', year = 'all', kabupaten = 'all') {{",
        f"function {func_name}(month = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {{"
    )

    # For fetch url: append jenjang
    # Some have pengawas, some don't. Just replace the backtick string.
    # regex to find fetch url: `.*?`
    url_pattern = re.compile(f"`({fetch_url}.*?)`")
    def url_replacer(match):
        url = match.group(1)
        if 'jenjang' not in url:
            return f"`{url}&jenjang=${{jenjang}}`"
        return f"`{url}`"
    content = url_pattern.sub(url_replacer, content)
    return content

content = patch_fetch(content, 'fetchChartData2', '/admin/chart-data2', '#filter-bln-last, #filter-tahun-last, #filter-pengawas, #filter-kabupaten-2', "fetchChartData2('all', currentYear2, 'all', 'all');")
content = patch_fetch(content, 'fetchChartData3', '/admin/chart-data-raport-pendidikan', '#filter-bln-raport, #filter-tahun-raport, #filter-pengawas3, #filter-kabupaten-raport', "fetchChartData3('all', currentYear3, 'all', 'all');")
content = patch_fetch(content, 'fetchChartData4', '/admin/chart-terkonfirmasi', '#filter-bln3, #filter-tahun3, #filter-kabupaten-konfirm', "fetchChartData4('all', currentYear4, 'all');")
content = patch_fetch(content, 'fetchSpiderWebData', '/admin/spider-web-data', '#filter-pengawas2, #filter-tahun-spider, #filter-kabupaten-spider', "fetchSpiderWebData('all', currentYear, 'all');")
content = patch_fetch(content, 'fetchPieChartData', '/admin/chartpie', '#filter-pengawas4, #filter-tahun-pie, #filter-kabupaten-pie', "fetchPieChartData('all', currentYear, 'all');")

# Now update the change listeners manually.
# For chart 2:
content = content.replace(
    "$('#filter-bln-last, #filter-tahun-last, #filter-pengawas, #filter-kabupaten-2').change(function() {",
    "$('#filter-bln-last, #filter-tahun-last, #filter-pengawas, #filter-kabupaten-2, #filter-jenjang-last').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-2').val();",
    "const kabupaten = $('#filter-kabupaten-2').val();\n                const jenjang = $('#filter-jenjang-last').val();"
)
content = content.replace(
    "fetchChartData2(month, year, pengawas, kabupaten);",
    "fetchChartData2(month, year, pengawas, kabupaten, jenjang);"
)
content = content.replace(
    "fetchChartData2('all', currentYear2, 'all', 'all');",
    "fetchChartData2('all', currentYear2, 'all', 'all', 'all');"
)


# chart 3
content = content.replace(
    "$('#filter-bln-raport, #filter-tahun-raport, #filter-pengawas3, #filter-kabupaten-raport').change(function() {",
    "$('#filter-bln-raport, #filter-tahun-raport, #filter-pengawas3, #filter-kabupaten-raport, #filter-jenjang-raport').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-raport').val();",
    "const kabupaten = $('#filter-kabupaten-raport').val();\n                const jenjang = $('#filter-jenjang-raport').val();"
)
content = content.replace(
    "fetchChartData3(month, year, pengawas, kabupaten);",
    "fetchChartData3(month, year, pengawas, kabupaten, jenjang);"
)
content = content.replace(
    "fetchChartData3('all', currentYear3, 'all', 'all');",
    "fetchChartData3('all', currentYear3, 'all', 'all', 'all');"
)

# chart 4
content = content.replace(
    "$('#filter-bln3, #filter-tahun3, #filter-kabupaten-konfirm').change(function() {",
    "$('#filter-bln3, #filter-tahun3, #filter-kabupaten-konfirm, #filter-jenjang3').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-konfirm').val();",
    "const kabupaten = $('#filter-kabupaten-konfirm').val();\n                const jenjang = $('#filter-jenjang3').val();"
)
content = content.replace(
    "fetchChartData4(month, year, kabupaten);",
    "fetchChartData4(month, year, kabupaten, jenjang);"
)
content = content.replace(
    "fetchChartData4('all', currentYear4, 'all');",
    "fetchChartData4('all', currentYear4, 'all', 'all');"
)


# chart 5 (spider)
content = content.replace(
    "$('#filter-pengawas2, #filter-tahun-spider, #filter-kabupaten-spider').change(function() {",
    "$('#filter-pengawas2, #filter-tahun-spider, #filter-kabupaten-spider, #filter-jenjang-spider').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-spider').val();",
    "const kabupaten = $('#filter-kabupaten-spider').val();\n                const jenjang = $('#filter-jenjang-spider').val();"
)
content = content.replace(
    "fetchSpiderWebData(pengawas, year, kabupaten);",
    "fetchSpiderWebData(pengawas, year, kabupaten, jenjang);"
)
content = content.replace(
    "fetchSpiderWebData('all', currentYear, 'all');",
    "fetchSpiderWebData('all', currentYear, 'all', 'all');"
)


# chart 6 (pie)
content = content.replace(
    "$('#filter-pengawas4, #filter-tahun-pie, #filter-kabupaten-pie').change(function() {",
    "$('#filter-pengawas4, #filter-tahun-pie, #filter-kabupaten-pie, #filter-jenjang-pie').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-pie').val();",
    "const kabupaten = $('#filter-kabupaten-pie').val();\n                const jenjang = $('#filter-jenjang-pie').val();"
)
content = content.replace(
    "fetchPieChartData(pengawas, year, kabupaten);",
    "fetchPieChartData(pengawas, year, kabupaten, jenjang);"
)
content = content.replace(
    "fetchPieChartData('all', currentYear, 'all');",
    "fetchPieChartData('all', currentYear, 'all', 'all');"
)

# And dynamic charts:
content = content.replace(
    "function fetchDynamicChart(aspekprogramId, chartId, chartType, label, pengawas = 'all', year = 'all', kabupaten = 'all') {",
    "function fetchDynamicChart(aspekprogramId, chartId, chartType, label, pengawas = 'all', year = 'all', kabupaten = 'all', jenjang = 'all') {"
)
content = content.replace(
    "`/admin/chart-data-aspekprogram/${aspekprogramId}?pengawas=${pengawas}&tahun=${filterYear}&kabupaten=${kabupaten}`",
    "`/admin/chart-data-aspekprogram/${aspekprogramId}?pengawas=${pengawas}&tahun=${filterYear}&kabupaten=${kabupaten}&jenjang=${jenjang}`"
)
content = content.replace(
    "$('#filter-pengawas5, #filter-tahun-bar, #filter-kabupaten-bar').change(function() {",
    "$('#filter-pengawas5, #filter-tahun-bar, #filter-kabupaten-bar, #filter-jenjang-bar').change(function() {"
)
content = content.replace(
    "const kabupaten = $('#filter-kabupaten-bar').val();",
    "const kabupaten = $('#filter-kabupaten-bar').val();\n        const jenjang = $('#filter-jenjang-bar').val();"
)
content = content.replace(
    "fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional', pengawas, year, kabupaten);",
    "fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional', pengawas, year, kabupaten, jenjang);"
)
content = content.replace(
    "fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi', pengawas, year, kabupaten);",
    "fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi', pengawas, year, kabupaten, jenjang);"
)
content = content.replace(
    "fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan', pengawas, year, kabupaten);",
    "fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan', pengawas, year, kabupaten, jenjang);"
)

content = content.replace(
    "fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional', 'all', currentYear, 'all');",
    "fetchDynamicChart(12, 'chartQ1', 'bar', 'Pengembangan Profesional', 'all', currentYear, 'all', 'all');"
)
content = content.replace(
    "fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi', 'all', currentYear, 'all');",
    "fetchDynamicChart(14, 'chartQ2', 'pie', 'Aspek Kompetensi', 'all', currentYear, 'all', 'all');"
)
content = content.replace(
    "fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan', 'all', currentYear, 'all');",
    "fetchDynamicChart(15, 'chartQ4', 'pie', 'Kebermanfaatan', 'all', currentYear, 'all', 'all');"
)


with open('resources/views/adminNew/index.blade.php', 'w') as f:
    f.write(content)
